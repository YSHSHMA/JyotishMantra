<?php

namespace App\Repositories;

use App\Contracts\Repositories\RestaurantReviewRepositoryInterface;
use App\Models\RestaurantReview;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class RestaurantReviewRepository implements RestaurantReviewRepositoryInterface
{
    public function __construct(
        private readonly RestaurantReview $restaur,
    )
    {
    }

    public function add(array $data): string|object
    {
        return $this->restaur->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->restaur->withoutGlobalScope('translate')->where($params)->with($relations)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        // TODO: Implement getList() method.
    }

    public function getListWhere(array $orderBy = [], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query =  $this->restaur->with($relations)
        ->when($searchValue, function ($query) use ($searchValue) { 
            $query->orWhereHas('restaurantData', function ($q) use ($searchValue) {
                $q->where('restaurant_name', 'like', "%$searchValue%");
            });
            $query->orWhereHas('userData', function ($q) use ($searchValue) {
                $q->where('name', 'like', "%$searchValue%");
            });
        });
        $query->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(key($orderBy),current($orderBy));
            });
        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }

    

    public function getFirstWhereHas(array $params, string $whereHas = null, array $whereHasFilters = [], array $relations = []): ?Model
    {
        return $this->restaur->whereHas($whereHas, function ($query) use ($whereHasFilters) {
            $query->where($whereHasFilters);
        })->where($params)->first();
    }

    public function updatewhere(array $params, array $data): bool
    {
        return $this->restaur->where($params)->update($data);
    }

    public function update(string $id, array $data): bool
    {
        return $this->restaur->find($id)->update($data);
    }

    public function delete(array $params): bool
    {
        return $this->restaur->where($params)->delete();
    }
}
