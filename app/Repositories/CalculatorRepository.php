<?php

namespace App\Repositories;

// use App\Contracts\Repositories\FestivalRepositoryInterface;
use App\Contracts\Repositories\CalculatorRepositoryInterface;
// use App\Models\Festival;
use App\Models\Calculator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class CalculatorRepository implements CalculatorRepositoryInterface
{
    public function __construct(
        private readonly Calculator         $calculator,
    )
    {
    }

    public function add(array $data): string|object
    {
        return $this->calculator->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->calculator->withoutGlobalScope('translate')->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->calculator->with($relations)
                ->when(!empty($orderBy), function ($query) use ($orderBy) {
                    return $query->orderBy(array_key_first($orderBy),array_values($orderBy)[0]);
                });

        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

    public function getListWhere(array $orderBy=[], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->calculator
            ->when($searchValue, function ($query) use($searchValue){
                $query->Where('name', 'like', "%$searchValue%")->orWhere('id', $searchValue);
            })
            ->when(isset($filters['name']), function ($query) use($filters) {
                return $query->where(['name' => $filters['name']]);
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy),array_values($orderBy)[0]);
            });

        $filters += ['searchValue' =>$searchValue];
        //dd($query->get());
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);

    }

    public function update(string $id, array $data): bool
    {
        return $this->calculator->where('id', $id)->update($data);
    }

    public function delete(array $params): bool
    {
        $this->calculator->where($params)->delete();
        return true;
    }

}
