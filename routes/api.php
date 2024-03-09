<?php

use App\Http\Controllers\SavesForLaterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    UserController,
    StatusController,
    UserTypeController,
    PreferenceController,
    PlayerFootnessController,
    PlayerPositionController,
    AcademyLevelController,
    TrainerExperienceLevelController,

    CountryController,
    CityController,
    AcademyController,
    FederationController,
    ClubController,
    ClubPlayerController,
    ClubPresidentController,
    ClubPlanController,
    PlayerController,
    DocumentController,
    MediaController,
    CompetitionController,
    ClubAchievmentController,
    CourseController,

    SaveController,
    BlockController,
    FollowController,
    LikeController,

    VideoController,
    SoundController,
    BlogController,

    SpamController,
    CommentController,

    AcademyPresidentController,
    FederationPresidentController,
    TeamsController,

    ChatController,
    SearchController,
    InvoiceController,
    PromoteController,
    SettingsController,
    ContactMessageController,
    StickerController,
    UserSubscribtionController,
    ClubFeatureController,

    CourseSessionController,
};

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('signup', [AuthController::class, 'signup']);
    Route::post('verification-code', [AuthController::class, 'verificationCode']); //->middleware('throttle:forgetPasswordGetCode');
    Route::post('verify', [AuthController::class, 'verify']);
    Route::group(['prefix' => 'social'], function () {
        Route::post('/{social}', [AuthController::class, 'socialCheck']);
    });

    Route::group(['prefix' => 'forget-password'], function () {
        Route::post('code', [AuthController::class, 'forgetPasswordGetCode']); //->middleware('throttle:forgetPasswordGetCode');
        Route::post('check', [AuthController::class, 'forgetPasswordCheckCode']);
        Route::post('reset-password', [AuthController::class, 'forgetPasswordResetPassword']);
    });
});

/*
 * authorized routes
 */

