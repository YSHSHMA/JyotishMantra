<?php

namespace App\Repositories;

use App\Contracts\Repositories\EventsLeadsRepositoryInterface;
use App\Models\EventLeads;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;

class EventsLeadsRepository implements EventsLeadsRepositoryInterface
{
    public function __construct(
        private readonly EventLeads  $eventlead,
    ) {}

    public function add(array $data): string|object
    {
        return $this->eventlead->create($data);
    }

    public function sendMails(array $data): bool
    {
        return true;
    }


    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->eventlead->withoutGlobalScope('translate')->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->eventlead->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

    public function getListWhere(array $orderBy = [], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->eventlead->with($relations)
            ->when($searchValue, function ($query) use ($searchValue) {
                $query->where('user_name', 'like', "%{$searchValue}%");
                $query->orwhere('user_phone', 'like', "%{$searchValue}%");

                $query->orWhereHas('event', function ($q) use ($searchValue) {
                    $q->where('event_name', 'like', "%$searchValue%");
                    $searchPattern = '%' . $searchValue . '%';
                    $q->orWhere(function ($query) use ($searchValue, $searchPattern) {
                        $query->orWhereRaw("JSON_EXTRACT(all_venue_data, '$[*].event_venue') LIKE ?", [$searchPattern]);    
                        $query->orWhereJsonContains('all_venue_data', ['event_venue' => $searchValue]);
                    });
                });
                $query->orWhereHas('package', function ($q) use ($searchValue) {
                    $q->where('package_name', 'like', "%$searchValue%");
                });

            })
            
            ->when(isset($filters['status']), function ($query) use ($filters) {
                return $query->whereIn('status', $filters['status']);
            })
            ->when(isset($filters['test']), function ($query) use ($filters) {
                return $query->where('test', $filters['test']);
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }

    public function update(string $id, array $data): bool
    {
        return $this->eventlead->where('id', $id)->update($data);
    }
    public function delete(array $params): bool
    {
        $this->eventlead->where($params)->delete();
        return true;
    }
}
