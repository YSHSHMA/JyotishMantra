<?php

namespace App\Repositories;

use App\Contracts\Repositories\DonateTrustAdsRepositoryInterface;
use App\Models\DonateAds;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;

class DonateTrustAdsRepository implements DonateTrustAdsRepositoryInterface
{
    public function __construct(
        private readonly DonateAds  $donateAds,
    ) {}

    public function add(array $data): string|object
    {
        return $this->donateAds->create($data);
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->donateAds->withoutGlobalScope('translate')->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->donateAds->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

    public function getListWhere(array $orderBy = [], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->donateAds->with($relations)
            ->select('donate_ads.*')
            ->when(isset($filters['select_raw']), function ($query) use ($filters) {
                $query->selectRaw($filters['select_raw']);
            })

            ->when(isset($filters['joins']), function ($query) use ($filters) {
                foreach ($filters['joins'] as $join) {
                    $query->leftJoin($join['table'], function ($joinQuery) use ($join) {
                        $joinQuery->on($join['on'][0], '=', $join['on'][1]);
                        if (!empty($join['extra'])) {
                            foreach ($join['extra'] as $extra) {
                                $joinQuery->where($extra[0], $extra[1], $extra[2]);
                            }
                        }
                    });
                }
            })
            ->when($searchValue, function ($query) use ($searchValue) {
                $query->where('name', 'like', "%$searchValue%")->orWhere('id', $searchValue);
                $query->orWhere('ads_id', 'like', "%$searchValue%");
                $query->orWhereHas('category', function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%$searchValue%");
                });
                $query->orWhereHas('Trusts', function ($q) use ($searchValue) {
                    $q->where('trust_name', 'like', "%$searchValue%");
                });
                $query->orWhereHas('Purpose', function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%$searchValue%");
                });
            })

            ->when(isset($filters['status']), function ($query) use ($filters) {
                return $query->where('donate_ads.status', $filters['status']);
            })
            ->when(isset($filters['is_approve']), function ($query) use ($filters) {
                return $query->where('donate_ads.is_approve', $filters['is_approve']);
            })
            ->when(isset($filters['type']), function ($query) use ($filters) {
                return $query->where('donate_ads.type', $filters['type']);
            })
            ->when(isset($filters['trust_id']), function ($query) use ($filters) {
                return $query->where('donate_ads.trust_id', $filters['trust_id']);
            })
            ->when(isset($filters['purpura_id']), function ($query) use ($filters) {
                return $query->where('donate_ads.purpose_id', $filters['purpura_id']);
            })
            ->when((($filters['date_range_apply'] ?? 0) == 1), function ($query) {
                return $query->where(function ($q) {
                    $q->whereNull('set_requirement_date_range')
                        ->orWhere('set_requirement_date_range', '')
                        ->orWhere(function ($sub) {
                            $sub->whereRaw(
                                "? BETWEEN 
                                STR_TO_DATE(SUBSTRING_INDEX(set_requirement_date_range, ' - ', 1), '%Y-%m-%d') 
                                AND 
                                STR_TO_DATE(SUBSTRING_INDEX(set_requirement_date_range, ' - ', -1), '%Y-%m-%d')",
                                [date('Y-m-d')]
                            );
                        });
                });
            })
            ->when(isset($filters['group_by']), function ($query) use ($filters) {
                $query->groupBy($filters['group_by']);
            })
            ->when(isset($filters['having_raw']), function ($query) use ($filters) {
                $query->havingRaw($filters['having_raw']);
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
        return $this->donateAds->where('id', $id)->update($data);
    }


    public function delete(array $params): bool
    {
        $this->donateAds->where($params)->delete();
        return true;
    }
}
