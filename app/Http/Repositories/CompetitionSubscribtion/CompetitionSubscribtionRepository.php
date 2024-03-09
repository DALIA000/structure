<?php

namespace App\Http\Repositories\CompetitionSubscribtion;

use App\Http\Repositories\{
    Base\BaseRepository,
    User\UserInterface,
};
use App\Models\{
    CompetitionSubscribtion,
};

class CompetitionSubscribtionRepository extends BaseRepository implements CompetitionSubscribtionInterface
{
    public $loggedinUser;

    public function __construct(CompetitionSubscribtion $model, public UserInterface $UserI)
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

            if ($request->exists('status_id') && $request->status_id !== null) {
                $query->where('status_id', $request->status_id);
            }

            if($request->exists('competition_id') && $request->competition_id !== null){
                $query->where('competition_id', $request->competition_id);
            }

            if($request->exists('has_right_answer') && $request->has_right_answer !== null){
                $query->where(function ($query) use ($request) {
                    $query->whereHas('user.user_competition_options.option', function ($query) use ($request) {
                        $query->where('is_right_option', 1)
                            ->whereHas('competition', function ($query) use ($request) {
                                $query->where('id', $request->competition_id)
                                    ->where('style', 2);
                            });
                    })->orWhereHas('competition', function ($query) use ($request) {
                        $query->where('id', $request->competition_id)
                            ->where('style', 1);
                    });
                });
            }

            if($request->exists('has_wrong_answer') && $request->has_wrong_answer !== null){
                $query->where(function ($query) use ($request) {
                    $query->whereHas('user.user_competition_options.option', function ($query) use ($request) {
                        $query->where('is_right_option', 0)
                            ->whereHas('competition', function ($query) use ($request) {
                                $query->where('id', $request->competition_id)
                                    ->where('style', 2);
                            });
                    })->orWhereHas('competition', function ($query) use ($request) {
                        $query->where('id', $request->competition_id)
                            ->where('style', 1);
                    });
                });
            }

            // check if user is allowed to view
            if ($request->exists('subscriber_id') && $request->subscriber_id !== null) {
                $query->where(function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query->whereHas('competition', function ($query) use ($request) {
                            $query->where('type', 0);
                            $query->whereHas('club.subscribers', function ($query) use ($request) {
                                $query->where('user_id', $request->subscriber_id);
                            });
                        });
                    })->orWhere(function ($query) use ($request) {
                        $query->whereHas('competition', function ($query) use ($request) {
                            $query->where('type', 1);
                        });
                    })->orWhere(function ($query) use ($request) {
                        $query->whereHas('competition', function ($query) use ($request) {
                            $query->where('user_id', $request->subscriber_id);
                        });
                    });
                });
            }
        });

        // prevent blocked accounts
        /* $models->whereNot(function ($query) use ($request) {
            $query->whereHas('user.blocks', function ($query) use ($request) {
                $query->where('blockable_id', $this->loggedinUser?->id);
            })->orWhereHas('user.blocked', function ($query) use ($request) {
                $query->where('user_id', $this->loggedinUser?->id);
            });
        }); */

        $models = $models->with($request->with ?: [])->withCount($request->withCount ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }
}
