<?php

namespace Modules\Documents\Services;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Documents\Models\Document;
use Modules\Documents\Models\DocumentCategory;
use Modules\Documents\Repositories\DocumentRepository;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DocumentService extends BaseService
{
    protected $documentRepository;

    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    public function getDocuments(array $params)
    {
        return QueryBuilder::for(Document::class)
            ->allowedFilters([
                AllowedFilter::callback('q', function (Builder $query, $q) {
                    $query->where('name', 'LIKE', "%$q%");
                }),
                AllowedFilter::callback('date_between', function (Builder $query, $dateBetween) {
                    $query->whereBetween('created_at', $dateBetween);
                }),
                AllowedFilter::exact('category_id'),
                AllowedFilter::exact('branch_id'),
                AllowedFilter::exact('type'),
            ])
            ->defaultSorts('-created_at')
            ->paginate($params['limit'] ?? config('repository.pagination.limit'));
    }

    public function getDocument($id)
    {
        return $this->documentRepository->find($id);
    }

    public function createDocument(array $attrs)
    {
        try {
            DB::beginTransaction();

            $document = $this->documentRepository->create([
                'branch_id' => data_get($attrs, 'branch_id', auth()->user()->branch_id),
                'category_id' => data_get($attrs, 'category_id'),
                'type' => data_get($attrs, 'type'),
                'name' => data_get($attrs, 'name'),
                'content' => data_get($attrs, 'content'),
                'document_number' => data_get($attrs, 'document_number'),
                'issued_date' => data_get($attrs, 'issued_date'),
            ]);

            if (!empty($attrs['attachment'])) {
                $document->addMedia($attrs['attachment'])->toMediaCollection('document_attachment');
            }

            // $mediaIds = data_get($attrs, 'media_ids', '');
            // if ($mediaIds) {
            //     if (!is_array($mediaIds)) {
            //         $mediaIds = explode(',', $mediaIds);
            //     }
            // } else {
            //     $mediaIds = [];
            // }
            // $document->syncMedia($mediaIds, 'document_files');

            DB::commit();

            return $document;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function editDocument(Document $document, array $attrs)
    {
        try {
            DB::beginTransaction();

            $values = [];

            if (isset($attrs['branch_id'])) {
                $values['branch_id'] = $attrs['branch_id'];
            }

            if (isset($attrs['category_id'])) {
                $values['category_id'] = $attrs['category_id'];
            }

            if (isset($attrs['type'])) {
                $values['type'] = $attrs['type'];
            }

            if (isset($attrs['name'])) {
                $values['name'] = $attrs['name'];
            }

            if (isset($attrs['content'])) {
                $values['content'] = $attrs['content'];
            }

            if (isset($attrs['document_number'])) {
                $values['document_number'] = $attrs['document_number'];
            }

            if (isset($attrs['issued_date'])) {
                $values['issued_date'] = $attrs['issued_date'];
            }

            $data = $this->documentRepository->update($values, $document->id);

            if (!empty($attrs['attachment'])) {
                $document->clearMediaCollection('document_attachment');
                $document->addMedia($attrs['attachment'])->toMediaCollection('document_attachment');
            }

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function deleteDocument(Document $document)
    {
        try {
            DB::beginTransaction();

            $document->delete();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function getDocumentCategories(array $params)
    {
        return QueryBuilder::for(DocumentCategory::class)
            ->allowedFilters([
                AllowedFilter::callback('q', function (Builder $query, $q) {
                    $query->where('name', 'LIKE', "%$q%");
                }),
            ])
            ->defaultSorts('-created_at')
            ->paginate($params['limit'] ?? config('repository.pagination.limit'));
    }
}
