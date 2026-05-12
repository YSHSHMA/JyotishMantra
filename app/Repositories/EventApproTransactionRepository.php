<?php

namespace App\Repositories;

use App\Contracts\Repositories\EventApproTransactionRepositoryInterface;   
use App\Models\EventApproTransaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class EventApproTransactionRepository implements EventApproTransactionRepositoryInterface
{
    public function __construct(
        private readonly EventApproTransaction  $EventAp,
    )
    {
    }

    public function add(array $data): string|object
    {
        return $this->EventAp->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->EventAp->withoutGlobalScope('translate')->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->EventAp->with($relations)
                ->when(!empty($orderBy), function ($query) use ($orderBy) {
                    return $query->orderBy(array_key_first($orderBy),array_values($orderBy)[0]);
                });

        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

  public function getListWhere(array $orderBy = [], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->EventAp->with($relations)
            ->when($searchValue, function ($query) use ($searchValue) {
                $query->where('name', 'like', "%$searchValue%")->orWhere('id', $searchValue);
            })
        
            ->when(isset($filters['status']), function ($query) use ($filters) {
                return $query->where('status', $filters['status']);
            })
            ->when(isset($filters['organizer_id']), function ($query) use ($filters) {
                return $query->where('organizer_id', $filters['organizer_id']);
            })
            
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }

  public function update(string $id, array $data): bool
{
    return $this->EventAp->where('id', $id)->update($data);
}


    public function delete(array $params): bool
    {
        $this->EventAp->where($params)->delete();
        return true;
    }

}
