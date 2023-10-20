<?php

namespace App\Modules\Twitt\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Twitt\Models\Twitt;
use App\Modules\Twitt\Requests\TwittRequest;
use App\Modules\Twitt\Services\TwittService;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;

class TwittController extends Controller
{
    private $twittService;

    public function __construct(TwittService $twittService)
    {
        $this->twittService = $twittService;
    }

    public function index()
    {
        return $this->handleServiceCall(function () {
            return $this->twittService->index();
        });
    }

    public function show(Twitt $twitt)
    {
        return $this->handleServiceCall(function () use ($twitt) {
            return $this->twittService->show($twitt);
        });
    }

    public function user(User $user)
    {
        return $this->handleServiceCall(function () use ($user) {
            return $this->twittService->user($user);
        });
    }

    public function list(UsersList $usersList)
    {
        return $this->handleServiceCall(function () use ($usersList) {
            return $this->twittService->list($usersList);
        });
    }

    public function create(TwittRequest $twittRequest)
    {
        return $this->handleServiceCall(function () use ($twittRequest) {
            return $this->twittService->create($twittRequest);
        });
    }

    public function destroy(Twitt $twitt)
    {
        return $this->handleServiceCall(function () use ($twitt) {
            return $this->twittService->destroy($twitt);
        });
    }
}
