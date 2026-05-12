<?php

namespace App\Repositories;

use App\Contracts\Repositories\EventsReviewRepositoryInterface;
use App\Models\EventsReview;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class EventsReviewRepository implements EventsReviewRepositoryInterface
{
    public function __construct(
        private readonly EventsReview  $eventreview,
    )
    {
    }

    public function add(array $data): string|object
    {
        return $this->eventreview->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->eventreview->withoutGlobalScope('translate')->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->eventreview->with($relations)
                ->when(!empty($orderBy), function ($query) use ($orderBy) {
                    return $query->orderBy(array_key_first($orderBy),array_values($orderBy)[0]);
                });

        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

  public function getListWhere(array $orderBy = [], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->eventreview
            ->when($searchValue, function ($query) use ($searchValue) {
                $query->where('comment', 'like', "%$searchValue%")
                ->orWhere('id', $searchValue);
            })
            ->when(isset($filters['event_id']), function ($query) use ($filters) {
                return $query->where('event_id', $filters['event_id']);
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }

  public function update(string $id, array $data): bool
{
    return $this->eventreview->where('id', $id)->update($data);
}


    public function delete(array $params): bool
    {
        $this->eventreview->where($params)->delete();
        return true;
    }

}
