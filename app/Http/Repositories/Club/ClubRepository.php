<?php

namespace App\Http\Repositories\Club;

use App\Http\Repositories\Club\ClubInterface;
use App\Http\Repositories\{
    Base\BaseRepository,
    Status\StatusInterface,
    City\CityInterface,
};
use App\Models\{
    User,
    Club,
};
use DB;
use Hash;
use Carbon\Carbon;

class ClubRepository extends BaseRepository implements ClubInterface
{
    public $loggedinUser;
    public $user_type_class;

    public function __construct(Club $model, private User $user, private StatusInterface $StatusI, private CityInterface $CityI)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
        $this->user_type_class = Club::class;
    }

    public function findByUsername($username, $request=null)
    {
        $model = $this->user->where('username', $username)
            ->where(['user_type_class' => $this->model::class])
            ->first();
        return $model ?? false;
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('search') && $request->search !== null) {
                $query->where(function ($query) use ($request) {
                    $query->where('business_name', 'like', "%{$request->search}%");
                })->orWhereHas('account', function ($query) use ($request) {
                    $query->where('email', 'like', "%{$request->search}%")
                            ->orWhere('phone', 'like', "%{$request->search}%");
                });
            }

            if ($request->exists('city') && $request->city !== null) {
                $query->whereHas('account', function ($query) use ($request) {
                    $query->where('city_id', $request->city);
                });
            }

            if ($request->exists('username') && $request->username !== null) {
                $query->whereHas('account', function ($query) use ($request) {
                    $query->where('username', $request->username);
                });
            }

            if ($request->exists('status') && $request->status !== null) {
                $query->whereHas('status', function ($query) use ($request) {
                    $query->where('slug', $request->status);
                });
            }
        });

        // prevent blocked accounts
        $models->whereNot(function ($query) use ($request) {
            $query->whereHas('account.blocks', function ($query) use ($request) {
                $query->where('blockable_id', $this->loggedinUser?->id);
            })->orWhereHas('account.blocked', function ($query) use ($request) {
                $query->where('user_id', $this->loggedinUser?->id);
            });
        });

        if ($request->exists('trashed') && $request->trashed !== null) {
            $models->onlyTrashed();
        }

        $models = $models->with($request->with ?: [])->withCount($request->withCount ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function activate($request, $id)
    {
        $model = $this->findById($id);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $status_id = $this->StatusI->findBySlug('active')?->id;
        $this->setStatus($model, $status_id);

        return ['status' => true, 'data' => $model?->account];
    }

    public function block($request, $id)
    {
        $model = $this->findById($id);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $status_id = $this->StatusI->findBySlug('blocked')?->id;
        $this->setStatus($model, $status_id);

        return ['status' => true, 'data' => $model?->account];
    }

    public function unblock($request, $id)
    {
        $model = $this->findById($id);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($request->exists('status') && $request->status != null) {
            $status_id = $this->StatusI->findById($request->id)?->id;
        } else {
            $status_id = $this->StatusI->findBySlug('active')?->id;
        }
        $this->setStatus($model, $status_id);

        return ['status' => true, 'data' => $model?->account];
    }

    public function create($request)
    {
        $city = $this->CityI->findBySlug($request->city);

        $model = DB::transaction(function () use ($request, $city /* $club*/) {
            $model = $this->user->create([
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'city_id' => $city?->id,
                'birthday' => $request->birthday,
                'user_type_class' => $this->user_type_class,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'keywords' => [$request->business_name],
            ]);

            $model->update(['user_id' => $model->id]);
            $user_type = $model->user_type;

            switch ($user_type?->slug) {
                case 'club':
                    $user = $model->user()->create([
                        'user_id' => $model->user_id,
                        'business_name' => $request->business_name,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'description' => $request->description,
                        'status_id' => 1,
                    ]);

                    $user = $user->club_feature()->create([
                        'club_id' => $user->id
                    ]);
                    break;
            }

            \Media::where('id', $request->media)->update([
                'model_id' => $user->id,
                'model_type' => get_class($this->model),
            ]);

            $social_auth = $model->social_auth()->create([]);
            $preferences = $user_type->preferences->each(fn($i)=> $model->user_preferences()->create(['preference_id' => $i->id]));

            return $model;
        });

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        return ['status' => true, 'data' => $model->user];
    }

    public function edit($request, $id)
    {
        $model = $this->findById($id);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $city = $this->CityI->findBySlug($request->city);
        $account = $model->account;

        if ($request->exists('email') && $request->email !== null && $request->email !== $account->email) {
            $account->update([
                'email' => $request->email,
                'email_verified_at' => Carbon::now(),
            ]);
        }

        if ($request->exists('phone') && $request->phone !== null && $request->phone !== $account->phone) {
            $account->update([
                'phone' => $request->phone,
                'phone_verified_at' => Carbon::now(),
            ]);
        }

        if ($request->exists('birthday') && $request->birthday !== null) {
            $account->birthday = $request->birthday;
        }

        if ($request->exists('username') && $request->username !== null) {
            $account->username = $request->username;
        }

        if ($request->exists('city') && $request->city !== null) {
            $city = $this->CityI->findBySlug($request->city);
            $account->city_id = $city->id;
        }

        if ($request->exists('bio') && $request->bio !== null) {
            $account->bio = $request->bio;
        }

        $account->save();

        if ($request->exists('business_name') && $request->business_name !== null) {
            $model->business_name = $request->business_name;
        }

        if ($request->exists('latitude') && $request->latitude !== null) {
            $model->latitude = $request->latitude;
        }

        if ($request->exists('longitude') && $request->longitude !== null) {
            $model->longitude = $request->longitude;
        }

        if ($request->exists('description') && $request->description !== null) {
            $model->description = $request->description;
        }

        if ($request->exists('media')) {
            if ($request->media == null) {
                $model->media()->delete();
            }else {
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

    public function setStatus($model, $status)
    {
        return $model->update([
            'status_id' => $status,
        ]);
    }
}
