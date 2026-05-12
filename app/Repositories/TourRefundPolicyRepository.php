<?php

namespace App\Repositories;

use App\Contracts\Repositories\TourRefundPolicyRepositoryInterface;
use App\Models\TourRefundPolicy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class TourRefundPolicyRepository implements TourRefundPolicyRepositoryInterface
{

    public function __construct(private readonly TourRefundPolicy $tourpolicy)
    {
    }
    

    public function add(array $data): string|object
    {
        return $this->tourpolicy->create($data);
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, ?int $offset = null): Collection|LengthAwarePaginator
    {
        
        $query = $this->tourpolicy->with($relations)
        ->when(!empty($orderBy), function ($query) use ($orderBy) {
            return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
        });
    return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);

    }

    public function getListWhere(array $orderBy = [], ?string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, ?int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->tourpolicy->with($relations)
            ->when($searchValue, function ($query) use ($searchValue) {
                    $query->where('percentage', 'like', "%$searchValue%");
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
        
    }
    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->tourpolicy->withoutGlobalScope('translate')->where($params)->with($relations)->first();
    }

    public function getFirstWhereWithoutGlobalScope(array $params, array $relations = []): ?Model
    {
        return $this->tourpolicy->withoutGlobalScopes()->where($params)->with($relations)->first();
    }



    public function update(string $id, array $data): bool
    {
        return $this->tourpolicy->where('id', $id)->update($data);
    }

    public function delete(array $params): bool
    {
        $this->tourpolicy->where($params)->delete();
        return true;
    }

}

?>