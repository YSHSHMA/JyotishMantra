<?php

namespace App\Repositories;

use App\Contracts\Repositories\TourOrderRepositoryInterface;
use App\Models\TourOrder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class TourOrderRepository implements TourOrderRepositoryInterface
{

    public function __construct(private readonly TourOrder $tourorder) {}


    public function add(array $data): string|object
    {
        return $this->tourorder->create($data);
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, ?int $offset = null): Collection|LengthAwarePaginator
    {

        $query = $this->tourorder->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

    public function getListWhere(array $orderBy = [], ?string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, ?int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->tourorder->with($relations)
            ->when($searchValue, function ($query) use ($searchValue) {
                $query->where(function ($query) use ($searchValue) {
                    $query->where('amount', 'like', "%$searchValue%")
                        ->orWhereHas('userData', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%$searchValue%")
                                ->orWhere('phone', 'like', "%$searchValue%");
                        })
                        ->orWhereHas('company', function ($q) use ($searchValue) {
                            $q->where('company_name', 'like', "%$searchValue%");
                        })
                        ->orWhereHas('Tour', function ($q) use ($searchValue) {
                            $q->where('tour_name', 'like', "%$searchValue%");
                        });
                });
            })
            ->when(isset($filters['amount_status']), function ($query) use ($filters) {
                return $query->where('amount_status', $filters['amount_status']);
            })
            ->when(isset($filters['accept']) && ($filters['accept'] == 1), function ($query) use ($filters) {
                $query->where('pickup_date', '>', \Carbon\Carbon::today()->toDateString());
                $query->whereHas('accept', function ($subQuery) use ($filters) {
                    $subQuery->where('status', 1)
                        ->when(isset($filters['accept_user']) && !empty($filters['accept_user']), function ($subQuery) use ($filters) {
                            $subQuery->where('traveller_id', $filters['accept_user']);
                        });
                });
            })
            ->when(isset($filters['cancel_vendor_list']), function ($query) use ($filters) {
                return $query->where(function ($q) use ($filters) {
                    $q->whereNull('cancel_vendor_list')
                        ->orWhere('cancel_vendor_list', '[]')
                        ->orWhere('cancel_vendor_list', '')
                        ->orWhereRaw(
                            "NOT JSON_CONTAINS(cancel_vendor_list, ?)",
                            [json_encode((string)$filters['cancel_vendor_list'])]
                        );
                });
            })

            ->when(isset($filters['accept_user']), function ($query) use ($filters) {
                return $query->withCabOrderCheck($filters['accept_user']);
            })
            ->when(isset($filters['status']), function ($query) use ($filters) {
                if (is_array($filters['status'])) {
                    return $query->whereIn('status', $filters['status']);
                }
                return $query->where(['status' => $filters['status']]);
            })
            ->when(isset($filters['assign_status']) && !empty($filters['assign_status']), function ($query) use ($filters) {
                if ($filters['assign_status'] == 'cab_driver_assign_not') {
                    return $query->whereRaw("JSON_CONTAINS(traveller_cab_id, '0')")->whereRaw("JSON_CONTAINS(traveller_driver_id, '0')"); //->where('traveller_cab_id', '==', '0')->where('traveller_driver_id', '==', '0');
                } else {
                    return $query->whereRaw("NOT JSON_CONTAINS(traveller_cab_id, '0')")->whereRaw("NOT JSON_CONTAINS(traveller_driver_id, '0')"); //$query->where('traveller_driver_id', '!=', '0')->where('traveller_driver_id', '!=', '0');
                }
            })

            ->when(isset($filters['cab_assign_not']), function ($query) use ($filters) {
                return $query->where('cab_assign', '==', '0');
            })

            ->when(isset($filters['cab_assign']), function ($query) use ($filters) {
                return $query->where('cab_assign', '!=', '0');
            })
            ->when(isset($filters['cab_assign_id']), function ($query) use ($filters) {
                return $query->where('cab_assign', $filters['cab_assign_id']);
            })
            ->when(isset($filters['pickup_status']), function ($query) use ($filters) {
                return $query->where(['pickup_status' => $filters['pickup_status']]);
            })
            ->when(isset($filters['drop_status']), function ($query) use ($filters) {
                return $query->where(['drop_status' => $filters['drop_status']]);
            })
            ->when(isset($filters['tour_id']), function ($query) use ($filters) {
                return $query->where(['tour_id' => $filters['tour_id']]);
            })
            ->when(isset($filters['refund_status']), function ($query) use ($filters) {
                return $query->where(['refund_status' => $filters['refund_status']]);
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }
    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->tourorder->withoutGlobalScope('translate')->where($params)->with($relations)->first();
    }

    public function getFirstWhereWithoutGlobalScope(array $params, array $relations = []): ?Model
    {
        return $this->tourorder->withoutGlobalScopes()->where($params)->with($relations)->first();
    }



    public function update(string $id, array $data): bool
    {
        return $this->tourorder->where('id', $id)->update($data);
    }

    public function delete(array $params): bool
    {
        $this->tourorder->where($params)->delete();
        return true;
    }
}
