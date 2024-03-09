<?php
namespace App\Http\Repositories\ContactMessage;
use App\Http\Repositories\Base\BaseRepository;
use App\Models\ContactMessage;
use App\Http\Repositories\ContactMessage\ContactMessageInterface;

class ContactMessageRepository extends BaseRepository implements ContactMessageInterface{
    public function __construct(ContactMessage  $model)
    {
        $this->model = $model;
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {

            if ($request->exists('search')) {
                $query->where(function ($query) use ($request) {
                    $query->where('title', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%")
                        ->orWhere('phone', 'like', "%{$request->search}%");
                });
            }

            if ($request->exists('date_from') && $request->date_from !== null) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->exists('date_to') && $request->date_to !== null) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            if ($request->exists('status') && $request->status !== null) {
                switch ($request->status) {
                    case '0':
                        $query->where('read_at', null);
                        break;

                    case '1':
                        $query->where('read_at', '!=',null);
                        break;

                    default:
                        # code...
                        break;
                }
            }
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

    public function create($request)
    {
        $message = $this->model::create([
            'title' => $request->title,
            'phone' => $request->phone,
            'email' => $request->email,
            'message' => $request->message,
        ]);

        if (!$message) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        return ['status' => true, 'data' => $message];
    }

    public function read($id)
    {
        $model = $this->findById($id);
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model->update([
            'read_at' => now()
        ]);

        return ['status' => true, 'data' => []];
    }

    public function unread($id)
    {
        $model = $this->findById($id);
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model->update([
            'read_at' => null,
        ]);

        return ['status' => true, 'data' => []];
    }
}
