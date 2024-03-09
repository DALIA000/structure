<?php

namespace App\Services;

use Intervention\Image\Facades\Image;
use Ramsey\Uuid\Uuid;
use Storage;

class PPGenerator
{
  public static function text(string $text, string $dir)
  {
    $fileName = Uuid::uuid4()->toString().'.svg';

    $url = 'https://ui-avatars.com/api/?background=random&name='.$text;
    $fileName = $dir . '/' . $fileName;

    $html = file_get_contents($url);

    preg_match(`/<img.*?>/`, $html, $matches);
    echo $matches;

    Storage::put($fileName, file_get_contents($url));

    return 'files/' . $fileName;
  }
}
