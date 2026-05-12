<?php

namespace App\Repositories;

use App\Contracts\Repositories\VideoSubCategoryRepositoryInterface;
use App\Models\VideoSubCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class VideoSubCategoryRepository implements VideoSubCategoryRepositoryInterface
{
    public function __construct(
        private readonly VideoSubCategory $videosubcategory,
    )
    {
    }


    public function add(array $data): string|object
    {
        return $this->videosubcategory->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->videosubcategory->withoutGlobalScope('translate')->where($params)->with($relations)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->videosubcategory->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

/*
    public function getListWhere(
        array      $orderBy = [],
        string     $searchValue = null,
        array      $filters = [], array $relations = [],
        int|string $dataLimit = DEFAULT_DATA_LIMIT,
        int        $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->videosubcategory->when($searchValue, function ($query) use ($searchValue) {
                    return $query->where('name', 'like', "%$searchValue%");
                })
                ->when(!empty($orderBy), function ($query) use ($orderBy) {
                    $query->orderBy(array_key_first($orderBy),array_values($orderBy)[0]);
                });

        $filters += ['searchValue' =>$searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }
*/

public function getListWhere(
    array $orderBy = [],
    string $searchValue = null,
    array $filters = [],
    array $relations = [],
    int|string $dataLimit = DEFAULT_DATA_LIMIT,
    int $offset = null
): Collection|LengthAwarePaginator {
    // Start building the base query
    $query = $this->videosubcategory->with(['category:id,name']);

    // Apply search condition if searchValue is provided
    if ($searchValue) {
        $query->where(function ($query) use ($searchValue) {
            $query->where('name', 'like', "%$searchValue%") // Search by subcategory name
                  ->orWhereHas('category', function ($query) use ($searchValue) {
                      $query->where('name', 'like', "%$searchValue%"); // Search by category name
                  });
        });
    }

    // Apply orderBy conditions
    if (!empty($orderBy)) {
        $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
    }

    // Paginate or get all based on dataLimit
    return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
}


    public function update(string $id, array $data): bool
    {
        return $this->videosubcategory->where('id', $id)->update($data);
    }

    public function delete(array $params): bool
    {
        $this->videosubcategory->where($params)->delete();
        return true;
    }
}
