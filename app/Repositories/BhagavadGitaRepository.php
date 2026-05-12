<?php

namespace App\Repositories;

use App\Contracts\Repositories\BhagavadGitaRepositoryInterface;
use App\Models\BhagavadGitaChapter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class BhagavadGitaRepository implements BhagavadGitaRepositoryInterface
{
    public function __construct(
        private readonly BhagavadGitaChapter $bhagavadgita,
    )
    {
    }


    public function add(array $data): string|object
    {
        return $this->bhagavadgita->create($data);
    }


    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->bhagavadgita->withoutGlobalScope('translate')->where($params)->with($relations)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->bhagavadgita->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }



   
    public function getListWhere(
        array      $orderBy = [],
        string     $searchValue = null,
        array      $filters = [], array $relations = [],
        int|string $dataLimit = DEFAULT_DATA_LIMIT,
        int        $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->bhagavadgita->when($searchValue, function ($query) use ($searchValue) {
                    return $query->where('name', 'like', "%$searchValue%");
                })
                ->when(!empty($orderBy), function ($query) use ($orderBy) {
                    $query->orderBy(array_key_first($orderBy),array_values($orderBy)[0]);
                });

        $filters += ['searchValue' =>$searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }

    public function update(string $id, array $data): bool
    {
        return $this->bhagavadgita->where('id', $id)->update($data);
    }

    public function delete(array $params): bool
    {
        $this->bhagavadgita->where($params)->delete();
        return true;
    }
}
