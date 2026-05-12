<?php

namespace App\Repositories;

use App\Contracts\Repositories\ChadhavaRepositoryInterface;
use App\Models\Chadhava;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChadhavaRepository implements ChadhavaRepositoryInterface
{
    public function __construct(
        private readonly Chadhava      $chadhava,
        private readonly Translation   $translation,
        private readonly Tag           $tag,
    )
    {
    }

    public function addchadhavaTag(object $request, object $chadhava): void
    {
        $tagIds = [];
        if ($request->tags != null) {
            $tags = explode(",", $request->tags);
        }
        if (isset($tags)) {
            foreach ($tags as $value) {
                $tag = $this->tag->firstOrNew(
                    ['tag' => trim($value)]
                );
                $tag->save();
                $tagIds[] = $tag->id;
            }
        }
        $chadhava->tags()->sync($tagIds);
    }
    public function add(array $data): string|object
    { 
        return $this->chadhava->create($data);
    }

    public function getChadhavaFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->chadhava->where($params)->with($relations)->first();
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->chadhava->withoutGlobalScope('translate')->with($relations)->where($params)->first();
    }
    public function getFirstWhereActive(array $params, array $relations = []): ?Model
    {
        return $this->chadhava->active()->where($params)->with($relations)->first();
    }

    public function getFirstWhereWithoutGlobalScope(array $params, array $relations = []): ?Model
    {
        return $this->chadhava->withoutGlobalScopes()->where($params)->with($relations)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->chadhava->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy),array_values($orderBy)[0]);
            });
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

    public function getListWhere(array $orderBy=[], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->chadhava
            ->when($searchValue, function ($query) use($searchValue){
                $query->Where('name', 'like', "%$searchValue%")->orWhere('id', $searchValue);
            })
            ->when(isset($filters['name']), function ($query) use($filters) {
                return $query->where(['name' => $filters['name']]);
            })
            ->when(isset($filters['status']), function ($query) use($filters) {
                return $query->where(['status' => $filters['status']]);
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy),array_values($orderBy)[0]);
            })->when(isset($filters['category_id']) && $filters['category_id'] != 'all', function ($query) use ($filters) {
                return $query->where(['category_id' => $filters['category_id']]);
            })->when(isset($filters['sub_category_id']) && $filters['sub_category_id'] != 'all', function ($query) use ($filters) {
                return $query->where(['sub_category_id' => $filters['sub_category_id']]);
            })->when(isset($filters['sub_sub_category_id']) && $filters['sub_sub_category_id'] != 'all', function ($query) use ($filters) {
                return $query->where(['sub_sub_category_id' => $filters['sub_sub_category_id']]);
            });

        $filters += ['searchValue' =>$searchValue];
        //dd($query->get());
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);

    }

    public function update(string $id, array $data): bool
    {
        return $this->chadhava->where('id', $id)->update($data);
    }

    public function delete(array $params): bool
    {
        $this->chadhava->where($params)->delete();
        return true;
    }

}