<?php
namespace App\Http\Resources\Dashboard;

use App\Models\User;
use App\Models\Video;
use App\Models\Course;
use App\Models\Comment;
use Illuminate\Http\Resources\Json\JsonResource;
use Musonza\Chat\Models\Message;

class ReportResource extends JsonResource
{
    public function toArray($request)
    {
        $type = $this->model?->slug;

        switch ($this->reportable ? get_class($this->reportable) : '') {
            case Video::class:
                $model = [
                    'id' =>  $this->reportable?->id,
                    'url' => $this->reportable?->video,
                    'cover' => $this->reportable?->cover,
                    'reported' => [
                        'id' => $this->reportable?->user_id,
                        'username' => $this->reportable?->user?->username,
                        'email' => $this->reportable?->user?->email,
                    ],
                    'deleted_at' => $this->reportable?->deleted_at,
                ];
                break;

            case Course::class:
                $model = [
                    'id' =>  $this->reportable?->id,
                    'video_id' =>  $this->reportable?->video?->id,
                    'url' => $this->reportable?->video?->video,
                    'cover' => $this->reportable?->video?->cover,
                    'reported' => [
                        'id' => $this->reportable?->video?->user_id,
                        'username' => $this->reportable?->video?->user?->username,
                        'email' => $this->reportable?->video?->user?->email,
                    ],
                    'deleted_at' => $this->reportable?->video?->deleted_at,
                ];
                break;

            case Comment::class:
                $model = [
                    'id' =>  $this->reportable?->id,
                    'comment' => $this->reportable?->comment,
                    'reported' => [
                        'id' => $this->reportable?->user_id,
                        'username' => $this->reportable?->user?->username,
                        'email' => $this->reportable?->user?->email,
                    ],
                    'deleted_at' => $this->reportable?->deleted_at,
                ];
                break;

            case User::class:
                $model = [
                    'id' =>  $this->reportable?->id,
                    'reported' => [
                        'id' => $this->reportable?->id,
                        'username' => $this->reportable?->username,
                        'email' => $this->reportable?->email,
                    ],
                    'deleted_at' => $this->reportable?->deleted_at,
                ];
                break;

            case Message::class:
                $user = $this->reportable?->participation->messageable;
                $model = [
                    'id' =>  $this->reportable?->id,
                    'message' => $this->reportable?->body,
                    'reported' => [
                        'id' => $user?->id,
                        'username' => $user?->username,
                        'email' => $user?->email,
                    ],
                    'deleted_at' => $this->reportable?->deleted_at,
                ];
                break;

            default:
                $model = [];
                break;
        }

        return [
            'id' => $this->id,
            'type' => $type,
            'note' => $this->note,
            'user' => [
                'id' => $this->user_id,
                'username' => $this->user?->username,
                'email' => $this->user?->email,
            ],
            'model' => $model,
            'read_at' => $this->read_at,
        ];
    }
}
