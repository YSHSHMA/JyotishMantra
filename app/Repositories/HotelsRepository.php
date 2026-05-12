<?php
namespace App\Repositories;

use App\Contracts\Repositories\HotelsRepositoryInterface;
use App\Models\Hotels;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class HotelsRepository implements HotelsRepositoryInterface
{
    public function __construct(
        private readonly Hotels $hotels,
    ) {
    }

    public function add(array $data): string|object
    {
        return $this->hotels->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->hotels->withoutGlobalScope('translate')->where($params)->with($relations)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->hotels->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }


    public function getListWhere(array $orderBy = [], ?string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, ?int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->hotels->with($relations)
        ->when($searchValue, function ($query) use ($searchValue) {
            $query->where('hotel_name', 'like', "%{$searchValue}%");
            $query->orWhereHas('states', function ($q) use ($searchValue) {
                $q->where('name', 'like', "%$searchValue%");
            });
            $query->orWhereHas('cities', function ($q) use ($searchValue) {
                $q->where('city', 'like', "%$searchValue%");
            });
        });
        
        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }
    public function update(string $id, array $data): bool
    {
        return $this->hotels->where('id', $id)->update($data);
    }

    public function updateWhere(array $params, array $data): bool
    {
        $this->hotels->where($params)->update($data);
        return true;
    }

    public function delete(array $params): bool
    {
        $this->hotels->where($params)->delete();
        return true;
    }
}
