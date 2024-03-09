<?php

namespace App\Http\Repositories\User;

use App\Http\Repositories\{
    Base\BaseRepository,
    City\CityInterface,
};
use App\Events\{
    UserRegistered,
    UserVerification,
    UserAccountVerified,
    ForgetPasswordCodeGenerated,
};
use App\Http\Repositories\UserType\UserTypeInterface;
use App\Models\{
    User,
    AcademyPlayer,
    Document,
    File,
    Like,
    Comment,
    View,
    Video,
    Subscribtion,
    CourseSubscribtion,
    CompetitionSubscribtion,
    UserType
};
use App\Models\Fan;
use App\Models\Model;
use App\Models\UserCertification;
use App\Services\LoggedinUser;
use DB;
use Hash;
use Carbon\Carbon;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UserRepository extends BaseRepository implements UserInterface
{
    public $loggedinUser;

    public function __construct(User $model, public Like $like, public Comment $comment, public View $view, public CityInterface $CityI, public Subscribtion $subscribtion, public CourseSubscribtion $course_subscribtion, public CompetitionSubscribtion $competition_subscribtion, public UserTypeInterface $UserTypeI)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function findByEmail($email)
    {
        $model = $this->model->where('email', $email)->first();
        return $model ?? false;
    }

    public function findByPhone($phone)
    {
        $model = $this->model->where('phone', $phone)->first();
        return $model ?? false;
    }

    public function findByUsername($username)
    {
        $model = $this->model->where('username', $username)->first();
        return $model ?? false;
    }

    public function findByUsernamePulk($usernames)
    {
        $models = $this->model->whereIn('username', $usernames)->get();
        return $models;
    }

    public function findByVerificationCode($verification_code)
    {
        $model = $this->model->whereHas('verification_code', function ($query) use ($verification_code) {
            $query->where('code', $verification_code);
        })->first();
        return $model ?? false;
    }

    public function findByEmailAndVerificationCode($email, $verification_code)
    {
        $model = $this->model->where('email', $email)->whereHas('verification_code', function ($query) use ($verification_code) {
            $query->where('code', $verification_code);
        })->first();
        return $model ?? false;
    }

    public function findByPhoneAndVerificationCode($phone, $verification_code)
    {
        $model = $this->model->where('phone', $phone)->whereHas('verification_code', function ($query) use ($verification_code) {
            $query->where('code', $verification_code);
        })->first();
        return $model ?? false;
    }

    public function findBySocial($social, $id)
    {
        $user = $this->model->where(function ($query) use ($social, $id) {
            $query->whereHas('social_auth', function ($query) use ($social, $id) {
                $query->where($social, $id);
            });
        })->first();

        return $user;
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('username') && $request->username !== null) {
                $query->where('username', 'like', "%{$request->username}%");
            }

            if ($request->exists('search')) {
                $query->where(function ($query) use ($request) {
                    $query->where('username', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%")
                        ->orWhere('keywords', 'like', "%{$request->search}%");
                });
            }

            if ($request->exists('status') && $request->status != null) {
                $query->whereHas('user', function ($query) use ($request) {
                    $query->where('status_id', $request->status);
                });
            }

            if ($request->follow) {
                $query->where(function ($query) use ($request) {
                    $query->whereHas('followings', function ($query) use ($request) {
                        $query->where('user_id', $this->loggedinUser?->id)
                            ->where('is_pending', 0);
                    })->orWhereHas('followers', function ($query) use ($request) {
                        $query->where('user_id', $this->loggedinUser?->id)
                            ->where('is_pending', 0);
                    });
                });
            }
        });

        // prevent blocked accounts
        $models->whereNot(function ($query) use ($request) {
            $query->whereHas('blocks', function ($query) use ($request) {
                $query->where('blockable_id', $this->loggedinUser?->id);
            })->orWhereHas('blocked', function ($query) use ($request) {
                $query->where('user_id', $this->loggedinUser?->id);
            });
        });

        $models = $models->whereNotIn('id', $request->whereIdNotIn ?: []);
        $models = $models->with($request->with ?: [])->withCount($request->withCount ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function create($request)
    {
        $city = $this->CityI->findBySlug($request->city);

        $model = DB::transaction(function () use ($request, $city /* $club*/) {
            $user_type = UserType::where(['id' => $request->user_type ?? 1])?->first();
            $fan_user_type = UserType::where(['id' => 1])?->first();

            $model = $this->model->create([
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'city_id' => $city?->id,
                'birthday' => $request->birthday,
                'user_type_class' => $fan_user_type->user_type,
                'keywords' => [$request->first_name, $request->last_name],
            ]);

            $model->update(['user_id' => $model->id]);

            //// case all:
            $model->user()->create([
                'user_id' => $model->user_id,
                'first_name' => $request->first_name ?: $request->business_name,
                'last_name' => $request->last_name,
            ]);

            switch ($user_type?->slug) {
                case 'academy':
                    $user_type->user_type::create([
                        'user_id' => $model->user_id,
                        'business_name' => $request->business_name,
                        'academy_level_id' => $request->academy_level,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                    ]);
                    break;

                case 'player':
                    $user_type->user_type::create([
                        'user_id' => $model->user_id,
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'player_position_id' => $request->player_position,
                        'player_footness_id' => $request->player_footness,
                    ]);

                    if ($request->exists('academy') && $request->academy !== null) {
                        if ($academy = $this->model->where('username', $request->academy)->first()) {
                            AcademyPlayer::create([
                                'academy_id' => $academy->user->id,
                                'player_id' => $model->user->id,
                            ]);
                        } else {
                            $user_type->user_type::update([
                                'other_academy' => $request->academy,
                            ]);
                        }
                    }
                    break;

                case 'trainer':
                    $user_type->user_type::create([
                        'user_id' => $model->user_id,
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'trainer_experience_level_id' => $request->trainer_experience_level,
                        'achievements' => $request->achievements,
                    ]);
                    break;

                case 'journalist':
                    $user_type->user_type::create([
                        'user_id' => $model->user_id,
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                    ]);
                    break;

                case 'business':
                    $user_type->user_type::create([
                        'user_id' => $model->user_id,
                        'business_name' => $request->business_name,
                    ]);
                    break;

                case 'influencer':
                    $user_type->user_type::create([
                        'user_id' => $model->user_id,
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                    ]);
                    break;
            }

            $certification = $model->certifications()->create([]);
            $social_auth = $model->social_auth()->create([
                "facebook" => $request->facebook_id,
                "google" => $request->google_id,
                "twitter" => $request->twitter_id,
                "apple" => $request->apple_id,
            ]);

            $preferences = $fan_user_type->preferences->each(fn($i)=> $model->user_preferences()->create(['preference_id' => $i->id]));

            $certification->update([
                'commercial_certification_document_id' => $request->commercial_certification,
                'experience_certification_document_id' => $request->experience_certification,
                'training_certification_document_id' => $request->training_certification,
                'journalism_certification_document_id' => $request->journalism_certification,
                'influencement_certification_document_id' => $request->influencement_certification,
            ]);

            Document::whereIn('id', [
                $certification->commercial_certification_document_id,
                $certification->experience_certification_document_id,
                $certification->training_certification_document_id,
                $certification->influencement_certification_document_id,
            ])->update(['documentable_id' => $certification->id, 'documentable_type' => UserCertification::class]);

            $model->verification_code()->create([
                'code' => $this->generateVerificationCode(),
                'code_expires_at' => Carbon::now()->addMinutes(5),
            ]);

            // default image

            $file = File::create();
            \File::copy(public_path('images/user-default.png'), public_path('images/user.png'));
            $media = $file->addMedia(public_path('images/user.png'))->toMediaCollection('media');

            Media::where('id', $request->media)->update([
                'model_id' => $model->id,
                'model_type' => get_class($this->model),
            ]);

            return $model;
        });

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        UserRegistered::dispatch($model);
        $token = $this->createToken($request, $model);
        return ['status' => true, 'data' => $model, 'token' => $token];
    }

    public function connectSocial($social, $request)
    {
        $model = $this->loggedinUser;

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => trans('auth.unauthenticated')]];
        }

        $social_id_key = $social.'_id';
        $model->social_auth()->updateOrCreate(['user_id' => $model->id], [
            $social => $request->$social_id_key,
        ]);

        return ['status' => true, 'data' => null];
    }

    public function disconnectSocial($social)
    {
        $model = $this->loggedinUser;

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => trans('auth.unauthenticated')]];
        }

        $model->social_auth()->updateOrCreate(['user_id' => $model->id], [
            $social => null,
        ]);

        return ['status' => true, 'data' => null];
    }

    public function edit($request, $id)
    {
        $model = $this->findById($id);
        $certification = $model->certification;

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $user = $model->user;
        $user_type = $model->user_type;

        if ($request->exists('email') && $request->email !== null && $request->email !== $model->email) {
            $model->update([
                'email' => $request->email,
                'email_verified_at' => null,
            ]);

            $model->verification_code
                ? $model->verification_code->update([
                    'code' => $this->generateVerificationCode(),
                    'code_expires_at' => Carbon::now()->addMinutes(5),
                ])
                : $model->verification_code()->create([
                    'code' => $this->generateVerificationCode(),
                    'code_expires_at' => Carbon::now()->addMinutes(5),
                ]);

            UserVerification::dispatch($model);
            $model->tokens()->delete();
        }

        if ($request->exists('phone') && $request->phone !== null && $request->phone !== $model->phone) {
            $model->update([
                'phone' => $request->phone,
                'phone_verified_at' => null,
            ]);

            $model->verification_code
                ? $model->verification_code->update([
                    'code' => $this->generateVerificationCode(),
                    'code_expires_at' => Carbon::now()->addMinutes(5),
                ])
                : $model->verification_code()->create([
                    'code' => $this->generateVerificationCode(),
                    'code_expires_at' => Carbon::now()->addMinutes(5),
                ]);
            UserVerification::dispatch($model);
        }

        if ($request->exists('username') && $request->username !== null && $request->username !== $model->username) {
            $model->username = $request->username;
        }

        if ($request->exists('birthday') && $request->birthday !== null && $request->birthday !== $model->birthday) {
            $model->birthday = $request->birthday;
        }

        if ($request->exists('bio') && $request->bio !== $model->bio) {
            $model->bio = $request->bio;
        }

        if ($request->exists('city') && $request->city !== null) {
            $city = $this->CityI->findBySlug($request->city);
            if($city?->id !== $model->city_id){
                $model->city_id = $city->id;
            }
        }

        if ($request->exists('club') && $request->club !== null) {
            $club = $this->findByUsername($request->club)?->user;
            if($club?->id !== $model->club_id){
                $model->club_id = $club->id;
            }
        }

        if ($request->exists('media')) {
            $model->clearMediaCollection('media');
            Media::where('id', $request->media)->update([
                'model_id' => $model->id,
                'model_type' => get_class($this->model),
            ]);
        }

        $model->save();

        switch ($user_type?->slug) {
            case 'fan':
                $user = $this->person_info($user, $request);
                break;

            case 'academy':
                $user = $this->business_info($user, $request);

                if ($request->exists('academy_level') && $user->academy_level && $request->academy_level !== null && $request->academy_level !== $user->academy_level) {
                    $user->academy_level_id = $request->academy_level;
                }

                if ($request->exists('latitude') && $user->latitude && $request->latitude !== null && $request->latitude !== $user->latitude) {
                    $user->latitude = $request->latitude;
                }

                if ($request->exists('longitude') && $user->longitude && $request->longitude !== null && $request->longitude !== $user->longitude) {
                    $user->longitude = $request->longitude;
                }

                if ($request->exists('description') && $request->description !== null && $request->description !== $user->description) {
                    $user->description = $request->description;
                }
                break;

            case 'player':
                $user = $this->person_info($user, $request);

                if ($request->exists('player_position') && $request->player_position !== null && $request->player_position !== $user->player_position) {
                    $user->player_position_id = $request->player_position;
                }

                if ($request->exists('player_footness') && $request->player_footness !== null && $request->player_footness !== $user->player_footness) {
                    $user->player_footness_id = $request->player_footness;
                }

                if ($request->exists('academy')) {
                    $academy = $this->findByUsername($request->academy)?->user;
                    if($academy){
                        $user->academy_players()->where('academy_id', '!=', $academy->id)?->delete();
                        $academy_players = $user->academy_players()->firstOrCreate([
                            'academy_id' => $academy->user->id, // adademies
                        ]);
                    }
                }
                break;

            case 'trainer':
                $user = $this->person_info($user, $request);

                if ($request->exists('trainer_experience_level') && $request->trainer_experience_level !== null && $request->trainer_experience_level !== $user->trainer_experience_level) {
                    $user->trainer_experience_level_id = $request->trainer_experience_level;
                }

                if ($request->exists('achievements') && $request->achievements !== null && $request->achievements !== $user->achievements) {
                    $user->achievements = $request->achievements;
                }
                break;

            case 'journalist':
                $user = $this->person_info($user, $request);
                break;

            case 'business':
                $user = $this->business_info($user, $request);
                break;

            case 'club':
                $user = $this->business_info($user, $request);
                if ($request->exists('description') && $request->description !== null && $request->description !== $user->description) {
                    $user->description = $request->description;
                }
                if ($request->exists('latitude') && $request->latitude !== null && $request->latitude !== $user->latitude) {
                    $user->latitude = $request->latitude;
                }
                if ($request->exists('longitude') && $request->longitude !== null && $request->longitude !== $user->longitude) {
                    $user->longitude = $request->longitude;
                }

            case 'federation':
                if ($request->exists('description') && $request->description !== null && $request->description !== $user->description) {
                    $user->description = $request->description;
                }
                if ($request->exists('latitude') && $request->latitude !== null && $request->latitude !== $user->latitude) {
                    $user->latitude = $request->latitude;
                }
                if ($request->exists('longitude') && $request->longitude !== null && $request->longitude !== $user->longitude) {
                    $user->longitude = $request->longitude;
                }
                break;
        }

        $user->save();

        return ['status' => true, 'data' => $model];
    }

    public function changeUserTypeRequest($request, $id)
    {
        $model = $this->findById($id);
        $certification = $model->certification;

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($model->user_type_class !== Fan::class) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.changeToFanFirst', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = DB::transaction(function () use ($request, $model, $certification) {

            $user_type = $this->UserTypeI->findById($request->user_type);

            // request
            $model->user_type_requests()->create([
                'user_type_class' => $user_type?->user_type,
            ]);

            // repository pattern break
            switch ($user_type?->slug) {
                case 'fan':
                    ($user_type->user_type)::create([
                        'user_id' => $model->id,
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                    ]);
                    break;

                case 'academy':
                    ($user_type->user_type)::create([
                        'user_id' => $model->id,
                        'business_name' => $request->business_name,
                        'academy_level_id' => $request->academy_level,
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                    ]);
                    break;

                case 'player':
                    ($user_type->user_type)::create([
                        'user_id' => $model->id,
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'player_position_id' => $request->player_position,
                        'player_footness_id' => $request->player_footness,
                    ]);

                    if ($request->exists('academy') && $request->academy !== null && $academy = $this->model->where('username', $request->academy)->first()) {
                        AcademyPlayer::create([
                            'academy_id' => $academy->user->id,
                            'player_id' => $model->user->id,
                        ]);
                    }
                    break;

                case 'trainer':
                    ($user_type->user_type)::create([
                        'user_id' => $model->id,
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'trainer_experience_level_id' => $request->trainer_experience_level,
                        'achievements' => $request->achievements,
                    ]);
                    break;

                case 'influencer':
                    ($user_type->user_type)::create([
                        'user_id' => $model->id,
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                    ]);
                    break;

                case 'journalist':
                    ($user_type->user_type)::create([
                        'user_id' => $model->id,
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                    ]);
                    break;

                case 'business':
                    ($user_type->user_type)::create([
                        'user_id' => $model->id,
                        'business_name' => $request->business_name,
                    ]);
                    break;
            }

            $certification->update([
                'commercial_certification_document_id' => $request->commercial_certification,
                'experience_certification_document_id' => $request->experience_certification,
                'training_certification_document_id' => $request->training_certification,
                'journalism_certification_document_id' => $request->journalism_certification,
                'influencement_certification_document_id' => $request->influencement_certification,
            ]);

            Document::whereIn('id', [
                $certification->commercial_certification_document_id,
                $certification->experience_certification_document_id,
                $certification->training_certification_document_id,
                $certification->influencement_certification_document_id,
            ])->update(['documentable_id' => $model->id, 'documentable_type' => UserCertification::class]);

            return $model;
        });

        return ['status' => true, 'data' => $model];
    }

    public function cancelChangeUserTypeRequest($request, $id)
    {
        $model = $this->findById($id);
        $certification = $model->certification;

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = DB::transaction(function () use ($request, $model, $certification) {

            $user_type = $this->UserTypeI->findById($request->user_type);

            // request
            $user_type_requests = $model->user_type_requests;

            if($user_type_requests->count()) {
                foreach ($user_type_requests as $user_type_request) {
                    ($user_type_request->user_type_class)::where([
                        'status_id' => 2,
                        'user_id' => $model->id
                    ])->delete();
                }
            }
            $model->user_type_requests()->delete();

            return $model;
        });

        return ['status' => true, 'data' => $model];
    }

    public function changeUserType($request, $id)
    {
        $model = $this->findById($id);
        $user_type_request = $model->user_type_requests()->where('id', $request->user_type_request_id)->first();

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($model->user_type_class === $user_type_request->user_type_class) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.forbidden', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = DB::transaction(function () use ($request, $model, $user_type_request) {
            $model->user()->delete();

            $user_type = $this->UserTypeI->findById($request->user_type_request_id);
            $model->update([
                'user_type_class' => $user_type_request->user_type_class,
            ]);

            $model->user()->update(['status_id' => 1]);

            $old_preferences = $model->user_preferences;

            // delete old user preferences
            $model->user_preferences()->delete();

            // set new preferences
            $preferences = $user_type->preferences->each(fn($i)=> $model->user_preferences()->create([
                'preference_id' => $i->id,
                'value' => $old_preferences->where('id', $i->id)?->first()?->value ?: 1,
            ]));

            $user_type_request->update(['status_id' => 1]);

            return $model;
        });

        return ['status' => true, 'data' => $model];
    }

    public function preferences($request, $id, $preference_type = null)
    {
        $user = $this->findById($id);
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $preferences = $user->user_preferences()->where(function ($query) use ($request, $user, $preference_type) {
            $query->whereHas('preference', function ($query) use ($request, $user, $preference_type) {
                if ($user->user?->status_id !== 1) {
                    $query->where('is_professional_specific', 0);
                }
                $query->whereHas('preference_type', function ($query) use ($request, $preference_type) {
                    if($preference_type) {
                        $query->where('slug', $preference_type);
                    }
                });
            });
        })->with(['preference'])->get();

        return ['status' => true, 'data' => $preferences];
    }

    public function editPreference($request, $id)
    {
        $user = $this->findById($id);
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $preference = $user->user_preferences()->where(function ($query) use($request) {
            $query->whereHas('preference', function ($query) use ($request) {
                $query->where('slug', $request->slug);
            });
        })->first();

        if (!$preference) {
            return ['status' => false, 'errors' => ['error' => [trans('auth.forbidden', ['model' => trans_class_basename($this->model)])]]];
        }

        if($request->exists('value')){
            $preference_type = $preference->preference->preference_type->slug;
            switch ($preference_type) {
                case 'general': // home 1
                    $request->validate([
                        'value' => 'in:1,2,3'
                    ]);
                    break;
                case 'section': // 2
                    $request->validate([
                        'value' => 'boolean'
                    ]);
                    break;
                case 'comunication': // 3
                    $request->validate([
                        'value' => 'boolean'
                    ]);
                    break;
                case 'account': // 4
                    $request->validate([
                        'value' => 'boolean'
                    ]);
                    break;
            }
            $preference->update([
                'value' => $request->value,
            ]);
        }

        return ['status' => true, 'data' => $preference];
    }

    public function createToken($request, $model)
    {
        return $model->createToken('user')->accessToken;
    }

    private function generateVerificationCode()
    {
        $verification_code = mt_rand(100000, 999999);

        if ($this->findByVerificationCode($verification_code)) {
            return $this->generateVerificationCode();
        }
        return $verification_code;
    }

    public function verificationCode($request)
    {
        $model = null;
        $via = 'email';
        if ($request->exists('email')) {
            $model = $this->findByEmail($request->email);
        } elseif ($request->exists('phone')) {
            $model = $this->findByPhone($request->phone);
            $via = 'sms';
        }

        if (!$model) {
            return ['status' => false, 'errors' => ['email' => [trans('auth.invalid', ['attribute' => 'email address'])]]];
        }

        $verification_code = $this->generateVerificationCode();
        $model->verification_code()->updateOrCreate(["code" => $verification_code]);

        UserVerification::dispatch($model, $via);

        return ['status' => true, 'pin' => $verification_code];
    }

    public function verify($request)
    {
        $model = null;
        if ($request->exists('email')) {
            $model = $this->findByEmailAndVerificationCode($request->email, $request->code);
        } elseif ($request->exists('phone') && $request->exists('code') && $request->code !== null) {
            $model = $this->findByPhoneAndVerificationCode($request->phone, $request->code);
        }

        if (!$model) {
            return ['status' => false, 'errors' => ['code' => [trans('auth.invalid', ['attribute' => trans('validation.attributes.pin code')])]]];
        }

        if ($request->exists('email')) {
            $model->update(['email_verified_at' => now()]);
            $via = 'email';
        } elseif ($request->exists('phone')) {
            $model->update(['phone_verified_at' => now()]);
            $via = 'sms';
        }

        $model->verification_codes()->delete();

        UserAccountVerified::dispatch($model, $via ?: 'email');

        $token = $this->createToken($request, $model);

        return ['status' => true, 'data' => $model, 'token' => $token];
    }
    public function login($request)
    {
        $model = null;
        if ($request->exists('phone')) {
            $model = $this->findByPhone($request->phone);
        }

        if ($request->exists('email')) {
            $model = $this->findByEmail($request->email);
        }

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('auth.failed')]]];
        }

        if (!Hash::check($request->password, $model->password)) {
            return ['status' => false, 'errors' => ['error' => [trans('auth.failed')]]];
        }

        if (!$model->email_verified_at && !$model->phone_verified_at) {
            return ['status' => false, 'errors' => ['verify' => [trans('auth.notVerified')]]];
        }

        // $status_id = $this->StatusI->findBySlug('blocked')?->id;
        if ($model->user?->status_id == 4) {
            return ['status' => false, 'errors' => ['error' => [trans('auth.blocked')]]];
        }

        $token = $this->createToken($request, $model);

        return ['status' => true, 'data' => ['user' => $model, 'token' => $token]];
    }

    public function logout($request)
    {
        if (!$model = $this->loggedinUser) {
            return ['status' => false, 'errors' => ['error' => [trans('auth.forbidden')]]];
        }

        $logout = LoggedinUser::revoke();

        if (!$logout) {
            return ['status' => false, 'errors' => ['error' => [trans('auth.logout.failed')]]];
        }
        return ['status' => true, 'data' => []];
    }

    public function profile($request)
    {
        $model = $this->loggedinUser;

        $model->loadCount($request->withCount);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('auth.forbidden')]]];
        }

        $bearerToken = $request->bearerToken();
        if (!$bearerToken) {
            return ['status' => false, 'errors' => ['error' => [trans('auth.forbidden')]]];
        }

        return ['status' => true, 'data' => ['user' => $model, 'token' => $bearerToken]];
    }

    public function forgetPasswordGetCode($request)
    {
        $model = null;
        $via = 'email';
        if ($request->exists('email')) {
            $model = $this->findByEmail($request->email);
        } elseif ($request->exists('phone')) {
            $via = 'sms';
            $model = $this->findByPhone($request->phone);
        }

        if (!$model) {
            return ['status' => false, 'errors' => ['email' => [trans('auth.invalid', ['attribute' => 'email address / phone number'])]]];
        }

        $verification_code = $this->generateVerificationCode();
        $model->verification_code()->updateOrCreate(["code" => $verification_code]);

        ForgetPasswordCodeGenerated::dispatch($model, $via);

        return ['status' => true, 'code' => $verification_code];
    }

    public function forgetPasswordCheckCode($request)
    {
        $model = null;
        $via = 'email';
        if ($request->exists('email')) {
            $model = $this->findByEmailAndVerificationCode($request->email, $request->code);
        } elseif ($request->exists('phone')) {
            $via = 'sms';
            $model = $this->findByPhoneAndVerificationCode($request->phone, $request->code);
        }

        if (!$model) {
            return ['status' => false, 'errors' => ['code' => [trans('auth.invalid', ['attribute' => trans('validation.attributes.pin code')])]]];
        }

        return ['status' => true, 'data' => $request->code];
    }

    public function forgetPasswordResetPassword($request)
    {
        $model = null;
        $via = 'email';
        if ($request->exists('email')) {
            $model = $this->findByEmailAndVerificationCode($request->email, $request->code);
        } elseif ($request->exists('phone')) {
            $via = 'sms';
            $model = $this->findByPhoneAndVerificationCode($request->phone, $request->code);
        }

        if (!$model) {
            return ['status' => false, 'errors' => ['email' => [trans('auth.invalid', ['attribute' => trans('validation.attributes.pin code')])]]];
        }

        $model->update([
            'password' => Hash::make($request->password)
        ]);

        $model->verification_code()->delete();

        return ['status' => true, 'data' => $model];
    }

    public function changePassword($request, $id)
	{
		$model = $this->findById($id);

		if (!$model) {
			return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
		}

		$errors = [];

		if ($request->exists('password')) {
			if (!Hash::check($request->current_password, $model->password)) {
				return ['status' => false, 'errors' => ['error' => [trans('auth.current_password')]]];
			}
			$model->password = Hash::make($request->password);
		}

		if (count($errors)) {
			return ['status' => false, 'errors' => $errors];
		}

		$model->save();

		return ['status' => true, 'data' => $model];
	}

    public function save($request, $username)
    {
    }

    private function person_info($user, $request)
    {
        if ($request->exists('first_name') && $request->first_name !== null && $request->first_name !== $user->first_name) {
            $user->first_name = $request->first_name;
        }

        if ($request->exists('last_name') && $request->last_name !== null && $request->last_name !== $user->last_name) {
            $user->last_name = $request->last_name;
        }

        return $user;
    }

    private function business_info($user, $request)
    {
        if ($request->exists('business_name') && $user->business_name && $request->business_name !== null && $request->business_name !== $user->business_name) {
            $user->business_name = $request->business_name;
        }

        return $user;
    }

    public function isFollow(User $user, User $followable)
    {
        return $user->follows()->where([
            'followable_id' => $followable->id,
            'followable_type' => get_class($this->model),
        ])->count();
    }

    public function isSave(User $user, User $savable)
    {
        return $user->saves()->where([
            'savable_id' => $savable->id,
            'model_id' => $this->model->model()?->id,
        ])->count();
    }

    public function checkUserPlan()
    {
        $active = $this->subscribtion
                ->where(['status' => 1])
                ->where('created_at', '<', Carbon::now())
                ->with(['plan', 'plan.club_plan_type'])
                ->get();

        foreach ($active as $subscribtion) {
            $plan_type = $subscribtion->plan->club_plan_type;
            switch ($plan_type->slug) {
                case 'monthly':
                    $ends_at = (clone $subscribtion->created_at)->addMonth()->subDay(1);
                    break;

                case 'biannual':
                    $ends_at = (clone $subscribtion->created_at)->addMonth(6)->subDay(1);
                    break;

                case 'annual':
                    $ends_at = (clone $subscribtion->created_at)->addMonth(12)->subDay(1);
                    break;

                default:
                    $ends_at = null;
                    break;
            }

            if ($ends_at && $ends_at < Carbon::today()) {
                $subscribtion->update(['status' => 0]);
            }
        }

        return true;
    }

    public function notifications()
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $notifications = $user->notifications;

        return ['status' => true, 'data' => $notifications];
    }

    public function readNotification($id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $notification = $user->notifications()->where('id', $id)->update([
            'read_at' => now(),
        ]);

        if (!$notification) {
            return ['status' => false, 'errors' => ['error' => [trans('messages.error')]]];
        }

        return ['status' => true, 'data' => []];
    }

    public function insights($request) {
        $user = $this->loggedinUser;
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $video_likes_count = $this->like->where(function ($query) use ($user, $request) {
            $query->where('likable_type', Video::class)->whereHas('likable', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

            if ($request->exists('date') && $request->date != null) {
                $query->whereDate('created_at', $request->date);
            }
        })->count();

        $video_views_count = $this->view->where(function ($query) use ($user, $request) {
            $query->where('viewable_type', Video::class)->whereHas('viewable', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

            if ($request->exists('date') && $request->date != null) {
                $query->whereDate('created_at', $request->date);
            }
        })->count();

        $video_comments_count = $this->comment->where(function ($query) use ($user, $request) {
            $query->where('commentable_type', Video::class)->whereHas('commentable', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

            if ($request->exists('date') && $request->date != null) {
                $query->whereDate('created_at', $request->date);
            }
        })->count();

        $data = [
            'video_likes_count' => $video_likes_count,
            'video_views_count' => $video_views_count,
            'video_comments_count' => $video_comments_count,
        ];
        return ['status' => true, 'data' => $data];
    }

    public function subscribtions($request) // plan_subscribtions
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $models = $this->subscribtion->where(function ($query) use ($user, $request) {
            $query->where('user_id', $user->id);
        })->with($request->with ?: [])->withCount($request->withCount ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function course_subscribtions($request)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $models = $this->course_subscribtion->where(function ($query) use ($user, $request) {
            $query->where('user_id', $user->id);
        })->with($request->with ?: [])->withCount($request->withCount ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function competition_subscribtions($request)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $models = $this->competition_subscribtion->where(function ($query) use ($user, $request) {
            $query->where('user_id', $user->id);
        })->with($request->with ?: [])->withCount($request->withCount ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function deleteAccount($request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if (!(\Hash::check($request->password, $user->password))) {
            return ['status' => false, 'errors' => ['error' => trans('validation.password')]];
        }

        $model = $this->model->find($id);
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = $model->forceDelete();
        return ['status' => true, 'data' => $model];
    }

    public function suspend($request, $id)
    {
        $user = $this->model->find($id);
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = $user->suspends()->create([
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'note' => $request->note,
        ]);

        $suspended_ids = json_decode(\Cache::get('suspended_users')) ?: [];
        array_push($suspended_ids, $user->id);
        \Cache::put('suspended_users', json_encode($suspended_ids));

        return ['status' => true, 'data' => $model];
    }
}
