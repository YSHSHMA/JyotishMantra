<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface OfflinePoojaScheduleRepositoryInterface extends RepositoryInterface
{
		 /**
     * @param array $params
     * @param array $relations
     * @return Model|null
     */
    public function getFirstWhereActive(array $params, array $relations = []): ?Model;
}
