<?php

namespace Modules\Documents\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Documents\Http\Requests\DocumentCreateRequest;
use Modules\Documents\Http\Requests\DocumentUpdateRequest;
use Modules\Documents\Models\Document;
use Modules\Documents\Services\DocumentService;
use Modules\Documents\Transformers\DocumentTransformer;

class DocumentsController extends Controller
{
    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;

        $this->authorizeResource(Document::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->documentService->getDocuments($request->all());

        return responder()->success($data, DocumentTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DocumentCreateRequest $request)
    {
        $data = $this->documentService->createDocument($request->all());

        return responder()->success($data, DocumentTransformer::class)->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Document $document)
    {
        $data = $this->documentService->getDocument($document->id);

        return responder()->success($data, DocumentTransformer::class)->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(DocumentUpdateRequest $request, Document $document)
    {
        $data = $this->documentService->editDocument($document, $request->all());

        return responder()->success($data, DocumentTransformer::class)->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Document $document)
    {
        $this->documentService->deleteDocument($document);

        return responder()->success()->respond();
    }

    public function getDocumentCategories(Request $request)
    {
        $data = $this->documentService->getDocumentCategories($request->all());

        return responder()->success($data)->respond();
    }

    public function download(Request $request, $id)
    {
        $media = $this->documentService->getDocument($id);

        return $media->getFirstMedia('document_attachment')->toResponse($request);
    }
}
