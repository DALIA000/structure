<?php

namespace App\Services;

use App\Models\Sticker;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Ramsey\Uuid\Uuid;
use FFMpeg;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;

class FileUploader
{
    public static function getduration($path)
    {
        $audioFilePath = public_path($path);
        $getID3 = new \getID3();
        $fileInfo = $getID3->analyze($audioFilePath);

        if (isset($fileInfo['playtime_seconds'])) {
            $durationInSeconds = $fileInfo['playtime_seconds'];
            $durationFormatted = gmdate('i:s', $durationInSeconds);
            return $durationFormatted;
        } else {
            return false;

        }
    }
    public static function uploadSingle(UploadedFile $file, string $dir)
    {
        $fileName = Uuid::uuid4()->toString();
        $fileName = $fileName . '.' . $file->getClientOriginalExtension();
        $file->storeAs($dir, $fileName);

        $document = 'files/' . $dir . '/' . $fileName;

        if ($dir === 'covers') {
            $path = $document;
            $webp = Image::make(public_path($path));
            $webp->save($path, 50, 'webp');
        }

        if ($dir == 'videos') {
            FFMpeg::fromDisk('public')
                    ->open($dir . '/' . $fileName)
                    ->export()
                    ->toDisk('public')
                    // ->inFormat(new \FFMpeg\Format\Video\X264())
                    ->save($dir . '/converted/' . $fileName);

            unlink(public_path($document));
            $document = 'files/' . $dir . '/converted/' . $fileName;
        }

        return [
            'document' => $document,
            'document_name' => $file->getClientOriginalName(),
            'document_mimetype' => $file->getClientMimeType(),
            // 'extension' => $file->getClientOriginalExtension(),
            'size' => $file->getSize(),
        ];
    }

    public static function uploadMedia(UploadedFile $file, $sound=null, $video_is_muted=false, $text=null, $sticker=null)
    {
        $fileName = Uuid::uuid4()->toString() . '.' . $file->getClientOriginalExtension();

        $format = new \FFMpeg\Format\Video\X264();
        $duration = FileUploader::getduration($file);
        $initial_parameters = [];

        $file = FFMpeg::fromDisk('public')
                ->open($file)
                ->addFilter([
                    '-vf', "scale=720:1280:force_original_aspect_ratio=decrease,pad=720:1280:-1:-1:color=black", 
                ]);

        if ($sound) {
            // add audio
            array_push($initial_parameters, 
                '-stream_loop', '-1',
                '-i', $sound,
            );

            // keep video sound
            if (!$video_is_muted) {
                array_push($initial_parameters,
                    '-filter_complex', '[0:a][1:a] amix=inputs=2:duration=1',
                );
            }
        }

        if ($text) {
            $file = FileUploader::addText($file, $text);
        }

        if ($sticker) {
            $file = FileUploader::addSticker($file, $sticker);
        }

        $format = $format/* ->setKiloBitrate(2000) */->setInitialParameters($initial_parameters);

        $file
                ->export()
                ->toDisk('public')
                ->inFormat($format)
                ->save('converted/' . $fileName);

        $path = Storage::disk('public')->path('converted/' . $fileName);
        $media = File::create()->addMedia($path)->usingFileName($fileName)/* ->preservingOriginal() */->toMediaCollection('media');
        // unlink($path);

        return $media;
    }

    public static function convertImageToVideo(UploadedFile $file, $sound=null, $text=null, $sticker=null)
    {
        $fileName = Uuid::uuid4()->toString() . '.mp4';

        $format = new \FFMpeg\Format\Video\X264();
        $initial_parameters = [];

        $file = FFMpeg::fromDisk('public')
            ->open($file)
            ->addFilter([
                '-vf', "scale=720:1280:force_original_aspect_ratio=decrease,pad=720:1280:-1:-1:color=black", 
            ]);
            
        if ($sound) {
            // add sound with duration
            array_push($initial_parameters, 
                '-i', $sound,
                '-t', 15,
                '-loop', '1',
            );
        } else {
            // give image duration
            array_push($initial_parameters, 
                '-loop', '1',
                '-t', 15,
            );
        }

        
        if ($text) {
           $file = FileUploader::addText($file, $text);
        }

        if ($sticker) {
            $file = FileUploader::addSticker($file, $sticker);
        }

        $format = $format/* ->setKiloBitrate(2000) */->setInitialParameters($initial_parameters);

        $file
                ->export()
                ->toDisk('public')
                ->inFormat($format)
                ->save('converted/' . $fileName);

        $path = Storage::disk('public')->path('converted/' . $fileName);
        $media = File::create()->addMedia($path)->usingFileName($fileName)/* ->preservingOriginal() */->toMediaCollection('media');
        // unlink($path);

        return $media;
    }

    public static function bulkUpload(array $files, $dir)
    {
        $fileNames = [];
        foreach ($files as $file) {
            $fileName = self::uploadSingle($file, $dir);
            array_push($fileNames, $fileName);
        }

        return $fileNames;
    }

    private static function addText($file, $text) 
    {
        $str = $text['str'];
        $size = array_key_exists('size', $text) ? $text['size'] : 10;
        $color = array_key_exists('color', $text) ? $text['color'] : 'white';
        $x = array_key_exists('x', $text) ? $text['x'] : 0;
        $y = array_key_exists('y', $text) ? $text['y'] : 0;
        $font_path = public_path('font.ttf');

        $file
            ->addFilter([
                '-vf', "drawtext=text='$str':fontfile=$font_path:fontsize=$size:fontcolor=$color:x=$x:y=$y", 
            ]);

        return $file;
    }

    private static function addSticker($file, $sticker) 
    {
        $file
            ->addWatermark(function (WatermarkFactory $watermark) use ($sticker) {
                $id = $sticker['id'];
                $width = array_key_exists('width', $sticker) ? $sticker['width'] : 100;
                $x = array_key_exists('x', $sticker) ? $sticker['x'] : 0;
                $y = array_key_exists('y', $sticker) ? $sticker['y'] : 0;

                $sticker = Sticker::find($id);
                $sticker_path = preg_replace('/files\//', '', $sticker->sticker);

                $watermark->fromDisk('public')
                    ->open($sticker_path)
                    ->left($x)
                    ->top($y)
                    ->width($width);
            });

        return $file;
    }

}
