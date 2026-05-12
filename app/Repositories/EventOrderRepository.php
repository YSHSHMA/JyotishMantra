<?php

namespace App\Repositories;

use App\Contracts\Repositories\EventOrderRepositoryInterface;
use App\Models\EventOrder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class EventOrderRepository implements EventOrderRepositoryInterface
{
    public function __construct(
        private readonly EventOrder  $eventorder,
    ) {}

    public function add(array $data): string|object
    {
        return $this->eventorder->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->eventorder->withoutGlobalScope('translate')->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->eventorder->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

    public function getListWhere(array $orderBy = [], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->eventorder->with($relations)
            ->when($searchValue, function ($query) use ($searchValue) {
                $query->where('order_no', 'like', "%{$searchValue}%");
                $query->orWhereHas('eventid', function ($q) use ($searchValue) {
                    $q->where('event_name', 'like', "%$searchValue%");
                    $q->orWhere('organizer_by', 'like', "%$searchValue%")
                        ->orWhereHas('categorys', function ($p) use ($searchValue) {
                            $p->where('category_name', 'like', "%$searchValue%");
                        });
                });
                $query->orWhereHas('userdata', function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%$searchValue%");
                });
            })

            ->when(isset($filters['status']), function ($query) use ($filters) {
                return $query->where('status', $filters['status']);
            })
            ->when((isset($filters['venue_id']) && !empty($filters['venue_id'])), function ($query) use ($filters) {
                return $query->where('venue_id', $filters['venue_id']);
            })
            ->when(isset($filters['event_id']), function ($query) use ($filters) {
                return is_array($filters['event_id'])
                    ? $query->whereIn('event_id', $filters['event_id'])
                    : $query->where('event_id', $filters['event_id']);
            })



            ->when(isset($filters['organizer_id']), function ($query) use ($filters) {
                return $query->whereHas('eventid', function ($q) use ($filters) {
                    $q->where('event_organizer_id', $filters['organizer_id']);
                });
            })
            ->when(isset($filters['status__transaction_status']), function ($query) use ($filters) {
                $query->where(function ($query) {
                    $query->where('transaction_status', 3)
                          ->orWhere('status', 3);
                });
            })
            ->when(isset($filters['status__transaction_status_filter']), function ($query) use ($filters) {
                $query->select(
                        'event_id', 
                        \Illuminate\Support\Facades\DB::raw('count(order_no) as total_orders'), 
                        \Illuminate\Support\Facades\DB::raw('SUM(amount) as amount'), 
                        \Illuminate\Support\Facades\DB::raw('SUM(coupon_amount) as coupon_amount'), 
                        \Illuminate\Support\Facades\DB::raw('SUM(admin_commission) as admin_commission'), 
                        \Illuminate\Support\Facades\DB::raw('SUM(gst_amount) as gst_amount'), 
                        \Illuminate\Support\Facades\DB::raw('SUM(final_amount) as final_amount')
                    )
                    ->where(function ($query) {
                        $query->where('transaction_status', 3)
                              ->orWhere('status', 3);
                    })
                    ->groupBy('event_id');
            })

            ->when(isset($filters['order_status']), function ($query) use ($filters) {
                return $query->where('transaction_status', $filters['order_status']);
            })
            ->when(isset($filters['transaction_status']), function ($query) use ($filters) {
                return $query->where('transaction_status', $filters['transaction_status']);
            })
            ->when((isset($filters['groupby_event']) && $filters['groupby_event'] == 1), function ($query) {
                return $query->select('event_id', \Illuminate\Support\Facades\DB::raw('count(order_no) as total_orders'), \Illuminate\Support\Facades\DB::raw('SUM(amount) as amount'), \Illuminate\Support\Facades\DB::raw('SUM(coupon_amount) as coupon_amount'), \Illuminate\Support\Facades\DB::raw('SUM(admin_commission) as admin_commission'), \Illuminate\Support\Facades\DB::raw('SUM(gst_amount) as gst_amount'), \Illuminate\Support\Facades\DB::raw('SUM(final_amount) as final_amount'))->where('transaction_status', 1)->where('status', 1)->groupBy('event_id');
            })
            ->when((isset($filters['start_to_end_date']) && !empty($filters['start_to_end_date'])), function ($query) use ($filters) {
                $dates = explode(' - ', $filters['start_to_end_date']);
                return $query->whereBetween('created_at', [$dates[0], $dates[1]]);
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }

    public function update(string $id, array $data): bool
    {
        return $this->eventorder->where('id', $id)->update($data);
    }
    public function delete(array $params): bool
    {
        $this->eventorder->where($params)->delete();
        return true;
    }
}
