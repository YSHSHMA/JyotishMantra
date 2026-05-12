<?php

namespace App\Repositories;

use App\Contracts\Repositories\BirthJournalKundaliRepositoryInterface;
use App\Models\BirthJournalKundali;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class BirthJournalKundaliRepository implements BirthJournalKundaliRepositoryInterface
{
    public function __construct(
        private readonly BirthJournalKundali $birthjoukundali,
    ) {}

    public function add(array $data): string|object
    {
        return $this->birthjoukundali->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->birthjoukundali->withoutGlobalScope('translate')->where($params)->with($relations)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->birthjoukundali->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

    public function getListWhere(
        array $orderBy = [],
        string $searchValue = null,
        array $filters = [],
        array $relations = [],
        int|string $dataLimit = DEFAULT_DATA_LIMIT,
        int $offset = null
    ): Collection|LengthAwarePaginator {
        $relationsWithConditions = [];

        $query = $this->birthjoukundali->newQuery();
    
        if (in_array('birthJournal_kundali', $relations)) {
            $relationsWithConditions['birthJournal_kundali'] = function ($query) {
                $query->where('name', 'kundali');
            };
            $query->whereHas('birthJournal_kundali', function ($query) {
                $query->where('name', 'kundali');
            });
        }
    
        if (in_array('birthJournal_kundalimilan', $relations)) {
            $relationsWithConditions['birthJournal_kundalimilan'] = function ($query) {
                $query->where('name', 'kundali_milan');
            };
            $query->whereHas('birthJournal_kundalimilan', function ($query) {
                $query->where('name', 'kundali_milan');
            });
        }
    
        foreach ($relations as $relation) {
            if (!isset($relationsWithConditions[$relation])) {
                $relationsWithConditions[] = $relation;
            }
        }
    
        $query->with($relationsWithConditions)
            ->when($searchValue, function ($query) use ($searchValue) {
                $query->where(function ($query) use ($searchValue) {
                    $query->where('name', 'like', "%$searchValue%")
                        ->orWhere('email', 'like', "%$searchValue%")
                        ->orWhere('phone_no', 'like', "%$searchValue%")
                        ->orWhere('state', 'like', "%$searchValue%");
                });
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            })
            ->when(isset($filters['payment_statusIN']), function ($query) use ($filters) {
                return $query->whereIn('payment_status', $filters['payment_statusIN']);
            })
            ->when(isset($filters['payment_status']), function ($query) use ($filters) {
                return $query->where('payment_status', $filters['payment_status']);
            })            
            ->when((isset($filters['kundali_pdf']) && $filters['kundali_pdf'] == 0), function ($query) use ($filters) {
                return $query->where('kundali_pdf','=','');
            })
            ->when((isset($filters['kundali_pdf']) && $filters['kundali_pdf'] == 1), function ($query) use ($filters) {
                return $query->where('kundali_pdf','!=','');
            })

            ->when(isset($filters['milan_verify']), function ($query) use ($filters) {
                return $query->where('milan_verify', $filters['milan_verify']);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit === 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }

    public function getListWhereIn(array $orderBy = [], string $searchValue = null, array $filters = [], array $whereInFilters = [], array $relations = [], array $nullFields = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->birthjoukundali
            ->with($relations)
            ->when($searchValue, function ($query) use ($searchValue) {
                return $query->orWhere('banner_type', 'like', "%$searchValue%");
            })
            ->when(!empty($whereInFilters), function ($query) use ($whereInFilters) {
                foreach ($whereInFilters as $key => $filterIndex) {
                    $query->whereIn($key, $filterIndex);
                }
            })
            ->when(!empty($nullFields), function ($query) use ($nullFields) {
                return $query->whereNull($nullFields);
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            })->when($filters['theme'], function ($query) use ($filters) {
                return $query->where('theme', $filters['theme']);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }


    public function update(string $id, array $data): bool
    {
        $this->birthjoukundali->where('id', $id)->update($data);
        return true;
    }

    public function delete(array $params): bool
    {
        $this->birthjoukundali->where($params)->delete();
        return true;
    }
}
