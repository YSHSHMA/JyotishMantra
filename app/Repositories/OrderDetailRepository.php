<?php

namespace App\Repositories;

use App\Contracts\Repositories\OrderDetailRepositoryInterface;
use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderDetailRepository implements OrderDetailRepositoryInterface
{

    public function __construct(
        private OrderDetail $orderDetail,
    ){

    }

    public function add(array $data): string|object
    {
        return $this->orderDetail->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->orderDetail->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        // TODO: Implement getList() method.
    }

    public function getListWhere(array $orderBy = [], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->orderDetail->where($filters)->get();
    }
    public function update(string $id, array $data): bool
    {
        return $this->orderDetail->find($id)->update($data);
    }

    public function delete(array $params): bool
    {
        // TODO: Implement delete() method.
    }

    public function updateWhere(array $params, array $data): bool
    {
        return $this->orderDetail->where($params)->update($data);
    }

    public function getListWhereCount(string $searchValue = null, array $filters = [], array $relations = []): int
    {
        return $this->orderDetail->with($relations)
            ->when(isset($filters['product_id']), function ($query) use ($filters) {
                return $query->where(['product_id' => $filters['product_id']]);
            })->count();
    }
}
