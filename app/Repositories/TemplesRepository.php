<?php

namespace App\Repositories;

use App\Contracts\Repositories\TemplesRepositoryInterface;
use App\Models\Temple;
use App\Models\TempleServicePackages;
use App\Models\TempleServicePrice;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class TemplesRepository implements TemplesRepositoryInterface
{

    public function __construct(private readonly Temple $temple,TempleServicePackages $templePackage,TempleServicePrice $templePackagePrice)
    {
        $this->templePackage = $templePackage;
        $this->templePackagePrice = $templePackagePrice;
    }
    

    public function add(array $data): string|object
    {
        return $this->temple->create($data);
    }
    public function addpackage(array $data): string|object
    {
        return $this->templePackage->create($data);
    }
    public function addpackageprice(array $data): string|object
    {
        return $this->templePackagePrice->create($data);
    }

   
    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, ?int $offset = null): Collection|LengthAwarePaginator
    {
        

    }
   

    // public function getFirstWhere(array $params, array $relations = []): ?Model
    // {
    //     return $this->socialMedia->with($relations)->where($params)->first();
    // }
    

    // public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    // {
    //     $query = $this->temple->with($relations)
    //         ->when(!empty($orderBy), function ($query) use ($orderBy) {
    //             return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
    //         });
    //     return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    // }

    public function getListWhere(array $orderBy=[], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->temple->with($relations)
            ->when($searchValue, function ($query) use($searchValue){
                $query->Where('name', 'like', "%$searchValue%")->orWhere('id', $searchValue);
                $query->orwhereHas('cities', function ($q) use ($searchValue) {
                    $q->where('city', 'like', "%$searchValue%");
                });
                $query->orWhereHas('states', function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%$searchValue%");
                });
            })
            ->when(isset($filters['name']), function ($query) use($filters) {
                return $query->where(['name' => $filters['name']]);
            })            
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy),array_values($orderBy)[0]);
            });

        $filters += ['searchValue' =>$searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);

    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->temple->withoutGlobalScope('translate')->where($params)->with($relations)->first();
    }

    public function getFirstWhereWithoutGlobalScope(array $params, array $relations = []): ?Model
    {
        return $this->temple->withoutGlobalScopes()->where($params)->with($relations)->first();
    }



    public function update(string $id, array $data): bool
    {
        return $this->temple->where('id', $id)->update($data);
    }
    public function editpackage(string $id, array $data): bool
    {
        $package = $this->templePackage->find($id);
        if ($package) {
            return $package->update($data);
        }
        return false;
    }
    
    public function editpackageprice(string $id, array $data): ?TempleServicePrice
    {
        $packageprice = $this->templePackagePrice->find($id);
        if ($packageprice) {
            $packageprice->update($data);
            return $packageprice; //  updated model return karega
        }
        return null;
    }
    
    

    public function delete(array $params): bool
    {
        $this->temple->where($params)->delete();
        return true;
    }

}

?>