<?php

namespace App\Repositories;

use App\Contracts\Repositories\DonateCategoryRepositoryInterface;
use App\Models\DonateCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class DonateCategoryRepository implements DonateCategoryRepositoryInterface
{
    public function __construct(
        private readonly DonateCategory  $donatecate,
    ) {}

    public function add(array $data): string|object
    {
        return $this->donatecate->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->donatecate->withoutGlobalScope('translate')->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->donatecate->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

    public function getListWhere(array $orderBy = [], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->donatecate->with($relations)
            ->when($searchValue, function ($query) use ($searchValue) {
                $query->where('name', 'like', "%$searchValue%")->orWhere('id', $searchValue);
            })

            ->when(isset($filters['status']), function ($query) use ($filters) {
                return $query->where('status', $filters['status']);
            })
            ->when(isset($filters['types']), function ($query) use ($filters) {
                return $query->where('type', $filters['types']);
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }

    public function update(string $id, array $data): bool
    {
        return $this->donatecate->where('id', $id)->update($data);
    }


    public function delete(array $params): bool
    {
        $this->donatecate->where($params)->delete();
        return true;
    }
}
