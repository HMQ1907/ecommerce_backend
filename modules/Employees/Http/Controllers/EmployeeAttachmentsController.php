<?php

namespace Modules\Employees\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Employees\Services\EmployeeService;
use Modules\Employees\Transformers\AttachmentEmployeeTransformer;

class EmployeeAttachmentsController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($employeeId)
    {
        $attachments = $this->employeeService->getAttachments($employeeId);

        return responder()->success($attachments, AttachmentEmployeeTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $employeeId)
    {
        $this->employeeService->storeAttachment($employeeId, $request->all());

        return responder()->success()->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $employeeId, $id)
    {
        $this->employeeService->updateAttachment($employeeId, $id, $request->all());

        return responder()->success()->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($employeeId, $id)
    {
        $this->employeeService->deleteAttachment($id);

        return responder()->success()->respond();
    }

    public function download($employeeId, $id)
    {
        $media = $this->employeeService->getAttachment($id);

        return response()->streamDownload(
            function () use ($media) {
                $stream = $media->stream();
                while ($bytes = $stream->read(1024)) {
                    echo $bytes;
                }
            },
            $media->filename.'.'.$media->extension,
            [
                'Content-Type' => $media->mime_type,
                'Content-Length' => $media->size,
            ]
        );
    }
}
