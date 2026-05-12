<?php

namespace App\Repositories;

use App\Contracts\Repositories\CitiesVisitRepositoryInterface;
use App\Models\CitiesVisits;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class CitiesVisitRepository implements CitiesVisitRepositoryInterface
{
    public function __construct(
        private readonly CitiesVisits $citiesvisit
    )
    {
    }

    public function add(Array $data): string|object
    {
        return $this->citiesvisit->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->citiesvisit->withoutGlobalScope('translate')->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, ?int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->citiesvisit->with($relations)
                ->when(!empty($orderBy), function ($query) use ($orderBy) {
                    return $query->orderBy(array_key_first($orderBy),array_values($orderBy)[0]);
                });
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }
    public function getListWhere(array $orderBy = [], ?string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, ?int $offset = null): Collection|LengthAwarePaginator
    {
        
        $query = $this->citiesvisit
        ->when($searchValue, function ($query) use($searchValue){
            $query->where('month_name', 'like', "%$searchValue%")
            ->orWhere('season', 'like', "%$searchValue%")
            ->orWhere('crowd', 'like', "%$searchValue%")
            ->orWhere('weather', 'like', "%$searchValue%")
            ->orWhere('sight', 'like', "%$searchValue%")
            ->orWhere('id', $searchValue);
        })->when(isset($filters['citie_id']), function ($query) use($filters) {
            return $query->where(['citie_id' => $filters['citie_id']]);
        })->when(isset($filters['month_name']), function ($query) use($filters) {
            return $query->where(['month_name' => $filters['month_name']]);
        })->when(isset($filters['season']), function ($query) use($filters) {
            return $query->where(['season' => $filters['season']]);
        })->when(isset($filters['crowd']), function ($query) use($filters) {
            return $query->where(['crowd' => $filters['crowd']]);
        })->when(isset($filters['weather']), function ($query) use($filters) {
            return $query->where(['weather' => $filters['weather']]);
        })->when(isset($filters['sight']), function ($query) use($filters) {
            return $query->where(['sight' => $filters['sight']]);
        })->when(!empty($orderBy), function ($query) use ($orderBy) {
            $query->orderBy(array_key_first($orderBy),array_values($orderBy)[0]);
        });

        $filters += ['searchValue' =>$searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
   
    }


    public function getFirstWhereNotNull(array $params,array $filters = [],array $orderBy = [],array $relations = []): ?Model
    {
        return $this->citiesvisit->where($params)->whereNotNull($filters)->orderBy(key($orderBy), current($orderBy))->first();
    }
    public function getListBySelectWhere(array $joinColumn = [], array $select = [],array $filters = [],array $orderBy = []): Collection
    {
        list($table, $first, $operator, $second) = $joinColumn;
        return $this->citiesvisit
            ->join($table, $first, $operator, $second)
            ->select($select)
            ->where($filters)
            ->orderBy(key($orderBy), current($orderBy))
            ->get();
    }

    
    public function update(string $id, array $data): bool
    {
        return $this->citiesvisit->where('id', $id)->update($data);
    }
    public function delete(array $params): bool
    {
        $this->citiesvisit->where($params)->delete();
        return true;
    }
    

}

?>