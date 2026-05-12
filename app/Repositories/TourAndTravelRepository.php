<?php

namespace App\Repositories;

use App\Contracts\Repositories\TourAndTravelRepositoryInterface;
use App\Models\TourAndTravel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class TourAndTravelRepository implements TourAndTravelRepositoryInterface
{

    public function __construct(private readonly TourAndTravel $tourtravel) {}


    public function add(array $data): string|object
    {
        return $this->tourtravel->create($data);
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, ?int $offset = null): Collection|LengthAwarePaginator
    {

        $query = $this->tourtravel->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

    public function getListWhere(array $orderBy = [], ?string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, ?int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->tourtravel->with($relations)
            ->when($searchValue, function ($query) use ($searchValue) {
                $query->where('owner_name', 'like', "%$searchValue%");
                $query->orWhere('company_name', 'like', "%$searchValue%");
                $query->orWhere('phone_no', 'like', "%$searchValue%");
                $query->orWhere('email', 'like', "%$searchValue%");
                $query->orWhere('address', 'like', "%$searchValue%");
                $query->orWhere('person_name', 'like', "%$searchValue%");
                $query->orWhere('person_phone', 'like', "%$searchValue%");
                $query->orWhere('person_email', 'like', "%$searchValue%");
                $query->orWhere('person_address', 'like', "%$searchValue%");
            })
            ->when((isset($filters['id']) && !empty($filters['id'])), function ($query) use ($filters) {
                return $query->where(['id' => $filters['id']]);
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }
    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->tourtravel->withoutGlobalScope('translate')->where($params)->with($relations)->first();
    }

    public function getFirstWhereWithoutGlobalScope(array $params, array $relations = []): ?Model
    {
        return $this->tourtravel->withoutGlobalScopes()->where($params)->with($relations)->first();
    }



    public function update(string $id, array $data): bool
    {
        return $this->tourtravel->where('id', $id)->update($data);
    }

    public function delete(array $params): bool
    {
        $this->tourtravel->where($params)->delete();
        return true;
    }
}