<?php

namespace App\Modules\User\Services;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\User\Requests\SearchRequest;
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
        return $this->userRepository->getByIdWithRelations(
            Auth::id(),
            ['lists', 'lists_subscribtions']
        );
    }

    public function show(User $user)
    {
        return $this->userRepository->getByIdWithRelations(
            $user->id,
            ['lists', 'lists_subscribtions']
        );
    }

    public function search(SearchRequest $request)
    {
        return $this->userRepository->search($request->search);
    }
}
