<?php

namespace App\Repositories;

use App\Contracts\Repositories\MasikRashiRepositoryInterface;
use App\Models\MasikRashi;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class masikRashiRepository implements masikRashiRepositoryInterface
{
    public function __construct(
        private readonly MasikRashi         $masikrashi,
    )
    {
    }

    public function add(array $data): string|object
    {
        return $this->masikrashi->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->masikrashi->withoutGlobalScope('translate')->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->masikrashi->with($relations)
                ->when(!empty($orderBy), function ($query) use ($orderBy) {
                    return $query->orderBy(array_key_first($orderBy),array_values($orderBy)[0]);
                });

        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

    public function getListWhere(array $orderBy=[], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->masikrashi
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
        return $this->masikrashi->where('id', $id)->update($data);
    }

    public function delete(array $params): bool
    {
        $this->masikrashi->where($params)->delete();
        return true;
    }

}
