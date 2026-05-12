<?php

namespace App\Repositories;

use App\Contracts\Repositories\GalleryRepositoryInterface;
use App\Models\Gallery;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class GalleryRepository implements GalleryRepositoryInterface
{
    public function __construct(private readonly Gallery $gallery)
    {
    }

    public function add(array $data): string|object
    {
        return $this->gallery->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->gallery->withoutGlobalScope('translate')->where($params)->with($relations)->first();
    }


    public function getFirstWhereWithoutGlobalScope(array $params, array $relations = []): ?Model
    {
        return $this->gallery->withoutGlobalScopes()->where($params)->with($relations)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {

    }

    public function getListWhere(array $orderBy = [], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->gallery
            ->when(isset($filters['temple_id']), function ($query) use ($filters) {
                return $query->where(['temple_id' => $filters['temple_id']]);
            })
            ->when(isset($filters['status']), function ($query) use ($filters) {
                return $query->where(['status' => $filters['status']]);
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }

    public function update(string $id, array $data): bool
    {
        return $this->gallery->where('id', $id)->update($data);
    }

    public function updateWhere(array $params, array $data): bool
    {
        $this->gallery->where($params)->update($data);
        return true;
    }

    public function delete(array $params): bool
    {
        return $this->gallery->where($params)->delete();
    }




    public function getListWithRelations(array $orderBy=[], string $searchValue = null, array $filters = [], array $withCount = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        
    }


}
