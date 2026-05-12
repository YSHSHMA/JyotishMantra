<?php

namespace App\Repositories;

use App\Contracts\Repositories\TourVisitRepositoryInterface;
use App\Models\TourVisits;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class TourVisitRepository implements TourVisitRepositoryInterface
{

    public function __construct(private readonly TourVisits $tourvisit) {}


    public function add(array $data): string|object
    {
        return $this->tourvisit->create($data);
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, ?int $offset = null): Collection|LengthAwarePaginator
    {

        $query = $this->tourvisit->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

    public function getListWhere(array $orderBy = [], ?string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, ?int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->tourvisit->with($relations)
            ->when($searchValue, function ($query) use ($searchValue) {
                $query->where('tour_name', 'like', "%$searchValue%");
                $query->orWhere('tour_type', 'like', "%" . str_replace(' ', '_', $searchValue) . "%");
            })

            ->when((isset($filters['created_id']) && !empty($filters['created_id'])), function ($query) use ($filters) {
                if (is_array($filters['created_id'])) {
                    return $query->whereIn('created_id', $filters['created_id']);
                }
                return $query->where(['created_id' => $filters['created_id']]);
            })
            ->when((!empty($filters['tour_type'])), function ($query) use ($filters) {
                return $query->where('tour_type', $filters['tour_type']);
            })
            ->when((!empty($filters['status'])), function ($query) use ($filters) {
                return $query->where('status', $filters['status']);
            })
            ->when((!empty($filters['use_date_status']) && $filters['use_date_status'] == 1), function ($query) {                 $query->where(function ($query) {
                    $query->whereIn('use_date', [0, 2, 3, 4])
                        ->orWhere(function ($query) {
                            $query->where('use_date', 1)
                                ->where(function ($subQuery) {
                                    $subQuery->whereIn('customized_type', ['', '0'])
                                        ->whereNotNull('startandend_date')
                                        ->whereRaw('? < STR_TO_DATE(SUBSTRING_INDEX(startandend_date, " - ", 1), "%Y-%m-%d")', [date('Y-m-d')]);
                                })
                                ->orWhere(function ($subQuery) {
                                    $subQuery->whereIn('customized_type', [1, 2, 3]);
                                });
                        });
                });
            })

            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }
    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->tourvisit->withoutGlobalScope('translate')->where($params)->with($relations)->first();
    }

    public function getFirstWhereWithoutGlobalScope(array $params, array $relations = []): ?Model
    {
        return $this->tourvisit->withoutGlobalScopes()->where($params)->with($relations)->first();
    }



    public function update(string $id, array $data): bool
    {
        return $this->tourvisit->where('id', $id)->update($data);
    }

    public function delete(array $params): bool
    {
        $this->tourvisit->where($params)->delete();
        return true;
    }
}
