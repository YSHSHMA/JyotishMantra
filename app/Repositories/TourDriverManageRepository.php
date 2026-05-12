<?php

namespace App\Repositories;

use App\Contracts\Repositories\TourDriverManageRepositoryInterface;
use App\Models\TourDriverManage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class TourDriverManageRepository implements TourDriverManageRepositoryInterface
{

    public function __construct(private readonly TourDriverManage $tourdriver)
    {
    }
    
    public function add(array $data): string|object
    {
        return $this->tourdriver->create($data);
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, ?int $offset = null): Collection|LengthAwarePaginator
    {
        
        $query = $this->tourdriver->with($relations)
        ->when(!empty($orderBy), function ($query) use ($orderBy) {
            return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
        });
    return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);

    }

    public function getListWhere(array $orderBy = [], ?string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, ?int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->tourdriver->with($relations)
            ->when($searchValue, function ($query) use ($searchValue) {
                    $query->where('name', 'like', "%$searchValue%");
                    $query->orWhere('phone', 'like', "%$searchValue%");
                    $query->orWhere('email', 'like', "%$searchValue%");
            })

            ->when((isset($filters['traveller_id']) && !empty($filters['traveller_id'])), function ($query) use($filters) {
                return $query->where(['traveller_id' => $filters['traveller_id']]);
            }) 

            ->when((isset($filters['status']) && !empty($filters['status'])), function ($query) use($filters) {
                return $query->where(['status' => $filters['status']]);
            }) 
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
        
    }
    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->tourdriver->withoutGlobalScope('translate')->where($params)->with($relations)->first();
    }

    public function getFirstWhereWithoutGlobalScope(array $params, array $relations = []): ?Model
    {
        return $this->tourdriver->withoutGlobalScopes()->where($params)->with($relations)->first();
    }



    public function update(string $id, array $data): bool
    {
        return $this->tourdriver->where('id', $id)->update($data);
    }

    public function delete(array $params): bool
    {
        $this->tourdriver->where($params)->delete();
        return true;
    }

}

?>