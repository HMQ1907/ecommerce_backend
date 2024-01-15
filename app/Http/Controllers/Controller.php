<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function paginate(Collection $collect, $limit, $page)
    {
        $offset = ($page * $limit) - $limit;
        $itemsForCurrentPage = $collect->slice($offset, $limit)->all();

        return new LengthAwarePaginator($itemsForCurrentPage, count($collect), $limit, $page);
    }

    protected function bufferDownload($buffer, $name = null, array $headers = [], $disposition = 'attachment')
    {
        $headers = [
            'Content-type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$name.'"',
            'Cache-Control' => 'public, must-revalidate, max-age=0',
            'Pragma' => 'public',
            'Expires' => 'Sat, 26 Jul 1997 05:00:00 GMT',
            'Last-Modified' => gmdate('D, d M Y H:i:s').' GMT',
        ];

        return response()->make($buffer, Response::HTTP_OK, $headers);
    }
}
