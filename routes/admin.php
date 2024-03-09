<?php

use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\CountryController;
use App\Http\Controllers\Dashboard\MainController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\{
    CityController,
    UserTypeController,
    AcademyLevelController,
    PlayerFootnessController,
    PlayerPositionController,
    TrainerExperienceLevelController,
    BlogController,
    TagController,
    SettingController,
    StatusController,
    AcademyController,
    PlayerController,
    ClubController,
    ClubPlayerController,
    ClubPresidentController,
    FederationController,
    TrainerController,
    JournalistController,
    BusinessController,
    InfluencerController,
    FanController,
    SpamSectionController,
    SpamController,
    ClubPlanController,
    SoundController,
    VideoController,
    CompetitionController,
    AcademyPlayerController,
    CourseController,
    FinancialSettingController,
    FederationPresidentController,
    CompetitionsController,
    ReportController,
    CommentController,
    PromoteController,
    AdminController,
    RoleController,
    ClubFeatureController,
    ContactMessageController,
    PermissionController,
    StatisticController,
    StickerController,
    UserController,
    AcademyPresidentController,
    DeleteAccountRequestController,
    ChangeUserTypeRequestController,
};

Route::post("/media", [MainController::class, 'media']);

Route::controller(AuthController::class)->group(function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::group(['middleware' => 'auth:admin'], function () {
            Route::post('logout', 'logout');
            Route::patch('update', 'update');
            Route::get("profile", 'profile');
        });

        Route::post('/login', 'login');

        Route::post('/forget_password/code', 'resetPassword');
        Route::post('/forget_password/check', 'pinCodeConfirmation');
        Route::post('/forget_password/reset_password', 'confirmPassword');
    });
});

