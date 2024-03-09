<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\File;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;
        $social_auth = $this->social_auth;
        $file = $this->files && $this->files[0] ? $this->files[0] : null;

        $data = [
            'username' => $this->username,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'phone' => $this->phone,
            'phone_verified_at' => $this->phone_verified_at,
            'country' => $this->city?->country?->locale?->name,
            'city' => $this->city?->locale?->name,
            'birthday' => $this->birthday,
            'club' => new ClubsListResource($this->club),
            'user_type' => new UserTypeResource($this->resource_user_type),
            'user_status' => new StatusResource($user->status),
            'bio' => $this->bio,
            "social_connected_facebook" => (bool) $social_auth?->facebook,
            "social_connected_google" => (bool) $social_auth?->google,
            "social_connected_twitter" => (bool) $social_auth?->twitter,
            "social_connected_apple" => (bool) $social_auth?->apple,
            'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
            'followings_count' => $this->follows_count,
            'followers_count' => $this->followers_count,
            'videos_count' => $this->videos_count,
            'account' => $this->account_is_public,
            'subscribtion' => new ClubPlanResource($this->active_subscribtion?->plan),
            'notifications_count' => $this->notifications_count,
            'has_delete_account_request' => (bool) $user->delete_account_requests()->count(),
        ];

        if ($user->business_name !== null) {
            $data['business_name'] = $user->business_name;
        }else{
            $data['first_name'] = $user->first_name;
            $data['last_name'] = $user->last_name;
        }

        switch($this->user_type->slug) {
            case 'trainer':
                $data['certifications'] = [
                    'training_certification' => new CertificationResource($this->certification?->training_certification),
                    'experience_certification' => new CertificationResource($this->certification?->experience_certification),
                ];
                break;
            case 'journalist':
                $data['certifications'] = [
                    'journalisment_certification' => new CertificationResource($this->certification?->journalisment_certification),
                ];
                break;
            case 'academy':
                $data['certifications'] = [
                    'commercial_certification' => new CertificationResource($this->certification?->commercial_certification),
                ];
                break;
            case 'influencer':
                $data['certifications'] = [
                    'influencement_certification' => new CertificationResource($this->certification?->influencement_certification),
                ];
                break;
            case 'business':
                $data['certifications'] = [
                    'commercial_certification' => new CertificationResource($this->certification?->commercial_certification),
                ];
                break;
            
            default:
                $data['certifications'] = [];
                break;
        }

        return $data;
    }
}
