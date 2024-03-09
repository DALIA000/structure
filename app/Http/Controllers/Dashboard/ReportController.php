<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Report\ReportInterface;
use App\Http\Resources\Dashboard\ReportResource;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private $responseService;

    public function __construct(private ReportInterface $ReportI, ResponseService $responseService)
    {
        $this->ReportI = $ReportI;
        $this->responseService = $responseService;
    }

    public function reports(Request $request){
        if(!$request->exists('order') || $request->order == null){
            $request->merge(['order' => 'desc']);
        }

        if(!$request->exists('sort') || $request->sort == null){
            $request->merge(['sort' =>'updated_at']);
        }

        if(!$request->exists('country_slug') || $request->country_slug == null){
            $request->merge(['country_slug' => app('country')]);
        }

        $request->merge(['with' => [
            'reportable' => function ($query) {
                $query->withTrashed();
            }
        ]]);

        $report = $this->ReportI->models($request);

        if(!$report){
            return $this->responseService->json('fail', [], 400, ['error' => [trans('message.error')]]);
        }

        if(!$report['status']){
            return $this->responseService->json('Fail!', [], 400, $report['errors']);
        }

        $data = ReportResource::collection($report['data']);
        return $this->responseService->json('success', $data, 200);
    }

    public function read(Request $request, $id)
    {
        $model = $this->ReportI->read($id);

        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$model['status']) {
            return $this->responseService->json('Fail!', [], 400, $model['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function unread(Request $request, $id)
    {
        $model = $this->ReportI->unread($id);

        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$model['status']) {
            return $this->responseService->json('Fail!', [], 400, $model['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function delete(Request $request, $id)
    {
        $model = $this->ReportI->delete($id);

        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$model['status']) {
            return $this->responseService->json('Fail!', [], 400, $model['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }
}
