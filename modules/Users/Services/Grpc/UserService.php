<?php

namespace Modules\Users\Services\Grpc;

use App\Models\User as UserModel;
use Proto\UserService\Error;
use Proto\UserService\User;
use Proto\UserService\UserByEmailRequest;
use Proto\UserService\UserByIdRequest;
use Proto\UserService\UserByIdsRequest;
use Proto\UserService\UserResponse;
use Proto\UserService\Users;
use Proto\UserService\UserServiceInterface;
use Proto\UserService\UsersResponse;
use Spiral\RoadRunner\GRPC;

class UserService implements UserServiceInterface
{
    public function getUserByIds(GRPC\ContextInterface $ctx, UserByIdsRequest $in): UsersResponse
    {
        $ids = $in->getIds();
        $response = new UsersResponse();

        $queryIds = [];
        foreach ($ids as $id) {
            $queryIds[] = $id;
        }

        try {
            $items = UserModel::whereIn('id', $queryIds)->get();

            $users = [];

            foreach ($items as $item) {
                $user = new User();
                $user->setId($item->id);
                $user->setName($item->name);
                $user->setEmail($item->email);
                $user->setAvatarUrl((string) $item->avatar_url);
                $user->setPhone((string) $item->employee->phone);
                $user->setCreatedAt((string) $item->created_at);
                $user->setUpdatedAt((string) $item->updated_at);

                $users[] = $user;
            }

            $response->setData((new Users())->setUsers($users));
        } catch (\Exception $e) {
            $error = new Error();
            $error->setCode($e->getCode())->setMessage($e->getMessage());

            $response->setError($error);
        }

        return $response;
    }

    public function getUserById(GRPC\ContextInterface $ctx, UserByIdRequest $in): UserResponse
    {
        $id = $in->getId();
        $response = new UserResponse();

        try {
            $userModel = UserModel::find($id);

            $user = new User();
            $user->setId($userModel->id);
            $user->setName($userModel->name);
            $user->setEmail($userModel->email);
            $user->setAvatarUrl((string) $userModel->avatar_url);
            $user->setPhone((string) $userModel->employee->phone);
            $user->setCreatedAt((string) $userModel->created_at);
            $user->setUpdatedAt((string) $userModel->updated_at);

            $response->setData($user);
        } catch (\Exception $e) {
            $error = new Error();
            $error->setCode($e->getCode())->setMessage($e->getMessage());

            $response->setError($error);
        }

        return $response;
    }

    public function getUsersEmail(GRPC\ContextInterface $ctx, UserByEmailRequest $in): UsersResponse
    {
        $email = $in->getEmail();
        $response = new UsersResponse();

        try {
            $userModel = UserModel::where('email', $email)->firstOrFail();

            $user = new User();
            $user->setId($userModel->id);
            $user->setName($userModel->name);
            $user->setEmail($userModel->email);
            $user->setAvatarUrl((string) $userModel->avatar_url);
            $user->setPhone((string) $userModel->employee->phone);
            $user->setCreatedAt((string) $userModel->created_at);
            $user->setUpdatedAt((string) $userModel->updated_at);

            $response->setData((new Users())->setUsers([$user]));
        } catch (\Exception $e) {
            $error = new Error();
            $error->setCode($e->getCode())->setMessage($e->getMessage());

            $response->setError($error);
        }

        return $response;
    }
}
