<?php

namespace App\Modules\User\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\User\Models\User;
use App\Modules\User\Requests\SearchRequest;
use App\Modules\User\Services\UsersListService;

class UsersListController extends Controller
{
    private $usersListService;

    public function __construct(UsersListService $usersListService)
    {
        $this->usersListService = $usersListService;
    }
}
