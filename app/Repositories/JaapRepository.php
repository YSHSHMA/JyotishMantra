<?php

namespace App\Repositories;

use App\Contracts\Repositories\JaapRepositoryInterface;
use App\Models\Jaap;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class JaapRepository implements JaapRepositoryInterface
{
    public function __construct(
        private readonly Jaap $jaap,
    )
    {
    }


    public function add(array $data): string|object
    {
        return $this->jaap->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->jaap->withoutGlobalScope('translate')->where($params)->with($relations)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->jaap->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }



   
    public function getListWhere(
        array      $orderBy = [],
        string     $searchValue = null,
        array      $filters = [], array $relations = [],
        int|string $dataLimit = DEFAULT_DATA_LIMIT,
        int        $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->jaap->when($searchValue, function ($query) use ($searchValue) {
                    return $query->where('name', 'like', "%$searchValue%");
                })
                ->when(!empty($orderBy), function ($query) use ($orderBy) {
                    $query->orderBy(array_key_first($orderBy),array_values($orderBy)[0]);
                });

        $filters += ['searchValue' =>$searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }

    public function update(string $id, array $data): bool
    {
        return $this->jaap->where('id', $id)->update($data);
    }

    public function delete(array $params): bool
    {
        $this->jaap->where($params)->delete();
        return true;
    }
}
