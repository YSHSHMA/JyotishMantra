<?php

namespace App\Repositories;

use App\Contracts\Repositories\CitiesReviewRepositoryInterface;
use App\Models\CitiesReview;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class CitiesReviewRepository implements CitiesReviewRepositoryInterface
{
    public function __construct(
        private readonly CitiesReview $cities
    ) {
    }

    public function add(array $data): string|object
    {
        return $this->cities->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->cities->withoutGlobalScope('translate')->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, ?int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->cities->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }
    public function getListWhere(array $orderBy = [], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->cities->with($relations)
            ->when($searchValue, function ($query) use ($searchValue) {
                $query->whereHas('cities', function ($q) use ($searchValue) {
                    $q->where('city', 'like', "%$searchValue%");
                });
                $query->orWhereHas('userData', function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%$searchValue%");
                });
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }

    public function getFirstWhereNotNull(array $params, array $filters = [], array $orderBy = [], array $relations = []): ?Model
    {
        return $this->cities->where($params)->whereNotNull($filters)->orderBy(key($orderBy), current($orderBy))->first();
    }
    public function getListBySelectWhere(array $joinColumn = [], array $select = [], array $filters = [], array $orderBy = []): Collection
    {
        list($table, $first, $operator, $second) = $joinColumn;
        return $this->cities
            ->join($table, $first, $operator, $second)
            ->select($select)
            ->where($filters)
            ->orderBy(key($orderBy), current($orderBy))
            ->get();
    }


    public function update(string $id, array $data): bool
    {
        return $this->cities->where('id', $id)->update($data);
    }
    public function delete(array $params): bool
    {
        $this->cities->where($params)->delete();
        return true;
    }
}
