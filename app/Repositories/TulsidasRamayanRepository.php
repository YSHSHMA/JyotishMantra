<?php

namespace App\Repositories;

use App\Contracts\Repositories\TulsidasRamayanRepositoryInterface;
use App\Models\TulsidasRamayan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class TulsidasRamayanRepository implements TulsidasRamayanRepositoryInterface
{
    public function __construct(
        private readonly TulsidasRamayan $tulsidasramayan,
    )
    {
    }


    public function add(array $data): string|object
    {
        return $this->tulsidasramayan->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->tulsidasramayan->withoutGlobalScope('translate')->where($params)->with($relations)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->tulsidasramayan->with($relations)
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
        $query = $this->tulsidasramayan->when($searchValue, function ($query) use ($searchValue) {
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
        return $this->tulsidasramayan->where('id', $id)->update($data);
    }

    public function delete(array $params): bool
    {
        $this->tulsidasramayan->where($params)->delete();
        return true;
    }
}
