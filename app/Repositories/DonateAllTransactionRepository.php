<?php

namespace App\Repositories;

use App\Contracts\Repositories\DonateAllTransactionRepositoryInterface;
use App\Models\DonateAllTransaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class DonateAllTransactionRepository implements DonateAllTransactionRepositoryInterface
{
    public function __construct(
        private readonly DonateAllTransaction  $donateTransaction,
    ) {}

    public function add(array $data): string|object
    {
        return $this->donateTransaction->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->donateTransaction->withoutGlobalScope('translate')->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->donateTransaction->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

    public function getListWhere(array $orderBy = [], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->donateTransaction->with($relations)
            ->when($searchValue, function ($query) use ($searchValue) {
                $query->where('trans_id', 'like', "%$searchValue%");
                $query->orWhere('amount', 'like', "%$searchValue%");
                $query->orWhereHas('users', function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%$searchValue%");
                    $q->orWhere('phone', 'like', "%$searchValue%");
                });
                $query->orWhereHas('adsTrust', function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%$searchValue%");
                    $q->orWhere('type', 'like', "%$searchValue%");
                    $q->orWhere('ads_id', 'like', "%$searchValue%")
                    ->orWhereHas('Purpose', function ($p) use ($searchValue) {
                        $p->where('name', 'like', "%$searchValue%");
                    });
                });
                $query->orWhereHas('getTrust', function ($q) use ($searchValue) {
                    $q->where('trust_name', 'like', "%$searchValue%");
                    $q->orWhere('name', 'like', "%$searchValue%");
                });
            })

            ->when(isset($filters['amount_status']), function ($query) use ($filters) {
                return $query->where('amount_status', $filters['amount_status']);
            })
            ->when(isset($filters['type']), function ($query) use ($filters) {
                return $query->where('type', $filters['type']);
            })
            ->when(isset($filters['typeIn']), function ($query) use ($filters) {
                return $query->whereIn('type', $filters['typeIn']);
            })
            ->when(isset($filters['ads_id']), function ($query) use ($filters) {
                return $query->where('ads_id', $filters['ads_id']);
            })
            ->when(isset($filters['trust_id']), function ($query) use ($filters) {
                return $query->where('trust_id', $filters['trust_id']);
            })
            ->when(isset($filters['amount_status']), function ($query) use ($filters) {
                return $query->where('amount_status', $filters['amount_status']);
            })            
            ->when((isset($filters['start_to_end_date']) && !empty($filters['start_to_end_date'])), function ($query) use ($filters) {
                $dates = explode(' - ',$filters['start_to_end_date']);
                return $query->whereBetween('created_at', [$dates[0], $dates[1]]);
            })
            ->when((isset($filters['groupby_trust']) && $filters['groupby_trust'] == 1), function ($query) {
                return $query->select('trust_id', \Illuminate\Support\Facades\DB::raw('SUM(amount) as amount'))->where('amount_status',1)->where('type','donate_trust')->groupBy('trust_id');
            })
            ->when((isset($filters['groupby_ads']) && $filters['groupby_ads'] == 1), function ($query) {
                return $query->select('ads_id', \Illuminate\Support\Facades\DB::raw('SUM(amount) as amount'))->where('amount_status',1)->where('type','donate_ads')->groupBy('ads_id');
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }
    public function update(string $id, array $data): bool
    {
        return $this->donateTransaction->where('id', $id)->update($data);
    }


    public function delete(array $params): bool
    {
        $this->donateTransaction->where($params)->delete();
        return true;
    }
}
