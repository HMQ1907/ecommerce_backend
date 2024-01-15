<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Auth\Transformers\UserTransformer;

class UserController extends Controller
{
    /**
     * Get authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        $user = $request->user();

        return responder()->success($user, UserTransformer::class)->respond();
    }
}