Route::group(['middleware' => 'auth:admin'], function () {

    // non-permissioned routes
    Route::group(['prefix' => 'user-types', 'as' => 'user-type.'], function () {
        Route::get('', [UserTypeController::class, 'user_types'])->name('view');
    });

    Route::group(['prefix' => 'status', 'as' => 'status.'], function () {
        Route::get('', [StatusController::class, 'status'])->name('view');
    });

    // permissioned routes
    Route::group(['prefix' => 'permissions', 'as' => 'permission.'], function () {
        Route::controller(PermissionController::class)->group(function () {
            Route::get('/', 'permissions')->name('view');
        });
    });

    Route::group(['prefix' => 'countries', 'as' => 'country.'], function () {
        Route::controller(CountryController::class)->group(function () {
            Route::get('/', 'countries')->name('view');
            Route::get('/{id}', 'country')->name('id');
            Route::post('/', 'create')->name('create');
            Route::patch('/{id}', 'edit')->name('edit');
            Route::delete('/{id}', 'delete')->name('delete');
        });
    });

    Route::group(['prefix' => 'cities', 'as' => 'city.'], function () {
        Route::get('', [CityController::class, 'cities'])->name('view');
        Route::get('{id}', [CityController::class, 'city'])->name('id');
        Route::post('', [CityController::class, 'create'])->name('create');
        Route::patch('{id}', [CityController::class, 'edit'])->name('edit');
        Route::delete('{id}', [CityController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'academy-levels', 'as' => 'academy-level.'], function () {
        Route::controller(AcademyLevelController::class)->group(function () {
            Route::get('/', 'all')->name('view');
            Route::get('/{id}', 'findById')->name('id');
            Route::patch('/{id}', 'update')->name('edit');
            Route::post('/', 'create')->name('create');
            Route::delete('/{id}', 'destroy')->name('delete');
        });
    });

    Route::group(['prefix' => 'player-footnesses', 'as' => 'player-footnesse.'], function () {
        Route::controller(PlayerFootnessController::class)->group(function () {
            Route::get('/', 'all')->name('view');
            Route::get('/{id}', 'findById')->name('id');
            Route::patch('/{id}', 'update')->name('edit');
            Route::post('/', 'create')->name('create');
            Route::delete('/{id}', 'destroy')->name('delete');
        });
    });

    Route::group(['prefix' => 'player-positions', 'as' => 'player-position.'], function () {
        Route::controller(PlayerPositionController::class)->group(function () {
            Route::get('/', 'all')->name('view');
            Route::get('/{id}', 'findById')->name('id');
            Route::patch('/{id}', 'update')->name('edit');
            Route::post('/', 'create')->name('create');
            Route::delete('/{id}', 'destroy')->name('delete');
        });
    });

    Route::group(['prefix' => 'trainer-experience-levels', 'as' => 'trainer-experience-level.'], function () {
        Route::controller(TrainerExperienceLevelController::class)->group(function () {
            Route::get('/', 'all')->name('view');
            Route::get('/{id}', 'findById')->name('id');
            Route::patch('/{id}', 'update')->name('edit');
            Route::post('/', 'create')->name('create');
            Route::delete('/{id}', 'destroy')->name('delete');
        });
    });

    Route::group(['prefix' => 'blogs', 'as' => 'blog.'], function () {
        Route::controller(BlogController::class)->group(function () {
            Route::get('/', 'blogs')->name('view');
            Route::get('/{id}', 'blog')->name('id');
            Route::post('/', 'create')->name('create');
            Route::patch('/{id}', 'update')->name('edit');
            Route::delete('/{id}', 'destroy')->name('delete');
            Route::post('/{id}/activate', 'activate')->name('activate');
            Route::post('/{id}/archive', 'archive')->name('archive');
        });
    });

    Route::group(['prefix' => 'tags', 'as' => 'tag.'], function () {
        Route::controller(TagController::class)->group(function () {
            Route::get('/', 'all')->name('view');
            Route::get('/{id}', 'findById')->name('id');
            Route::post('/', 'create')->name('create');
            Route::patch('/{id}', 'update')->name('edit');
            Route::delete('/{id}', 'destroy')->name('delete');
        });
    });

    Route::group(['prefix' => 'settings', 'as' => 'setting.'], function () {
        Route::controller(SettingController::class)->group(function () {
            Route::get('/{slug}', 'findBySlug')->name('id');
            Route::patch('/{slug}', 'update')->name('edit');
        });
    });

    Route::group(['prefix' => 'users', 'as' => 'user.'], function () {
        Route::get('', [UserController::class, 'users'])->name('view');
        Route::delete('{id}', [UserController::class, 'delete'])->name('delete');
        Route::post('{id}/suspend', [UserController::class, 'suspend'])->name('suspend');
    });

    Route::group(['prefix' => 'change-type-requests', 'as' => 'change-type-request.'], function () {
        // Route::get('', [UserController::class, 'users'])->name('view');
        // Route::delete('{id}', [UserController::class, 'delete'])->name('delete');
        Route::post('{id}/accept', [ChangeUserTypeRequestController::class, 'accept'])->name('accept');
    });

    Route::group(['prefix' => 'delete-account-requests', 'as' => 'delete-account-request.'], function () {
        Route::get('', [DeleteAccountRequestController::class, 'deleteAccountRequests'])->name('delete_requests.view');
        Route::post('{id}/read', [DeleteAccountRequestController::class, 'read'])->name('delete_requests.read');
        Route::post('{id}/unread', [DeleteAccountRequestController::class, 'unread'])->name('delete_requests.unread');
    });

    Route::group(['prefix' => 'academies', 'as' => 'academy.'], function () {
        Route::get('', [AcademyController::class, 'academies'])->name('view');
        Route::get('{id}', [AcademyController::class, 'academy'])->name('id');
        Route::post('{id}/accept', [AcademyController::class, 'accept'])->name('accept');
        Route::post('{id}/reject', [AcademyController::class, 'reject'])->name('reject');
        Route::post('{id}/block', [AcademyController::class, 'block'])->name('block');
        Route::post('{id}/unblock', [AcademyController::class, 'unblock'])->name('unblock');
        Route::get('{id}/overview', [AcademyController::class, 'overview'])->name('overview');
        Route::get('{id}/academy-president', [AcademyPresidentController::class, 'president']);
    });

     Route::group(['prefix' => 'academy-president'], function () {
            Route::get('', [AcademyPresidentController::class, 'president'])->name('view');
            Route::post('', [AcademyPresidentController::class, 'create'])->name('create');
            Route::patch('', [AcademyPresidentController::class, 'edit'])->name('edit');
            Route::delete('', [AcademyPresidentController::class, 'delete'])->name('delete');
        });

    Route::group(['prefix' => 'players', 'as' => 'player.'], function () {
        Route::get('', [PlayerController::class, 'players'])->name('view');
        Route::get('{id}', [PlayerController::class, 'player'])->name('id');
        Route::post('{id}/accept', [PlayerController::class, 'accept'])->name('accept');
        Route::post('{id}/reject', [PlayerController::class, 'reject'])->name('reject');
        Route::post('{id}/block', [PlayerController::class, 'block'])->name('block');
        Route::post('{id}/unblock', [PlayerController::class, 'unblock'])->name('unblock');
        Route::get('{id}/overview', [PlayerController::class, 'overview'])->name('overview');
    });

    Route::group(['prefix' => 'clubs', 'as' => 'club.'], function () {
        Route::get('', [ClubController::class, 'clubs'])->name('view');
        Route::get('{id}', [ClubController::class, 'club'])->name('id');
        Route::get('{id}/president', [ClubPresidentController::class, 'club_president'])->name('club-president.view');
        Route::post('{id}/president', [ClubPresidentController::class, 'create'])->name('club-president.create');
        Route::post('', [ClubController::class, 'create'])->name('create');
        Route::patch('{id}', [ClubController::class, 'edit'])->name('edit');
        Route::patch('{id}/president', [ClubPresidentController::class, 'edit'])->name('club-president.edit');
        Route::post('{id}/activate', [ClubController::class, 'activate'])->name('activate');
        Route::post('{id}/block', [ClubController::class, 'block'])->name('block');
        Route::post('{id}/unblock', [ClubController::class, 'unblock'])->name('unblock');
        Route::delete('{id}/president', [ClubPresidentController::class, 'delete'])->name('club-president.delete');
        Route::get('{id}/overview', [ClubController::class, 'overview'])->name('overview');
        Route::get('competitions', [CompetitionsController::class, 'competitions'])->name('competition.view');

        // club-features
        Route::get('{id}/club-features', [ClubFeatureController::class, 'clubFeatures'])->name('club-feature.view');
        Route::patch('{id}/club-features', [ClubFeatureController::class, 'update'])->name('club-feature.edit');
    });

    Route::group(['prefix' => 'club-players', 'as' => 'club-player.'], function () {
        Route::get('', [ClubPlayerController::class, 'club_players'])->name('view');
        Route::get('{id}', [ClubPlayerController::class, 'club_player'])->name('id');
        Route::post('', [ClubPlayerController::class, 'create'])->name('create');
        Route::patch('{id}', [ClubPlayerController::class, 'edit'])->name('edit');
        Route::delete('{id}', [ClubPlayerController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'federations', 'as' => 'federation.'], function () {
        Route::get('', [FederationController::class, 'federations'])->name('view');
        Route::get('{id}', [FederationController::class, 'federation'])->name('id');
        Route::get('{id}/president', [FederationPresidentController::class, 'federation_president'])->name('federation-president.view');
        Route::post('{id}/president', [FederationPresidentController::class, 'create'])->name('federation-president.create');
        Route::patch('{id}/president', [FederationPresidentController::class, 'edit'])->name('federation-president.edit');
        Route::post('', [FederationController::class, 'create'])->name('create');
        Route::patch('{id}', [FederationController::class, 'edit'])->name('edit');
        Route::post('{id}/activate', [FederationController::class, 'activate'])->name('activate');
        Route::post('{id}/block', [FederationController::class, 'block'])->name('block');
        Route::post('{id}/unblock', [FederationController::class, 'unblock'])->name('unblock');
        Route::get('{id}/overview', [FederationController::class, 'overview'])->name('overview');
        Route::delete('{id}/president', [FederationPresidentController::class, 'delete'])->name('federation-president.delete');
    });

    Route::group(['prefix' => 'trainers', 'as' => 'trainer.'], function () {
        Route::get('', [TrainerController::class, 'trainers'])->name('view');
        Route::get('{id}', [TrainerController::class, 'trainer'])->name('id');
        Route::post('{id}/accept', [TrainerController::class, 'accept'])->name('accept');
        Route::post('{id}/reject', [TrainerController::class, 'reject'])->name('reject');
        Route::post('{id}/block', [TrainerController::class, 'block'])->name('block');
        Route::post('{id}/unblock', [TrainerController::class, 'unblock'])->name('unblock');
        Route::get('{id}/overview', [TrainerController::class, 'overview'])->name('overview');
    });

    Route::group(['prefix' => 'journalists', 'as' => 'journalist.'], function () {
        Route::get('', [JournalistController::class, 'journalists'])->name('view');
        Route::get('{id}', [JournalistController::class, 'journalist'])->name('id');
        Route::post('{id}/accept', [JournalistController::class, 'accept'])->name('accept');
        Route::post('{id}/reject', [JournalistController::class, 'reject'])->name('reject');
        Route::post('{id}/block', [JournalistController::class, 'block'])->name('block');
        Route::post('{id}/unblock', [JournalistController::class, 'unblock'])->name('unblock');
        Route::get('{id}/overview', [JournalistController::class, 'overview'])->name('overview');
    });

    Route::group(['prefix' => 'businesses', 'as' => 'business.'], function () {
        Route::get('', [BusinessController::class, 'businesses'])->name('view');
        Route::get('{id}', [BusinessController::class, 'business'])->name('id');
        Route::post('{id}/accept', [BusinessController::class, 'accept'])->name('accept');
        Route::post('{id}/reject', [BusinessController::class, 'reject'])->name('reject');
        Route::post('{id}/block', [BusinessController::class, 'block'])->name('block');
        Route::post('{id}/unblock', [BusinessController::class, 'unblock'])->name('unblock');
        Route::get('{id}/overview', [BusinessController::class, 'overview'])->name('overview');
    });

    Route::group(['prefix' => 'influencers', 'as' => 'influencer.'], function () {
        Route::get('', [InfluencerController::class, 'influencers'])->name('view');
        Route::get('{id}', [InfluencerController::class, 'influencer'])->name('id');
        Route::post('{id}/accept', [InfluencerController::class, 'accept'])->name('accept');
        Route::post('{id}/reject', [InfluencerController::class, 'reject'])->name('reject');
        Route::post('{id}/block', [InfluencerController::class, 'block'])->name('block');
        Route::post('{id}/unblock', [InfluencerController::class, 'unblock'])->name('unblock');
        Route::get('{id}/overview', [InfluencerController::class, 'overview'])->name('overview');
    });

    Route::group(['prefix' => 'fans', 'as' => 'fan.'], function () {
        Route::get('', [FanController::class, 'fans'])->name('view');
        Route::get('{id}', [FanController::class, 'fan'])->name('id');
        Route::post('{id}/block', [FanController::class, 'block'])->name('block');
        Route::post('{id}/unblock', [FanController::class, 'unblock'])->name('unblock');
        Route::get('{id}/overview', [FanController::class, 'overview'])->name('overview');
    });

    Route::group(['prefix' => 'spam-sections', 'as' => 'spam-section.'], function () {
        Route::get('', [SpamSectionController::class, 'spam_sections'])->name('view');
    });

    Route::group(['prefix' => 'spams', 'as' => 'spam.'], function () {
        Route::get('', [SpamController::class, 'spams'])->name('view');
        Route::get('{id}', [SpamController::class, 'spam'])->name('id');
        Route::post('', [SpamController::class, 'create'])->name('create');
        Route::patch('{id}', [SpamController::class, 'edit'])->name('edit');
        Route::delete('{id}', [SpamController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'plans', 'as' => 'plan.'], function () {
        Route::get('', [ClubPlanController::class, 'plans'])->name('view');
        Route::get('{id}', [ClubPlanController::class, 'plan'])->name('id');
        Route::post('', [ClubPlanController::class, 'create'])->name('create');
        Route::patch('', [ClubPlanController::class, 'edit'])->name('edit');
        Route::delete('{id}', [ClubPlanController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'reports', 'as' => 'report.'], function () {
        Route::get('', [ReportController::class, 'reports'])->name('view');
        Route::get('{report}', [ReportController::class, 'report'])->name('id');
        Route::post('{report}/read', [ReportController::class, 'read'])->name('read');
        Route::post('{report}/unread', [ReportController::class, 'unread'])->name('unread');
        Route::delete('{report}', [ReportController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'sounds', 'as' => 'sound.'], function () {
        Route::get('', [SoundController::class, 'sounds'])->name('view');
        Route::get('{id}', [SoundController::class, 'sound'])->name('id');
        Route::post('', [SoundController::class, 'create'])->name('create');
        Route::patch('{id}', [SoundController::class, 'edit'])->name('edit');
        Route::delete('{id}', [SoundController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'videos', 'as' => 'video.'], function () {
        Route::get('', [VideoController::class, 'videos'])->name('view');
        Route::delete('{id}', [VideoController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'competitions', 'as' => 'competition.'], function () {
        Route::get('', [CompetitionController::class, 'competitions'])->name('view');
    });

    // TODO:
    Route::group(['prefix' => 'academy-players', 'as' => 'academy-player.'], function () {
        Route::get('', [AcademyPlayerController::class, 'players'])->name('view');
    });

    Route::group(['prefix' => 'courses', 'as' => 'course.'], function () {
        Route::get('', [CourseController::class, 'courses'])->name('view');
        Route::get('{id}', [CourseController::class, 'course'])->name('id');
        Route::post('{id}/accept', [CourseController::class, 'accept'])->name('accept');
        Route::delete('{id}', [CourseController::class, 'delete'])->name('delete');
        Route::post('{id}/reject', [CourseController::class, 'reject'])->name('reject');
        Route::get('{id}/subscribtions', [CourseController::class, 'subscribtions'])->name('subscribtions.view');
    });

    Route::group(['prefix' => 'comments', 'as' => 'comment.'], function () {
        Route::get('', [CommentController::class, 'comments'])->name('view');
        Route::get('{id}', [CommentController::class, 'commetn'])->name('id'); // TODO:
        Route::delete('{id}', [CommentController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'financial-settings', 'as' => 'financial-setting.'], function () {
        Route::get('', [FinancialSettingController::class, 'financialSettings'])->name('view');
        Route::patch('{slug}', [FinancialSettingController::class, 'edit'])->name('edit');
    });

    Route::group(['prefix' => 'promotes', 'as' => 'promote.'], function () {
        Route::get('', [PromoteController::class, 'promotes'])->name('view');
        Route::get('{id}', [PromoteController::class, 'promote'])->name('id');
    });

    Route::group(['prefix' => 'admins', 'as' => 'admin.'], function () {
        Route::get('', [AdminController::class, 'admins'])->name('view');
        Route::get('{id}', [AdminController::class, 'admin'])->name('id');
        Route::post('', [AdminController::class, 'create'])->name('create');
        Route::patch('{id}', [AdminController::class, 'edit'])->name('edit');
        Route::delete('{id}', [AdminController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'roles', 'as' => 'role.'], function () {
        Route::get('', [RoleController::class, 'roles'])->name('view');
        Route::get('{id}', [RoleController::class, 'role'])->name('id');
        Route::post('', [RoleController::class, 'create'])->name('create');
        Route::patch('{id}', [RoleController::class, 'edit'])->name('edit');
        Route::delete('{id}', [RoleController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'contact', 'as' => 'contact.'], function () {
        Route::get('', [ContactMessageController::class, 'messages'])->name('view');
        Route::post('{id}/read', [ContactMessageController::class, 'read'])->name('read');
        Route::post('{id}/unread', [ContactMessageController::class, 'unread'])->name('unread');
        Route::delete('{id}/delete', [ContactMessageController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'overview'], function () {
        Route::get('', [StatisticController::class, 'overview'])->name('view');
        Route::post('{id}/read', [StatisticController::class, 'read'])->name('read');
        Route::post('{id}/unread', [StatisticController::class, 'unread'])->name('unread');
    });

    Route::group(['prefix' => 'stickers', 'as' => 'sticker.'], function () {
        Route::get('', [StickerController::class, 'stickers'])->name('view');
        Route::post('', [StickerController::class, 'create'])->name('create');
        Route::delete('{id}', [StickerController::class, 'delete'])->name('delete');
    });
});

