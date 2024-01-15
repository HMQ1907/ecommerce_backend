<?php

namespace Modules\Teams\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Teams\Models\Team;
use Modules\Teams\Services\TeamService;
use Modules\Teams\Transformers\TeamTransformer;
use Modules\Users\Transformers\UserTransformer;

class TeamsController extends Controller
{
    protected $teamService;

    public function __construct(TeamService $userService)
    {
        $this->teamService = $userService;
        $this->authorizeResource(Team::class);
    }

    public function index(Request $request)
    {
        $teams = $this->teamService->getAllTeams($request->all());

        return responder()->success($teams, TeamTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $this->teamService->createTeam($request->all());

        return responder()->success($data, TeamTransformer::class)->respond();
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Team $team)
    {
        $data = $this->teamService->getTeamById($team->id);

        return responder()->success($data, TeamTransformer::class)->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Team $team)
    {
        $data = $this->teamService->updateTeam($request->all(), $team->id);

        return responder()->success($data, TeamTransformer::class)->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Team $team)
    {
        $this->teamService->deleteTeam($team->id);

        return responder()->success()->respond();
    }

    public function getTeamUsers(Request $request, $teamId)
    {
        $data = $this->teamService->getTeamUsers($teamId, $request->all());

        return responder()->success($data, UserTransformer::class)->respond();
    }

    public function addTeamUsers(Request $request, $teamId)
    {
        $data = $this->teamService->addUserToTeam($teamId, $request->userIds);

        return responder()->success($data, TeamTransformer::class)->respond();
    }

    public function deleteUserInTeam($teamId, $userId)
    {
        $this->teamService->deleteUserInTeam($teamId, $userId);

        return responder()->success()->respond();
    }
}
