<?php

namespace App\Repositories;

use App\Contracts\Repositories\TempleCategoriesRepositoryInterface;
use App\Contracts\Repositories\TemplesRepositoryInterface;
use App\Models\TempleCategory;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class TempleCategoriesRepository implements TempleCategoriesRepositoryInterface
{

    public function __construct(private readonly TempleCategory $temple)
    {
    }
    

    public function add(array $data): string|object
    {
        return $this->temple->create($data);
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, ?int $offset = null): Collection|LengthAwarePaginator
    {
        
        $query = $this->temple->with($relations)
        ->when(!empty($orderBy), function ($query) use ($orderBy) {
            return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
        });
    return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);

    }

    public function getListWhere(array $orderBy = [], ?string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, ?int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->temple->with($relations)
            ->when($searchValue, function ($query) use ($searchValue) {
                    $query->where('name', 'like', "%$searchValue%");
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
        
    }
    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->temple->withoutGlobalScope('translate')->where($params)->with($relations)->first();
    }

    public function getFirstWhereWithoutGlobalScope(array $params, array $relations = []): ?Model
    {
        return $this->temple->withoutGlobalScopes()->where($params)->with($relations)->first();
    }



    public function update(string $id, array $data): bool
    {
        return $this->temple->where('id', $id)->update($data);
    }

    public function delete(array $params): bool
    {
        $this->temple->where($params)->delete();
        return true;
    }
}

?>
