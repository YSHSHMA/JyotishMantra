<?php

namespace App\Repositories;

use App\Contracts\Repositories\CitiesRepositoryInterface;
use App\Models\Cities;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class CitiesRepository implements CitiesRepositoryInterface
{
    public function __construct(
        private readonly Cities $cities
    )
    {
    }

    public function add(Array $data): string|object
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
                    return $query->orderBy(array_key_first($orderBy),array_values($orderBy)[0]);
                });
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }
    public function getListWhere(array $orderBy=[], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->cities
            ->when($searchValue, function ($query) use($searchValue){
                $query->Where('city', 'like', "%$searchValue%")->orWhere('id', $searchValue);
            })
            ->when(isset($filters['city']), function ($query) use($filters) {
                return $query->where(['city' => $filters['city']]);
            })
            ->when(isset($filters['state_id']), function ($query) use($filters) {
                return $query->where(['state_id' => $filters['state_id']]);
            })
            ->when(isset($filters['short_desc']), function ($query) use($filters) {
                return $query->where(['short_desc' => $filters['short_desc']]);
            })
            ->when(isset($filters['status']), function ($query) use($filters) {
                return $query->where(['status' => $filters['status']]);
            })
            ->when(isset($filters['name']), function ($query) use($filters) {
                return $query->where(['name' => $filters['name']]);
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy),array_values($orderBy)[0]);
            });

        $filters += ['searchValue' =>$searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }

    public function getFirstWhereNotNull(array $params,array $filters = [],array $orderBy = [],array $relations = []): ?Model
    {
        return $this->cities->where($params)->whereNotNull($filters)->orderBy(key($orderBy), current($orderBy))->first();
    }
    public function getListBySelectWhere(array $joinColumn = [], array $select = [],array $filters = [],array $orderBy = []): Collection
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

?>