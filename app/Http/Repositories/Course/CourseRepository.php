<?php

namespace App\Http\Repositories\Course;

use App\Events\CourseAccepted;
use App\Events\CourseRejected;
use App\Events\UserRejected;
use App\Http\Repositories\{
    Base\BaseRepository,
    User\UserInterface,
    FinancialSetting\FinancialSettingInterface
};
use App\Http\Repositories\Status\StatusInterface;
use App\Models\{
    Course,
    Video,
    Invoice,
    User,
    CourseSubscribtion,
};
use App\Models\Status;
use App\Services\{
    LoggedinUser,
    FileUploader,
};

use App\Events\UserSubscribedCourse;

class CourseRepository extends BaseRepository implements CourseInterface
{
    public $loggedinUser;

    public function __construct(Course $model, public CourseSubscribtion $course_subscribtion, public UserInterface $UserI, public Video $video, public Invoice $invoice, public FinancialSettingInterface $FinancialSettingI, private StatusInterface $StatusI)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('user_id') && $request->user_id !== null) {
                $query->whereHas('video', function ($query) use ($request) {
                    $query->where('user_id' ,$request->user_id);
                });
            }

            if ($request->exists('username') && $request->username !== null) {
                $query->whereHas('video.user', function ($query) use ($request) {
                    $query->where('username', $request->username);
                });
            }

            if ($request->exists('video_id') && $request->video_id !== null) {
                $query->where('video_id', $request->video_id);
            }

            if ($request->exists('status_id') && $request->status_id !== null) {
                $query->where(function ($query) use ($request) {
                    $query->where('status_id', $request->status_id)
                        ->orWhereHas('video', function ($query) use ($request) {
                            $query->where('user_id', $this->loggedinUser?->id);
                        });
                });
            }
        });

        // prevent blocked accounts
        $models->whereNot(function ($query) use ($request) {
            $query->whereHas('video.user.blocks', function ($query) use ($request) {
                $query->where('blockable_id', $this->loggedinUser?->id);
            })->orWhereHas('video.user.blocked', function ($query) use ($request) {
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
            if ($request->hasFile('video')) {
                if(substr($request->file('video')->getMimeType(), 0, 5) == 'image') {
                    $media = FileUploader::convertImageToVideo($request->file('video'));
                } else {
                    $media = FileUploader::uploadMedia($request->file('video'));
                }
            }

            $video = $this->video->create([
                'user_id' => $request->user_id,
                'status_id' => $request->status ?? 1,
                'comments_status_id' => $request->comments_status ?? 1,
                'description' => $request->description,
            ]);

            $model = $this->model->create([
                'video_id' => $video?->id,
                'title' => $request->title,
                'description' => $request->description,
                'individual_price' => $request->individual_price,
                'individual_currency_id' => 2, // sar
                'group_discount' => $request->group_discount,
                'seats_count' => $request->seats_count,
            ]);

            $media->update([
                'model_id' => $video->id,
                'model_type' => get_class($video),
            ]);

            $sessions = $model->sessions()->createMany($request->sessions);

            return $model;
        });

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        return ['status' => true, 'data' => $model];
    }

    public function update($request, $id)
    {
        $model = $this->loggedinUser?->courses()->where('video_id', $id)->first();

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($model->subscribtions()->count()) {
            return ['status' => false, 'errors' => ['error' => [trans('messages.cantUpdateCourseWithSubscriptions', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($request->exists('title') && $request->title !== null) {
            $model->title = $request->title;
        }

        if ($request->exists('description') && $request->description !== null) {
            $model->description = $request->description;
        }

        if ($request->exists('individual_price') && $request->individual_price !== null) {
            $model->individual_price = $request->individual_price;
        }

        if ($request->exists('group_discount') && $request->group_discount !== null) {
            $model->group_discount = $request->group_discount;
        }

        if ($request->exists('seats_count') && $request->seats_count !== null) {
            $model->seats_count = $request->seats_count;
        }

        $model->save();

        if ($request->exists('sessions') && $request->sessions !== null) {
            $model->sessions()->delete();
            $model->sessions()->createMany($request->sessions);
        }

        return ['status' => true, 'data' => $model];
    }

    public function accept($request, $id)
    {
        $status_id = Status::where('slug', 'pending')->first()?->id;
        $model = $this->findWhere(['video_id' => $id, 'status_id' => $status_id]);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($model->status_id !== 2) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $invoice = \DB::transaction(function () use ($request, $model) {
            $profit_margin = $this->FinancialSettingI->findBySlug('course-profit-margin')?->value; // %
            $zoom_minute_cost = $this->FinancialSettingI->findBySlug('zoom-minute-cost')?->value;

            // sessions_count * minutes per session * seats_count * minute pice
            $cost = $model->sessions()->count() * 45 * $model->seats_count * $zoom_minute_cost;

            $invoice = $this->invoice->updateOrCreate([
                'invoicable_id' => $model->id,
                'invoicable_type' => $request->invoicable_type,
            ], [
                'invoicable_id' => $model->id,
                'invoicable_type' => $request->invoicable_type,
                'cost' => $cost,
                'profit_margin' => $profit_margin,
            ]);

            // change model status
            if ($status_id = Status::where('slug', 'need-action')->first()?->id) {
                $model->update([
                    'status_id' => $status_id,
                ]);
            }

            return $invoice;
        });

        if (!$invoice) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->InvoiceI->model)])]]];
        }

        CourseAccepted::dispatch($invoice);

        return ['status' => true, 'data' => $invoice];
    }

    public function course_delete($request, $id)
    {
        if ($this->loggedinUser instanceof User) {
            $models = $this->loggedinUser?->courses()->where('video_id', $id)->where('courses.status_id', '!=', 1);
        } else {
            $models = $this->model->where('video_id', $id)->where('courses.status_id', '!=', 1);
        }

        if (!$models->count()) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = $models->first();

        $delete = \DB::transaction(function () use ($model) {
            $video = $model?->video;
            $video->delete();
            $this->delete($model?->id);

            return true;
        });

        if (!$delete) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.delete', ['model' => trans_class_basename($this->model)])]]];
        }

        return ['status' => true, 'data' => []];
    }

    public function reject($request, $id)
    {
        $model = $this->findById($id);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($model->status_id === 1) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }
        $status_id = $this->StatusI->findBySlug('rejected')?->id;
        $this->setStatus($model, $status_id, $request->note);

        CourseRejected::dispatch($model , $request->note);

        return ['status' => true, 'data' => $model?->account];
    }

    public function subscribe($request, $id)
    {
        $model = $this->model->where('video_id', $id)->first();

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($model->subscribtions()->where('user_id', $this->loggedinUser?->id)->count()) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.alreadySubscribed', ['model' => trans_class_basename($this->model)])]]];
        }

        $subscribtion = $model->subscribtions()->updateOrCreate([
            'user_id' => $this->loggedinUser?->id
        ]);

        if (!$subscribtion) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        UserSubscribedCourse::dispatch($subscribtion);

        return ['status' => true, 'data' => []];
    }

    public function academySubscribe($request, $id)
    {
        $model = $this->findByWhere('video_id', $id, [], function ($query) use ($request) {
                $query->whereHas('video.user.blocks', function ($query) use ($request) {
                    $query->where('blockable_id', $this->loggedinUser?->id);
                })->orWhereHas('video.user.blocked', function ($query) use ($request) {
                    $query->where('user_id', $this->loggedinUser?->id);
                });
            });
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $subscribed_users = $model->subscribtions()->whereHas('user', function($query) use ($request) {
            $query->whereIn('username', $request->players);
        })->get();

        if($subscribed_users->count()) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.playersAlreadySubscribed', ['users' => $subscribed_users,'model' => trans_class_basename($this->model)])]]];
        }

        $users = $this->UserI->findByUsernamePulk($request->players);

        \DB::transaction(function () use ($model, $users) {
            foreach ($users as $user) {
                $subscribtion = $model->subscribtions()->updateOrCreate([
                    'user_id' => $user->id
                ]);

                if (!$subscribtion) {
                    return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
                }

                UserSubscribedCourse::dispatch($subscribtion);
            }
        });

        return ['status' => true, 'data' => []];
    }

    public function subscribtions ($request, $id)
    {
        $subscribtions = $this->course_subscribtion->where('course_id', $id)->get();
        if (!$subscribtions) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        return ['status' => true, 'data' => $subscribtions];
    }

     public function setStatus($model, $status, $status_note=null)
    {
        return $model->update([
            'status_id' => $status,
            'status_note' => $status_note,
        ]);
    }
}
