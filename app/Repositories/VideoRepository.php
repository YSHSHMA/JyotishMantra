<?php

namespace App\Repositories;

use App\Contracts\Repositories\VideoRepositoryInterface;
use App\Models\Video;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class VideoRepository implements VideoRepositoryInterface
{
    public function __construct(
        private readonly Video         $video,
    )
    {
    }

    public function add(array $data): object
    {
        return $this->video->create($data);
    }


    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->video->withoutGlobalScope('translate')->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->video->with($relations)
                ->when(!empty($orderBy), function ($query) use ($orderBy) {
                    return $query->orderBy(array_key_first($orderBy),array_values($orderBy)[0]);
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
    ): Collection|LengthAwarePaginator
    {
        $query = $this->video
            ->select('videos.*', 'vc.name as category_name', 'vsc.name as subcategory_name')
            ->leftJoin('video_sub_categories as vsc', 'videos.subcategory_id', '=', 'vsc.id')
            ->leftJoin('video_categories as vc', 'vsc.category_id', '=', 'vc.id')
            ->with($relations)
            ->when($searchValue, function (Builder $query) use ($searchValue) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('videos.title', 'like', "%$searchValue%")
                      ->orWhere('videos.list_type', 'like', "%$searchValue%")
                      ->orWhere('videos.id', $searchValue)
                      ->orWhere('vc.name', 'like', "%$searchValue%")
                      ->orWhere('vsc.name', 'like', "%$searchValue%"); 
                });
            })
            ->when(isset($filters['title']), function (Builder $query) use ($filters) {
                $query->where('videos.title', $filters['title']);
            })
            ->when(isset($filters['category_name']), function (Builder $query) use ($filters) {
                $query->where('vc.name', $filters['category_name']);
            })
            ->when(isset($filters['subcategory_name']), function (Builder $query) use ($filters) {
                $query->where('vsc.name', $filters['subcategory_name']);
            })
            ->when(!empty($orderBy), function (Builder $query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        
        return $dataLimit == 'all'
            ? $query->get()
            : $query->paginate($dataLimit)->appends($filters);
    }


    public function update(string $id, array $data): bool
    {
        return $this->video->where('id', $id)->update($data);
    }


    public function delete(array $params): bool
    {
        $this->video->where($params)->delete();
        return true;
    }

}
