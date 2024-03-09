<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

use App\Events\{
  UserRegistered,
  UserVerification,
  UserAccountVerified,
  ForgetPasswordCodeGenerated,

  UserAccepted,
  UserRejected,
  MessageSent,

  UserLikedVideo,
  UserCommented,
  UserTaged,
  UserSubscribedCourse,
  UserFollowed,
  UserBlocked,

  CompetitionCreated,
  VideoCreated,
  CourseAccepted,
  DeleteReportable,
  
  SessionLiveSatrted,
};

// users listeners
use App\Listeners\{
  SendVerificationCodeNotification,
  SendUserAccountVerifiedNotification,
  SendForgetPasswordCodeNotification,

  SendMessageNotification,

  SendNotification,
  ParseVideoHashtags,
  SendMailNotification,
  SetBlockedkUserRestrictions,
  SendDeleteReportableVotification,
};

use App\Models\UserPreference;
use App\Observers\UserPreferenceObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        UserRegistered::class => [
            SendVerificationCodeNotification::class,
        ],

        UserVerification::class => [
            SendVerificationCodeNotification::class,
        ],

        UserAccountVerified::class => [
            SendUserAccountVerifiedNotification::class,
        ],

        ForgetPasswordCodeGenerated::class => [
            SendForgetPasswordCodeNotification::class,
        ],

        UserAccepted::class => [
            SendNotification::class,
        ],

        UserRejected::class => [
            SendNotification::class,
        ],

        /* MessageSent::class => [
            SendMessageNotification::class,
        ], */

        UserLikedVideo::class => [
            SendNotification::class,
            SendMailNotification::class,
        ],

        UserCommented::class => [
            SendNotification::class,
            SendMailNotification::class,
        ],

        UserTaged::class => [
            SendNotification::class,
            SendMailNotification::class,
        ],

        UserSubscribedCourse::class => [
            SendNotification::class,
            SendMailNotification::class,
        ],

        UserFollowed::class => [
            SendNotification::class,
            SendMailNotification::class,
        ],

        UserBlocked::class => [
            SetBlockedkUserRestrictions::class,
        ],

        CompetitionCreated::class => [
            SendNotification::class,
            SendMailNotification::class,
        ],

        VideoCreated::class => [
            ParseVideoHashtags::class,
        ],

        CourseAccepted::class => [
            SendNotification::class,
            SendMailNotification::class
        ],

        DeleteReportable::class => [
            SendNotification::class,
        ],

        SessionLiveSatrted::class => [
            SendNotification::class,
            SendMailNotification::class
        ]
    ];

    /**
     * The model to observer mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $observers = [
        UserPreference::class => [UserPreferenceObserver::class],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
