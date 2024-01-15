<?php

namespace Modules\Upload\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Upload\Services\UploadService;

class UploadController extends Controller
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'file.*' => 'required',
        ]);

        $data = $this->uploadService->upload($request->file('file'));

        return responder()->success($data)->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     *
     * @throws \Throwable
     */
    public function destroy($id)
    {
        $this->uploadService->delete($id);

        return responder()->success()->respond();
    }
}
