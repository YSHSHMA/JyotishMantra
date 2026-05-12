<?php

namespace App\Repositories;

use App\Contracts\Repositories\FaqRepositoryInterface;
use App\Models\FAQ;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FaqRepository implements FaqRepositoryInterface
{
    public function __construct(
        private readonly FAQ       $faq,
    )
    {
    }

    public function add(array $data): string|object
    {
        return $this->faq->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->faq->withoutGlobalScope('translate')->with($relations)->where($params)->first();
    }

    public function getFAQFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->faq->where($params)->with($relations)->first();
    }
    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->faq->with($relations)
                ->when(!empty($orderBy), function ($query) use ($orderBy) {
                    return $query->orderBy(array_key_first($orderBy),array_values($orderBy)[0]);
                });
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

    public function getListWhere(array $orderBy=[], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->faq
            ->when($searchValue, function ($query) use($searchValue){
                $query->Where('question', 'like', "%$searchValue%")->orWhere('id', $searchValue);
            })
            ->when(isset($filters['question']), function ($query) use($filters) {
                return $query->where(['question' => $filters['question']]);
            })
            ->when(isset($filters['status']), function ($query) use($filters) {
                return $query->where(['status' => $filters['status']]);
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy),array_values($orderBy)[0]);
            });

        $filters += ['searchValue' =>$searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);

    }

    public function update(string $id, array $data): bool
    {
        return $this->faq->where('id', $id)->update($data);
    }

    public function delete(array $params): bool
    {
        $this->faq->where($params)->delete();
        return true;
    }

}
