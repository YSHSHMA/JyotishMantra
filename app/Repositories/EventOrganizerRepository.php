<?php

namespace App\Repositories;

use App\Contracts\Repositories\EventOrganizerRepositoryInterface;
use App\Models\EventOrganizer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class EventOrganizerRepository implements EventOrganizerRepositoryInterface
{
    public function __construct(
        private readonly EventOrganizer  $eventorganizer,
    ) {
    }

    public function add(array $data): string|object
    {
        return $this->eventorganizer->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->eventorganizer->withoutGlobalScope('translate')->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->eventorganizer->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

    public function getListWhere(array $orderBy = [], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->eventorganizer->with($relations)
        ->when($searchValue, function ($query) use ($searchValue) {
            $query->where('organizer_name', 'like', "%{$searchValue}%");
        })
        
        ->when(isset($filters['status']), function ($query) use ($filters) {
            return $query->where('status', $filters['status']);
        })
        ->when(isset($filters['is_approve']), function ($query) use ($filters) {
            return $query->where('is_approve', $filters['is_approve']);
        })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }

    public function update(string $id, array $data): bool
    {
        return $this->eventorganizer->where('id', $id)->update($data);
    }
    public function delete(array $params): bool
    {
        $this->eventorganizer->where($params)->delete();
        return true;
    }
}
