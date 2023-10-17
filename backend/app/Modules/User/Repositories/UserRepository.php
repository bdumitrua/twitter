<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\User;
use Elastic\Elasticsearch\Client as ElasticSearch;
use Elastic\ScoutDriverPlus\Support\Query;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    protected $user;
    protected $elasticSearch;

    public function __construct(
        User $user,
        ElasticSearch $elasticSearch
    ) {
        $this->user = $user;
        $this->elasticSearch = $elasticSearch;
    }

    public function getById(int $id)
    {
        return $this->user->where('id', '=', $id);
    }

    public function search(string $text): Collection
    {
        $query = Query::match()
            ->field('name')
            ->query($text)
            ->fuzziness('AUTO');

        return User::searchQuery($query)->execute()->models();
    }
}