Route::group(['middleware' => 'auth:user'], function () {

    // auth
    Route::group(['prefix' => 'auth'], function () {
        Route::group(['prefix' => 'profile'], function () {
            Route::get('', [AuthController::class, 'profile']);
            Route::patch('', [AuthController::class, 'edit']);
            Route::patch('change-password', [AuthController::class, 'changePassword']);

            // preferences
            Route::get('preferences', [AuthController::class, 'preferences']);
            Route::patch('preferences/{slug}', [AuthController::class, 'editPreference']);
        });
        Route::post('logout', [AuthController::class, 'logout']);
        Route::group(['prefix' => 'social'], function () {
            Route::post('/{social}/connect', [AuthController::class, 'socialConnect']);
            Route::post('/{social}/disconnect', [AuthController::class, 'socialDisconnect']);
        });
        Route::group(['prefix' => 'user-type'], function () {
            Route::post('', [UserController::class, 'changeUserTypeRequest']);
            Route::post('cancel', [UserController::class, 'cancelChangeUserTypeRequest']);
        });
    });

    // user
    Route::group(['prefix' => 'user'], function () {
        // lists
        Route::group(['prefix' => 'lists'], function () {
            Route::get('/saved', [SaveController::class, 'saveHistory']);
            Route::get('/blocked', [BlockController::class, 'blocks']);
            Route::get('/liked', [LikeController::class, 'likeHistory']);
            Route::get('/comments', [CommentController::class, 'commentHistory']);
            Route::get('/follow-requests', [FollowController::class, 'followRequests']);
            Route::get('/follow-requests/{username}/accept', [FollowController::class, 'acceptFollowRequests']);
            Route::get('/follow-requests/{username}/reject', [FollowController::class, 'rejectFollowRequests']);
            Route::get('/promotes', [VideoController::class, 'promotes']);
            
            // subscribtions
            Route::group(['prefix' => 'subscribtions'], function () {
                Route::get('plans', [UserSubscribtionController::class, 'subscribtions']);
                Route::get('courses', [UserSubscribtionController::class, 'course_subscribtions']);
                Route::get('competitions', [UserSubscribtionController::class, 'competition_subscribtions']);
            });
        });

        // videos
        Route::group(['prefix' => 'videos'], function () {
            Route::get('', [VideoController::class, 'videos']);
            Route::get('drafts', [VideoController::class, 'drafts']);
            Route::group(['middleware' => 'UserIsNotSuspended'], function () {
                Route::post('{id}/activate', [VideoController::class, 'activate']);
                Route::post('', [VideoController::class, 'create']);
                Route::get('{id}', [videoController::class, 'video']);
                Route::group(['middleware' => 'can:promote'], function () {
                    Route::post('/{id}/promote', [VideoController::class, 'promote']);
                });
                Route::delete('{id}', [VideoController::class, 'delete']);
            });
        });

        // notifications
        Route::group(['prefix' => 'notifications'], function () {
            Route::get('', [UserController::class, 'notifications']);
            Route::post('{id}/read', [UserController::class, 'readNotification']);
        });

        // invoices
        Route::group(['prefix' => 'invoices'], function () {
            Route::get('', [InvoiceController::class, 'invoices']);
            Route::get('{id}', [InvoiceController::class, 'invoice']);
            Route::post('{id}/pay', [InvoiceController::class, 'pay']);
        });

        // insights
        Route::get('/insights', [UserController::class, 'insights']);

        // delete-account
        Route::post('/delete-account', [UserController::class, 'deleteAccountRequest']);
    });

    // sounds
    Route::group(['prefix' => 'sounds'], function () {
        Route::get('', [SoundController::class, 'sounds'])->name('view');
        Route::get('{id}', [SoundController::class, 'sound'])->name('id');
    });

    // auth actions
    // spams
    Route::group(['prefix' => 'spams'], function () {
        Route::get('', [SpamController::class, 'spams']);
    });

    // blogs
    Route::group(['prefix' => 'blogs'], function () {
        Route::get('', [BlogController::class, 'blogs']);
        Route::get('{id}', [BlogController::class, 'blog']);
        Route::delete('comments/{id}', [BlogController::class, 'deleteComment']);

        Route::group(['middleware' => 'UserIsNotSuspended'], function () {
            Route::post('{id}/comment', [BlogController::class, 'comment']);
            Route::post('{id}/like', [BlogController::class, 'like']);
            Route::post('{id}/unlike', [BlogController::class, 'unlike']);
            Route::post('{id}/save', [BlogController::class, 'save']);
            Route::post('{id}/unsave', [BlogController::class, 'unsave']);
        });
        Route::post('{id}/view', [BlogController::class, 'view']);
    });

    // comments
    Route::group(['prefix' => 'comments'], function () {

        Route::get('{id}', [CommentController::class, 'comment']);
        Route::group(['middleware' => 'UserIsNotSuspended'], function () {
            Route::delete('{id}', [CommentController::class, 'delete']);
            Route::post('{id}/like', [CommentController::class, 'like']);
            Route::post('{id}/unlike', [CommentController::class, 'unlike']);
            Route::post('{id}/report', [CommentController::class, 'report']);
            Route::post('{id}/comment', [CommentController::class, 'comment']);
        });
    });

    // videos
    Route::group(['prefix' => 'videos'], function () {
        Route::group(['middleware' => 'UserIsNotSuspended'], function () {
            Route::post('{id}/like', [VideoController::class, 'like']);
            Route::post('{id}/unlike', [VideoController::class, 'unlike']);
            Route::post('{id}/comment', [VideoController::class, 'comment']);
            Route::post('{id}/report', [VideoController::class, 'report']);
            Route::post('/{id}/save', [VideoController::class, 'save']);
            Route::post('/{id}/unsave', [VideoController::class, 'unsave']);
        });
    });

    // plans
    Route::group(['prefix' => 'plans'], function () {
        Route::get('', [ClubPlanController::class, 'plans']);
        Route::post('{id}', [ClubPlanController::class, 'subscribe']);
    });

    // chat
    Route::group(['prefix' => 'chat'], function () {
        Route::get('', [ChatController::class, 'conversations']);
        Route::group(['middleware' => 'UserIsNotSuspended'], function () {
            Route::get('{username}', [ChatController::class, 'conversation']);
            Route::post('{username}', [ChatController::class, 'createConversation']);
            Route::post('{id}/report', [ChatController::class, 'report']);
        });
    });

    // competitions
    Route::group(['prefix' => 'competitions'], function () {
        Route::group(['middleware' => 'UserIsNotSuspended'], function () {
            Route::post('{id}/subscribe', [CompetitionController::class, 'subscribe']);
            Route::post('{id}/unsubscribe', [CompetitionController::class, 'unsubscribe']);
            Route::post('{id}/participations', [CompetitionController::class, 'participate']);
        });
    });

    // courses
    Route::group(['prefix' => 'courses'], function () {
        Route::group(['middleware' => 'UserIsNotSuspended'], function () {
            Route::post('{id}/subscribe', [CourseController::class, 'subscribe']);
            Route::post('sessions/{id}/join', [CourseSessionController::class, 'join']);
        });
    });

    // promotes
    Route::group(['prefix' => 'promotes'], function () {
        Route::get('', [PromoteController::class, 'promotes']);
    });

    // club
    Route::group(['prefix' => 'clubs'], function () {
        Route::get('{username}/club-features', [ClubFeatureController::class, 'clubFeatures']);
    });

    // GATES
    // academy
    Route::group(['middleware' => 'can:academy'], function () {
        Route::group(['prefix' => 'user'], function () {
            Route::group(['prefix' => 'lists'], function () {
                Route::get('/link-requests', [AcademyController::class, 'link_requests']);
            });
        });

        Route::group(['prefix' => 'players'], function () {
            Route::group(['middleware' => 'UserIsNotSuspended'], function () {
                Route::post('{username}/link', [PlayerController::class, 'link']);
                Route::post('{username}/unlink', [PlayerController::class, 'unlink']);
            });
        });

        Route::group(['prefix' => 'academy-president'], function () {
            Route::get('', [AcademyPresidentController::class, 'president'])->name('view');
            Route::group(['middleware' => 'UserIsNotSuspended'], function () {
                Route::post('', [AcademyPresidentController::class, 'create'])->name('create');
                Route::patch('', [AcademyPresidentController::class, 'edit'])->name('edit');
                Route::delete('', [AcademyPresidentController::class, 'delete'])->name('delete');
            });
        });
    });

    // federation
    Route::group(['middleware' => 'can:federation'], function () {
        Route::group(['prefix' => 'federation-president'], function () {
            Route::get('', [FederationPresidentController::class, 'president'])->name('view');
            Route::group(['middleware' => 'UserIsNotSuspended'], function () {
                Route::post('', [FederationPresidentController::class, 'create'])->name('create');
                Route::patch('', [FederationPresidentController::class, 'edit'])->name('edit');
                Route::delete('', [FederationPresidentController::class, 'delete'])->name('delete');
            });
        });
    });

    // player
    Route::group(['middleware' => 'can:player'], function () {
        Route::group(['prefix' => 'academies'], function () {
            Route::group(['middleware' => 'UserIsNotSuspended'], function () {
                Route::post('{username}/link', [AcademyController::class, 'link']);
            });
        });
    });

    // club
    Route::group(['middleware' => 'can:club'], function () {
        Route::group(['prefix' => 'club-president'], function () {
            Route::get('', [ClubPresidentController::class, 'president'])->name('view');
            Route::group(['middleware' => 'UserIsNotSuspended'], function () {
                Route::post('', [ClubPresidentController::class, 'create']);
                Route::patch('', [ClubPresidentController::class, 'update']);
                Route::delete('', [ClubPresidentController::class, 'delete']);
            });
        });

        Route::get('subscribers', [ClubController::class, 'subscribers']);

        Route::group(['prefix' => 'club-players'], function () {
            Route::group(['middleware' => 'UserIsNotSuspended'], function () {
                Route::post('', [ClubPlayerController::class, 'create']);
                Route::patch('{id}', [ClubPlayerController::class, 'update']);
                Route::delete('{id}', [ClubPlayerController::class, 'delete']);
            });
        });

        Route::group(['prefix' => 'competitions'], function () {
            Route::group(['middleware' => 'UserIsNotSuspended'], function () {
                Route::post('', [CompetitionController::class, 'create']);
                Route::patch('{id}', [CompetitionController::class, 'update']);
                Route::delete('{id}', [CompetitionController::class, 'delete']);
                Route::post('{id}/winner', [CompetitionController::class, 'winner']);
            });
            Route::get('{id}/subscribers', [CompetitionController::class, 'subscribers']);
            Route::get('{id}/participations', [CompetitionController::class, 'participations']);
        });

        Route::group(['prefix' => 'achievments'], function () {
            Route::group(['middleware' => 'UserIsNotSuspended'], function () {
                Route::post('', [ClubAchievmentController::class, 'create']);
                Route::patch('{id}', [ClubAchievmentController::class, 'update']);
                Route::delete('{id}', [ClubAchievmentController::class, 'delete']);
            });
        });
    });

    // trainer
    Route::group(['middleware' => 'can:trainer'], function () {
        Route::group(['prefix' => 'courses'], function () {
            Route::group(['middleware' => 'UserIsNotSuspended'], function () {
                Route::post('', [CourseController::class, 'create']);
                // Route::patch('{id}', [CourseController::class, 'update']);
                Route::delete('{id}', [CourseController::class, 'delete']);
                Route::post('{id}/live', [CourseSessionController::class, 'live']);
            });
        });
        Route::post('jwt', [CourseSessionController::class, 'jwt']);
    });
});

