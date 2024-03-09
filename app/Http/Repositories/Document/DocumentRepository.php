<?php

namespace App\Http\Repositories\Document;

use App\Http\Repositories\Document\DocumentInterface;
use App\Http\Repositories\Base\BaseRepository;
use App\Models\Document;
use App\Services\FileUploader;
use Str;
use DB;

class DocumentRepository extends BaseRepository implements DocumentInterface
{
    public $loggedinUser;

    public function __construct(Document $model)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $documents = $this->model->where(function ($query) use ($request) {
        });

        [$sort, $order] = $this->setSortParams($request);
        $documents->orderBy($sort, $order);

        $documents = $request->per_page ? $documents->paginate($request->per_page) : $documents->get();

        return ['status' => true, 'data' => $documents];
    }

    public function upload($request)
    {
        $document_path = null;
        if ($request->hasFile('file')) {
            $document = FileUploader::uploadSingle($request->file('file'), 'documents');
        }

        $document = DB::transaction(function () use ($request, $document) {
            $document = $this->model->create($document);

            return $document;
        });

        if (!$document) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['document' => trans_class_basename($this->document)])]]];
        }

        return ['status' => true, 'data' => $document];
    }
}
