<?php

namespace App\Http\Repositories\Video;

use App\Events\DeleteReportable;
use App\Events\UserTaged;
use App\Events\VideoCreated;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Repositories\{
    Base\BaseRepository,
    User\UserInterface,
};
use App\Models\{
    Video,
    AcademyPlayer,
    User,
    Admin,
    Report,
    Sound,
    Club,
    Player,
    Trainer,
};
use App\Services\{
    LoggedinUser,
    FileUploader,
};
use App\Http\Repositories\UserType\UserTypeInterface;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class VideoRepository extends BaseRepository implements VideoInterface
{
    public $loggedinUser;

    private $userType;

    public function __construct(Video $model, public User $user, public Sound $sound, public UserInterface $UserI, UserTypeInterface $userType, private Media $media)
    {
        $this->model = $model;
        $this->userType = $userType;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('search') && $request->search !== null) {
                $query->where('description', 'like', "%{$request->search}%");
                    // ->where('description', 'not like', "%#{$request->search}%")
                    // ->where('description', 'not like', "%@{$request->search}%");
            }

            if ($request->exists('id') && $request->id !== null) {
                $query->where('id', $request->id);
            }

            if ($request->exists('user_id') && $request->user_id !== null) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->exists('username') && $request->username !== null) {
                $query->whereHas('user', function ($query) use ($request) {
                    $query->where('username', $request->username);
                });
            }

            if ($request->exists('tagged_user') && $request->tagged_user !== null) {
                $query->whereHas('tagged_users', function ($query) use ($request) {
                    $query->where('username', $request->tagged_user);
                });
            }

            if ($request->exists('followable_id') && $request->followable_id !== null) {
                $query->where('followable_id', $request->followable_id);
            }

            if ($request->exists('followable_type') && $request->followable_type !== null) {
                $query->where('followable_type', $request->followable_type);
            }

            if ($request->exists('status_id') && $request->status_id !== null) {
                $query->where('status_id', $request->status_id);
            }

            switch ($request->type) {
                case '1':
                    $query->where(function ($query) use ($request) {
                        $query->doesntHave('course');
                    });
                    break;

                case '2':
                    $query->where(function ($query) use ($request) {
                        $query->where(function ($query) use ($request) {
                            $query->whereHas('course', function ($query) use ($request) {
                                $query->where('status_id', 1);
                            });
                        })->orWhere(function ($query) use ($request) {
                            $query->where('user_id', $this->loggedinUser?->id)->whereHas('course');
                        });
                    });
                    break;

                default:
                    $query->where(function ($query) use ($request) {
                        $query->where(function ($query) use ($request) {
                            $query->whereHas('course', function ($query) use ($request) {
                                $query->where('status_id', 1);
                            });
                        })->orWhere(function ($query) use ($request) {
                            $query->where('user_id', $this->loggedinUser?->id)->whereHas('course');
                        })->orDoesntHave('course');
                    });
                    break;
            };

            $query->where(function ($query) use ($request) {
                if ($this->loggedinUser && $this->loggedinUser instanceof User) {

                    if($request->followings){
                        // followings
                        $query->whereIn('user_id', $this->loggedinUser?->followings?->pluck('followable_id')?->merge($this->loggedinUser?->id)->unique()->toArray());
                    } else {
                        // public accounts only
                        $query->whereNot(function ($query) use ($request) {
                            $query->whereHas('user.preferences', function ($query) use ($request) {
                                $query->where('slug', 'account')
                                    ->where('value', 0);
                            });
                        });

                        // or private accounts who I follow
                        $query->orWhere(function ($query) use ($request) {
                            $query->whereHas('user.preferences', function ($query) use ($request) {
                                $query->where('slug', 'account')
                                    ->where('value', 0);
                            })->whereIn('user_id', $this->loggedinUser?->followings?->pluck('followable_id')?->merge($this->loggedinUser?->id)->unique()->toArray());
                        });
                    }
                }

                if (!$this->loggedinUser) {
                    $query->whereNot(function ($query) use ($request) {
                        $query->whereHas('user.preferences', function ($query) use ($request) {
                            $query->where('slug', 'account')
                                ->where('value', 0);
                        });
                    });
                }
            });


            if ($request->teams) {
                // $usersType = $this->userType->findByUserTypePulk([Club::class, Player::class, Trainer::class])->pluck('id');
                $usersType = [Club::class, Player::class, Trainer::class];
                $query->whereHas('user', function ($query) use($usersType) {
                    $query->whereIn('user_type_class', $usersType);
                });
            }
        });

        // prevent blocked accounts
        $models->whereNot(function ($query) use ($request) {
            $query->whereHas('user.blocks', function ($query) use ($request) {
                $query->where('blockable_id', $this->loggedinUser?->id);
            })->orWhereHas('user.blocked', function ($query) use ($request) {
                $query->where('user_id', $this->loggedinUser?->id);
            });
        });

        $models = $models->with($request->with ?: [])->withCount($request->withCount ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function create($request)
    {
        $model = \DB::transaction(function () use ($request) {
            $sound = $this->sound->find($request->sound_id);
            if ($request->hasFile('video')) {
                if(substr($request->file('video')->getMimeType(), 0, 5) == 'image') {
                    $video = FileUploader::convertImageToVideo($request->file('video'), $sound?->sound, $request->text, $request->sticker);
                } else {
                    $video = FileUploader::uploadMedia($request->file('video'), $sound?->sound, $request->video_is_muted, $request->text, $request->sticker);
                }
            }

            $model = $this->model->create([
                'user_id' => $request->user_id,
                'status_id' => $request->status ?? 1,
                'comments_status_id' => $request->comments_status ?? 1,
                'description' => $request->description,
            ]);

            $video->update([
                'model_id' => $model->id,
                'model_type' => get_class($this->model),
            ]);

            return $model;
        });

        if ($request->tagged_users && is_array($request->tagged_users)){
            $tagged_users = $this->UserI->findByUsernamePulk($request->tagged_users);
            foreach ($request->tagged_users as $tagged_user){
                $user = $tagged_users->where('username', $tagged_user)->first();
                if($user){
                    $video_tag = $model->video_tages()->create(['user_id' => $user->id]);
                    UserTaged::dispatch($video_tag);
                }
            }
        }

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        VideoCreated::dispatch($model);

        return ['status' => true, 'data' => $model];
    }

    public function deleteVideo($request, $id)
    {
        if($this->loggedinUser instanceof Admin) {
            $model = $this->findByWhere('id', $id);
        }else{
            $model = $this->findByWhere('id', $id, ['user_id' => $this->loggedinUser?->id]);
        }
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }
        $deleted = $this->delete($id);
        if (!$deleted) {
            return ['status' => false, 'errors' => ['error' => [trans('message.erroe', ['model' => trans_class_basename($this->model)])]]];
        }
        if (!$deleted['status']) {
            return ['status' => false, 'errors' => $deleted['errors']];
        }
        DeleteReportable::dispatch($model, $request->note);
        return ['status' => true, 'data' => []];
    }
}