/*
 * unauthorized routes
 */

Route::group(['prefix' => 'users'], function () {
    Route::group(['middleware' => 'auth:user'], function () {
        Route::post('/{username}/save', [UserController::class, 'save']);
        Route::post('/{username}/unsave', [UserController::class, 'unsave']);
        Route::post('/{username}/block', [UserController::class, 'block']);
        Route::post('/{username}/unblock', [UserController::class, 'unblock']);
        Route::post('/{username}/follow', [UserController::class, 'follow']);
        Route::post('/{username}/unfollow', [UserController::class, 'unfollow']);
        Route::post('/{username}/report', [UserController::class, 'report']);
    });
    
    Route::get('/{username}/followings', [FollowController::class, 'followings']);
    Route::get('/{username}/followers', [FollowController::class, 'followers']);
    Route::get('{username}', [UserController::class, 'user']);
});

Route::group(['prefix' => 'status'], function () {
    Route::get('', [StatusController::class, 'status']);
});

Route::group(['prefix' => 'user-types'], function () {
    Route::get('', [UserTypeController::class, 'user_types']);
});

Route::group(['prefix' => 'preferences'], function () {
    Route::get('', [PreferenceController::class, 'preferences']);
});

Route::group(['prefix' => 'player-footnesses'], function () {
    Route::get('', [PlayerFootnessController::class, 'player_footnesses']);
});

