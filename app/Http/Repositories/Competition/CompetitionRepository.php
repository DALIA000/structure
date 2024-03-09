<?php

namespace App\Http\Repositories\Competition;

use App\Http\Repositories\{
    Base\BaseRepository,
    User\UserInterface,
};
use App\Models\{
    Competition,
};
use App\Services\{
    LoggedinUser,
    FileUploader,
};

use App\Events\CompetitionCreated;

class CompetitionRepository extends BaseRepository implements CompetitionInterface
{
    public $loggedinUser;

    public function __construct(Competition $model, public UserInterface $UserI)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
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

            if ($request->exists('status_id') && $request->status_id !== null) {
                $query->where('status_id', $request->status_id);
            }

            if($request->exists('type') && $request->type !== null){
                $query->where('type', $request->type);
            }

            // check if user is allowed to view
            if ($request->exists('subscriber_id')) {
                $query->where(function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query->where('type', 0)
                            ->whereHas('club.subscribers', function ($query) use ($request) {
                            $query->where('user_id', $request->subscriber_id);
                        });
                    })->orWhere(function ($query) use ($request) {
                        $query->where('type', 1);
                    })->orWhere(function ($query) use ($request) {
                        $query->where('user_id', $request->subscriber_id);
                    });
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
            $model = $this->model->create([
                'user_id' => $request->user_id,
                'title' => $request->title,
                'description' => $request->description,
                'prize' => $request->prize,
                'days' => $request->days,
                'winners_count' => $request->winners_count,
                'type' => $request->type,
                'style' => $request->style,
                'price' => $request->price,
            ]);

            if($request->media){
                \Media::where('id', $request->media)->update([
                    'model_id' => $model->id,
                    'model_type' => get_class($this->model),
                ]);
            }

            if ($request->options) {
                $model->options()->createMany($request->options);
            }

            return $model;
        });

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($model->type == 0) {
            CompetitionCreated::dispatch($model);
        }

        return ['status' => true, 'data' => $model];
    }

    public function update($request, $id)
    {
        $model = $this->findById($id);
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($request->exists('title') && $request->title !== null) {
            $model->title = $request->title;
        }

        if ($request->exists('description') && $request->description !== null) {
            $model->description = $request->description;
        }

        if ($request->exists('prize') && $request->prize !== null) {
            $model->prize = $request->prize;
        }

        if ($request->exists('days') && $request->days !== null) {
            $model->days = $request->days;
        }

        if ($request->exists('winners_count') && $request->winners_count !== null) {
            $model->winners_count = $request->winners_count;
        }

        if ($request->exists('media')) {
            if ($request->media == null) {
                $model->media()->delete();
            } else {
                $model->clearMediaCollectionExcept('media', \Media::where('id', $request->media)->first());
                \Media::where('id', $request->media)->update([
                    'model_id' => $model->id,
                    'model_type' => get_class($this->model),
                ]);
            }
        }

        $model->save();

        return ['status' => true, 'data' => $model];
    }

    public function delete($request)
    {
        $model = $this->loggedinUser?->competitions()->where('id', $request->id);

        if (!$model->count()) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model->delete();

        return ['status' => true, 'data' => []];
    }

    public function subscribe($request, $id)
    {
        $model = $this->model->where('id', $id)->where('user_id', '!=', $this->loggedinUser?->id)->where(function ($query) use ($request, $id) {
            $query->where(function ($query) use ($request) {
                $query->where('type', 0)
                    ->whereHas('club.subscribers', function ($query) use ($request) {
                    $query->where('user_id', $this->loggedinUser?->id);
                });
            })->orWhere(function ($query) use ($request) {
                $query->where('type', 1);
            });
        })->whereNot(function ($query) use ($request) {
            $query->whereHas('user.blocks', function ($query) use ($request) {
                $query->where('blockable_id', $this->loggedinUser?->id);
            })->orWhereHas('user.blocked', function ($query) use ($request) {
                $query->where('user_id', $this->loggedinUser?->id);
            });
        })->first();
    
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if (!$model->ends_at) {
            return ['status' => false, 'errors' => ['error' => [trans('messages.competitionsIsEnded', ['model' => trans_class_basename($this->model)])]]];
        }

        $subscribtion = $model->subscribtions()->updateOrCreate([
            'user_id' => $this->loggedinUser?->id
        ]);

        if (!$subscribtion) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        return ['status' => true, 'data' => []];
    }

    public function unsubscribe($request, $id)
    {
        $model = $this->model->where('id', $id)->where(function ($query) use ($request, $id) {
            $query->where(function ($query) use ($request) {
                $query->whereHas('subscribtions', function ($query) use ($request) {
                    $query->where('user_id', $this->loggedinUser?->id);
                });
            });
        })->first();
    
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if (!$model->ends_at) {
            return ['status' => false, 'errors' => ['error' => [trans('messages.competitionsIsEnded', ['model' => trans_class_basename($this->model)])]]];
        }

        $subscribtion = $model->subscribtions()->where([
            'user_id' => $this->loggedinUser?->id
        ])->delete();

        if (!$subscribtion) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }
        
        return ['status' => true, 'data' => []];
    }

    public function participations($request, $id)
    {
        $model = $this->model->where('id', $id)->where('user_id', $this->loggedinUser?->id)->first();
    
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $participations = $model->participations()->with($request->with ?: [])->withCount($request->withCount ?: [])->get();

        if (!$participations) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        return ['status' => true, 'data' => $participations];
    }

    public function participate($request, $id)
    {
        $model = $this->model->where('id', $id)->where('user_id', '!=', $this->loggedinUser?->id)->where(function ($query) use ($request, $id) {
            $query->where(function ($query) use ($request) {
                $query->where('type', 0)
                    ->whereHas('club.subscribers', function ($query) use ($request) {
                    $query->where('user_id', $this->loggedinUser?->id);
                });
            })->orWhere(function ($query) use ($request) {
                $query->where('type', 1);
            });
        })->whereNot(function ($query) use ($request) {
            $query->whereHas('user.blocks', function ($query) use ($request) {
                $query->where('blockable_id', $this->loggedinUser?->id);
            })->orWhereHas('user.blocked', function ($query) use ($request) {
                $query->where('user_id', $this->loggedinUser?->id);
            });
        })->first();
    
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if (!$model->ends_at) {
            return ['status' => false, 'errors' => ['error' => [trans('messages.competitionsIsEnded', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($model->has_participated()->count()) {
            return ['status' => false, 'errors' => ['error' => [trans('messages.alreadyParticipated', ['model' => trans_class_basename($this->model)])]]];
        }

        $participation = $model->participations()->updateOrCreate([
            'user_id' => $this->loggedinUser?->id,
        ], [
            'user_id' => $this->loggedinUser?->id,
            'option_id' => $request->option_id
        ]);

        if (!$participation) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        $right_option = $model->options()->where('is_right_option', 1)->first();
        return ['status' => true, 'data' => $right_option];
    }
}
