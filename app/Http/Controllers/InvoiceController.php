<?php

namespace App\Http\Controllers;

use App\Models\{
    Course,
};
use Illuminate\Http\Request;
use App\Http\Repositories\{
    Invoice\InvoiceInterface,
};
use App\Services\ResponseService;
use App\Http\Resources\{
    InvoicesListResource,
    InvoiceResource
};

class InvoiceController extends Controller
{
    public $loggedinUser;

    public function __construct(public InvoiceInterface $InvoiceI, public ResponseService $responseService) 
    {
        $this->loggedinUser = app('loggedinUser');
    }

    public function invoices(Request $request)
    {
        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $request->merge([
            'user_id' => $this->loggedinUser?->id
        ]);

        $request->merge(['with' => [
            // 'invoicable',
        ], 'withCount' => [
        ]]);

        $models = $this->InvoiceI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = InvoicesListResource::collection($models['data']);
        return $this->responseService->json('Success!', ['invoices' => $data], 200, paginate: 'invoices');
    }

    public function invoice(Request $request, $id)
    {
        $request->merge(['with' => [
            // 'invoicable',
        ], 'withCount' => [
        ]]);

        $model = $this->InvoiceI->findById($id);
        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.notfound', ['model' => $this->InvoiceI?->model])]]);
        }

        $invoicable = $model->invoicable;
        switch ($invoicable) {
            case $invoicable instanceof Course:
                $user_id = $invoicable->video->user_id;
                break;
                
            default:
                $user_id = $invoicable->user_id;
                break;
        }

        if ($user_id !== $this->loggedinUser?->id) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.notfound', ['model' => $this->InvoiceI?->model])]]);
        }

        $data = new InvoiceResource($model);
        return $this->responseService->json('Success!', ['invoice' => $data], 200);
    }

    public function pay(Request $request, $id)
    {
        $pay = $this->InvoiceI->pay($id);

        if (!$pay) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$pay['status']) {
            return $this->responseService->json('Fail!', [], 400, $pay['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }
}
