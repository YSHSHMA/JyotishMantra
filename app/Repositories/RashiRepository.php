<?php

namespace App\Repositories;

use App\Contracts\Repositories\RashiRepositoryInterface;
use App\Models\Rashi;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class RashiRepository implements RashiRepositoryInterface
{
    public function __construct(
        private readonly Rashi         $rashi,
    )
    {
    }

    public function add(array $data): string|object
    {
        return $this->rashi->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->rashi->withoutGlobalScope('translate')->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->rashi->with($relations)
                ->when(!empty($orderBy), function ($query) use ($orderBy) {
                    return $query->orderBy(array_key_first($orderBy),array_values($orderBy)[0]);
                });

        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

    public function getListWhere(array $orderBy=[], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->rashi
            ->when($searchValue, function ($query) use($searchValue){
                $query->Where('name', 'like', "%$searchValue%")->orWhere('id', $searchValue);
            })
            ->when(isset($filters['name']), function ($query) use($filters) {
                return $query->where(['name' => $filters['name']]);
            })
            ->when(isset($filters['status']), function ($query) use($filters) {
                return $query->where(['status' => $filters['status']]);
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
        return $this->rashi->where('id', $id)->update($data);
    }

    public function delete(array $params): bool
    {
        $this->rashi->where($params)->delete();
        return true;
    }

}
