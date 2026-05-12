<?php

namespace App\Repositories;

use App\Contracts\Repositories\PackageRepositoryInterface;
use App\Models\Package;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PackageRepository implements PackageRepositoryInterface
{
    public function __construct(
        private readonly Package       $package,
    ) {
    }

    public function add(array $data): string|object
    {
        return $this->package->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->package->withoutGlobalScope('translate')->with($relations)->where($params)->first();
    }

    public function getFAQFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->package->where($params)->with($relations)->first();
    }
    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->package->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

    public function getListWhere(array $orderBy = [], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->package
            ->when(isset($filters['service_id']), function ($query) use ($filters) {
                return $query->where(['service_id' => $filters['service_id']]);
            })
            ->when(isset($filters['status']), function ($query) use ($filters) {
                return $query->where(['status' => $filters['status']]);
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }

    public function update(string $id, array $data): bool
    {
        return $this->package->where('id', $id)->update($data);
    }

    public function delete(array $params): bool
    {
        $this->package->where($params)->delete();
        return true;
    }
}