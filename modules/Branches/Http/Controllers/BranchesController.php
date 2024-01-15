<?php

namespace Modules\Branches\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Branches\Services\BranchService;

class BranchesController extends Controller
{
    protected $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }

    public function index(Request $request)
    {
        $data = $this->branchService->getBranches($request->all());

        return responder()->success($data)->respond();
    }
}
