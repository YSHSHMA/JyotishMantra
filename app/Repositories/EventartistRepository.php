<?php

namespace App\Repositories;

use App\Contracts\Repositories\EventartistRepositoryInterface;
use App\Models\Eventartist;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class EventartistRepository implements EventartistRepositoryInterface
{
    public function __construct(
        private readonly Eventartist  $Eventartist,
    ) {}

    public function add(array $data): string|object
    {
        return $this->Eventartist->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->Eventartist->withoutGlobalScope('translate')->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->Eventartist->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

    public function getListWhere(array $orderBy = [], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->Eventartist->with($relations)
            ->when($searchValue, function ($query) use ($searchValue) {
                $query->where('name', 'like', "%$searchValue%")->orWhere('id', $searchValue);
            })

            ->when(isset($filters['status']), function ($query) use ($filters) {
                return $query->where('status', $filters['status']);
            })
            ->when(isset($filters['created_by']), function ($query) use ($filters) {
                return is_array($filters['created_by'])
                    ? $query->whereIn('created_by', $filters['created_by'])
                    : $query->where('created_by', $filters['created_by']);
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }

    public function update(string $id, array $data): bool
    {
        return $this->Eventartist->where('id', $id)->update($data);
    }


    public function delete(array $params): bool
    {
        $this->Eventartist->where($params)->delete();
        return true;
    }
}
