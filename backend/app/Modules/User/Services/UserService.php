<?php

namespace App\Modules\User\Services;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use Elastic\Elasticsearch\Client as ElasticSearch;
use Illuminate\Support\Facades\Auth;

class UserService
{
    protected $userRepository;

    public function __construct(
        UserRepository $userRepository,
    ) {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        return $this->userRepository->getById(Auth::id());
    }

    public function show(User $user)
    {
        return $user;
    }

    public function search(Request $request)
    {
        return $this->userRepository->search($request->text);
    }
}
