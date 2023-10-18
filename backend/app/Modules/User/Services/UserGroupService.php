<?php

namespace App\Modules\User\Services;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Repositories\UserGroupRepository;
use Illuminate\Support\Facades\Auth;

class UserGroupService
{
    protected $userGroupRepository;

    public function __construct(
        UserGroupRepository $userGroupRepository,
    ) {
        $this->userGroupRepository = $userGroupRepository;
    }

    public function index()
    {
        // 
    }
    public function create()
    {
        // 
    }
    public function update(UserGroup $userGroup)
    {
        // 
    }
    public function destroy(UserGroup $userGroup)
    {
        // 
    }
    public function add(User $user)
    {
        // 
    }
    public function remove(User $user)
    {
        // 
    }
}
