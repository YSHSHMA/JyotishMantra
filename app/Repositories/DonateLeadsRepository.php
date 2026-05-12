<?php

namespace App\Repositories;

use App\Contracts\Repositories\DonateLeadsRepositoryInterface;
use App\Models\DonateAds;
use App\Models\DonateLeads;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;

class DonateLeadsRepository implements DonateLeadsRepositoryInterface
{
    public function __construct(
        private readonly DonateLeads  $donateLead,
    ) {}

    public function add(array $data): string|object
    {
        return $this->donateLead->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->donateLead->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->donateLead->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

    public function getListWhere(array $orderBy = [], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->donateLead->with($relations)
            ->when($searchValue, function ($query) use ($searchValue) {
                $query->where('type', 'like', "%$searchValue%")->orWhere('id', $searchValue);
            
                $query->orWhereHas('users', function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%$searchValue%");
                    $q->orWhere('phone', 'like', "%$searchValue%");
                });
                $query->orWhereHas('Trusts', function ($q) use ($searchValue) {
                    $q->where('trust_name', 'like', "%$searchValue%");
                });
                $query->orWhereHas('AdsDonate', function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%$searchValue%");
                });
            })

            ->when(isset($filters['status']), function ($query) use ($filters) {
                if (is_array($filters['status'])) {
                    return $query->whereIn('status', $filters['status']);
                } else {
                    return $query->where('status', $filters['status']);
                }
            })
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        $filters += ['searchValue' => $searchValue];
        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit)->appends($filters);
    }

    public function sendMails($email, $subject, $message)
    {
        try {
            Mail::raw($message, function ($mail) use ($email, $subject) {
                $mail->to($email)
                    ->subject($subject)
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });
            return true;
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function update(string $id, array $data): bool
    {
        return $this->donateLead->where('id', $id)->update($data);
    }


    public function delete(array $params): bool
    {
        $this->donateLead->where($params)->delete();
        return true;
    }
}
