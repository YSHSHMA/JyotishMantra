<?php

namespace App\Repositories;

use App\Contracts\Repositories\DonateTrustRepositoryInterface;
use App\Models\DonateTrust;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class DonateTrustRepository implements DonateTrustRepositoryInterface
{
    public function __construct(
        private readonly DonateTrust  $donateTrust,
    ) {}

    public function add(array $data): string|object
    {
        return $this->donateTrust->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->donateTrust->withoutGlobalScope('translate')->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->donateTrust->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

    public function getListWhere(array $orderBy = [], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->donateTrust->with($relations)
            ->when($searchValue, function ($query) use ($searchValue) {
                $query->where('name', 'like', "%$searchValue%")->orWhere('id', $searchValue);
                $query->orWhere('trust_id', 'like', "%$searchValue%");
                $query->orWhere('trust_name', 'like', "%$searchValue%");
                $query->orWhereHas('category', function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%$searchValue%");
                });
            })

            ->when(isset($filters['status']), function ($query) use ($filters) {
                return $query->where('status', $filters['status']);
            })
            ->when(isset($filters['types']), function ($query) use ($filters) {
                return $query->where('type', $filters['types']);
            })
            ->when(isset($filters['is_approve']), function ($query) use ($filters) {
                return $query->where('is_approve', $filters['is_approve']);
            })
            ->when(isset($filters['category_id']), function ($query) use ($filters) {
                return $query->where('category_id', $filters['category_id']);
            })
            
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }

    public function update(string $id, array $data): bool
    {
        return $this->donateTrust->where('id', $id)->update($data);
    }


    public function delete(array $params): bool
    {
        $this->donateTrust->where($params)->delete();
        return true;
    }
}