Route::group(['prefix' => 'player-positions'], function () {
    Route::get('', [PlayerPositionController::class, 'player_positions']);
});

Route::group(['prefix' => 'academy-levels'], function () {
    Route::get('', [AcademyLevelController::class, 'academy_levels']);
});

Route::group(['prefix' => 'trainer-experience-levels'], function () {
    Route::get('', [TrainerExperienceLevelController::class, 'trainer_experience_levels']);
});

Route::group(['prefix' => 'countries'], function () {
    Route::get('', [CountryController::class, 'countries']);
});

Route::group(['prefix' => 'cities'], function () {
    Route::get('', [CityController::class, 'cities']);
});

Route::group(['prefix' => 'academies'], function () {
    Route::get('', [AcademyController::class, 'academies']);
    Route::get('{username}', [AcademyController::class, 'academy']);
});

Route::group(['prefix' => 'federations'], function () {
    Route::get('', [FederationController::class, 'federations']);
    Route::get('{username}', [FederationController::class, 'federation']);
});

Route::group(['prefix' => 'clubs'], function () {
    Route::get('', [ClubController::class, 'clubs']);
    Route::get('{username}', [ClubController::class, 'club']);
});

Route::group(['prefix' => 'club-players'], function () {
    Route::get('', [ClubPlayerController::class, 'clubPlayers']);
});

Route::group(['prefix' => 'players'], function () {
    Route::get('', [PlayerController::class, 'players']);
    Route::get('{username}', [PlayerController::class, 'player']);
});

Route::group(['prefix' => 'videos'], function () {
    Route::get('', [VideoController::class, 'videos']);
    Route::get('{id}', [VideoController::class, 'video']);
    Route::post('{id}/view', [VideoController::class, 'view']);
    Route::post('{id}/share', [VideoController::class, 'share']);
    Route::get('{id}/comments', [VideoController::class, 'comments']);
    Route::get('{id}/likes', [VideoController::class, 'likes']);
});

Route::group(['prefix' => 'comments'], function () {
    Route::get('{id}/comments', [CommentController::class, 'comments']);
    Route::get('{id}/likes', [CommentController::class, 'likes']);
});

Route::get('followings/videos', [VideoController::class, 'followingsVideos']);

// blogs
Route::group(['prefix' => 'blogs'], function () {
    Route::get('', [BlogController::class, 'blogs']);
    Route::get('{id}', [BlogController::class, 'blog']);
    Route::get('{id}/comments', [BlogController::class, 'comments']);
    Route::get('{id}/likes', [BlogController::class, 'likes']);
});

// achievments
Route::group(['prefix' => 'achievments'], function () {
    Route::get('', [ClubAchievmentController::class, 'achievments']);
});

// courses
Route::group(['prefix' => 'courses'], function () {
    Route::get('', [CourseController::class, 'courses']);
    Route::get('{id}', [CourseController::class, 'course']);
});

Route::post('document', [DocumentController::class, 'upload']);
Route::post('media', [MediaController::class, 'upload']);
Route::post('video', [MediaController::class, 'uploadVideo']);

// Teams
Route::group(['prefix' => 'teams'], function () {
    Route::get('', [VideoController::class, 'teams']);
});

Route::group(['prefix' => 'search'], function () {
    Route::get('', [SearchController::class, 'search']);
});

Route::group(['prefix' => 'settings'], function () {
    Route::controller(SettingsController::class)->group(function () {
        Route::get('/{slug}', 'findBySlug');
    });
});

Route::group(['prefix' => 'contact'], function () {
    Route::post('', [ContactMessageController::class, 'create'])->name('view');
});

//stickers
Route::group(['prefix' => 'stickers'], function () {
    Route::get('', [StickerController::class, 'stickers']);
});

// competitions
Route::group(['prefix' => 'competitions'], function () {
    Route::get('', [CompetitionController::class, 'competitions']);
    Route::get('{id}', [CompetitionController::class, 'competition']);
});