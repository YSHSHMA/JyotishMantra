<?php

namespace App\Repositories;

use App\Contracts\Repositories\EventsRepositoryInterface;
use App\Models\Events;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;

class EventsRepository implements EventsRepositoryInterface
{
    public function __construct(
        private readonly Events  $events,
    ) {}

    public function add(array $data): string|object
    {
        return $this->events->create($data);
    }

    public function sendMails(array $data): bool
    {
        try {

            // Send email
            // Mail::send([], [], function ($mail) use ($data, $configEmail) {
            //     $mail->to($data['email'])
            //         ->subject($data['subject'])
            //         ->from($configEmail['email'], $configEmail['username'])
            //         ->html($data['message']);
            // });

            $configEmail = \App\Models\EmailSetup::where('type', 'event')->first();
            if (!empty($configEmail)) {
                $host = $configEmail['host'];
                $port = $configEmail['port'];
                $encryption = $configEmail['encryption'];

                $username = $configEmail['emailid'];
                $password = $configEmail['password'];
                $fromAddress = $configEmail['username'];
                $fromName = ucwords($configEmail['mailername']);

                $transport = \Symfony\Component\Mailer\Transport::fromDsn("smtp://$username:$password@$host:$port?encryption=$encryption");
                $mailer = new \Symfony\Component\Mailer\Mailer($transport);
                $email = (new \Symfony\Component\Mime\Email())
                    ->from(new \Symfony\Component\Mime\Address($fromAddress, $fromName))
                    ->to($data['email'])
                    ->subject($data['subject'])
                    ->html($data['message']);
                $mailer->send($email);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->events->withoutGlobalScope('translate')->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->events->with($relations)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                return $query->orderBy(array_key_first($orderBy), array_values($orderBy)[0]);
            });

        return $dataLimit == 'all' ? $query->get() : $query->paginate($dataLimit);
    }

    public function getListWhere(array $orderBy = [], string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = DEFAULT_DATA_LIMIT, int $offset = null): Collection|LengthAwarePaginator
    {
        $query = $this->events->with($relations)
            ->when($searchValue, function ($query) use ($searchValue) {
                $query->where('event_name', 'like', "%{$searchValue}%");
                $query->orWhereHas('organizers', function ($q) use ($searchValue) {
                    $q->where('organizer_name', 'like', "%$searchValue%");
                    $q->orWhere('full_name', 'like', "%$searchValue%");
                });
                $query->orWhereHas('categorys', function ($q) use ($searchValue) {
                    $q->where('category_name', 'like', "%$searchValue%");
                });

                $searchPattern = '%' . $searchValue . '%';
                $query->orWhere(function ($query) use ($searchValue, $searchPattern) {
                    $query->orWhereRaw("JSON_EXTRACT(all_venue_data, '$[*].en_event_venue') LIKE ?", [$searchPattern])
                        ->orWhereRaw("JSON_EXTRACT(all_venue_data, '$[*].en_event_country') LIKE ?", [$searchPattern])
                        ->orWhereRaw("JSON_EXTRACT(all_venue_data, '$[*].en_event_state') LIKE ?", [$searchPattern])
                        ->orWhereRaw("JSON_EXTRACT(all_venue_data, '$[*].en_event_cities') LIKE ?", [$searchPattern]);

                    $query->orWhereJsonContains('all_venue_data', ['en_event_venue' => $searchValue])
                        ->orWhereJsonContains('all_venue_data', ['en_event_country' => $searchValue])
                        ->orWhereJsonContains('all_venue_data', ['en_event_state' => $searchValue])
                        ->orWhereJsonContains('all_venue_data', ['en_event_cities' => $searchValue]);
                });
            })

            ->when(isset($filters['price']), function ($query) use ($filters) {
                $prices = $filters['price'];
                $query->where(function ($q) use ($prices) {
                    foreach ($prices as $priceRange) {
                        list($minPrice, $maxPrice) = explode('-', $priceRange);

                        $q->orWhere(function ($subQuery) use ($minPrice, $maxPrice) {
                            for ($j = 0; $j < 10; $j++) {
                                for ($i = 0; $i < 10; $i++) {
                                    $subQuery->orWhereRaw("
                                    CAST(JSON_UNQUOTE(JSON_EXTRACT(all_venue_data ,'$[$j].package_list[$i].price_no')) AS UNSIGNED) BETWEEN ? AND ?
                                ", [$minPrice, $maxPrice]);
                                }
                            }
                        });
                    }
                });
            })

            ->when(isset($filters['venue_data']), function ($query) use ($filters) {
                $venueDataArray = $filters['venue_data'];

                $query->where(function ($q) use ($venueDataArray) {
                    foreach ($venueDataArray as $venueData) {
                        for ($i = 0; $i < 10; $i++) {
                            $q->orWhereRaw("
                                JSON_UNQUOTE(JSON_EXTRACT(all_venue_data, '$[$i].en_event_venue')) = ?
                            ", [$venueData]);
                        }
                    }
                });
            })

            ->when((isset($filters['upcoming']) && $filters['upcoming'] == 1), function ($query) use ($filters) {
                $today = date('Y-m-d');
                $futureDate = date('Y-m-d', strtotime('+25 days'));
                $query->where(function ($q) use ($today, $futureDate) {
                    for ($i = 0; $i < 10; $i++) {
                        $q->orWhereRaw("
                            JSON_UNQUOTE(JSON_EXTRACT(all_venue_data, '$[$i].date')) BETWEEN ? AND ?
                        ", [$today, $futureDate]);
                    }
                });
            })
            ->when((isset($filters['active_event']) && $filters['active_event'] == 1), function ($query) use ($filters) {
                $today = date('Y-m-d');
                $currentTime = date('H:i:s');
                $futureDate = date('Y-m-d', strtotime('+90 days'));

                $query->where(function ($q) use ($today, $futureDate, $currentTime) {
                    for ($i = 0; $i < 50; $i++) {
                        $q->orWhere(function ($subQuery) use ($i, $today, $futureDate, $currentTime) {
                            $subQuery->whereRaw("
                                JSON_UNQUOTE(JSON_EXTRACT(all_venue_data, '$[$i].date')) BETWEEN ? AND ?
                            ", [$today, $futureDate])
                                ->whereRaw("
                                (
                                    JSON_UNQUOTE(JSON_EXTRACT(all_venue_data, '$[$i].date')) > ? OR
                                    (
                                        JSON_UNQUOTE(JSON_EXTRACT(all_venue_data, '$[$i].date')) = ? AND
                                        STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(all_venue_data, '$[$i].end_time')), '%h:%i %p') > ?
                                    )
                                )
                            ", [$today, $today, $currentTime]);
                        });
                    }
                });
            })
            ->when((isset($filters['upcomming']) && $filters['upcomming'] == 1), function ($query) use ($filters) {
                // $today = date('Y-m-d',strtotime('+3 days')); 
                // $futureDate = date('Y-m-d', strtotime('+27 days'));
                // $query->where(function ($q) use ($today, $futureDate) {   
                //     for ($i=0; $i < 10 ; $i++) { 
                //         $q->orWhereRaw("
                //             JSON_UNQUOTE(JSON_EXTRACT(all_venue_data, '$[$i].date')) BETWEEN ? AND ?
                //         ", [$today, $futureDate]);   
                //     }
                // });

                $query->where('is_approve', 1)->where('status', 1)->whereRaw(" DATE(?) < STR_TO_DATE(
                    IF(INSTR(start_to_end_date, ' - ') > 0, 
                    SUBSTRING_INDEX(start_to_end_date, ' - ', 1), 
                    start_to_end_date
                    ), '%Y-%m-%d') ", [now()->format('Y-m-d')]);
            })

            ->when((isset($filters['completed']) && $filters['completed'] == 1), function ($query) use ($filters) {
                // $lastDate = date('Y-m-d');
                // $query->where(function ($q) use ($lastDate) {
                //     $q->whereRaw("
                //         JSON_UNQUOTE(
                //             JSON_EXTRACT(
                //                 all_venue_data, 
                //                 CONCAT('$[', JSON_LENGTH(all_venue_data) - 1, '].date')
                //             )
                //         ) < ?
                //     ", [$lastDate]);
                // });
                $query->where('is_approve', 1)
                    ->where('status', 1)
                    ->whereRaw("
        DATE(?) > STR_TO_DATE(
            IF(INSTR(start_to_end_date, ' - ') > 0, 
               SUBSTRING_INDEX(start_to_end_date, ' - ', -1), 
               start_to_end_date
            ), '%Y-%m-%d')
    ", [now()->format('Y-m-d')]);
            })



            ->when((isset($filters['global_event']) && $filters['global_event'] == 1), function ($query) use ($filters) {
                // $today = date('Y-m-d');
                // $futureDate = date('Y-m-d', strtotime('+2 days'));
                // $query->where(function ($q) use ($today, $futureDate) {
                //     for ($i = 0; $i < 10; $i++) {
                //         $q->orWhereRaw("
                //             JSON_UNQUOTE(JSON_EXTRACT(all_venue_data, '$[$i].date')) BETWEEN ? AND ?
                //         ", [$today, $futureDate]);
                //     }
                // });
                $query->where('is_approve', 1)->where('status', 1)->where(function ($query) {
                    $query->whereRaw("
            DATE(?) BETWEEN 
            STR_TO_DATE(SUBSTRING_INDEX(start_to_end_date, ' - ', 1), '%Y-%m-%d') 
            AND 
            STR_TO_DATE(SUBSTRING_INDEX(start_to_end_date, ' - ', -1), '%Y-%m-%d')
        ", [now()->format('Y-m-d')])
                        ->orWhereRaw("
            DATE(?) = 
            STR_TO_DATE(start_to_end_date, '%Y-%m-%d')
        ", [now()->format('Y-m-d')]);
                });
            })

            ->when(isset($filters['language']), function ($query) use ($filters) {
                $language = $filters['language'];
                $query->where(function ($q) use ($language) {
                    foreach ($language as $venueData) {
                        $q->orWhereRaw("language = ?  ", [$venueData]);
                    }
                });
            })

            ->when(isset($filters['category_id']), function ($query) use ($filters) {
                $category_id = $filters['category_id'];
                $query->where(function ($q) use ($category_id) {
                    foreach ($category_id as $category) {
                        $q->orWhereRaw("category_id = ?  ", [$category]);
                    }
                });
            })

            ->when(isset($filters['organizer']), function ($query) use ($filters) {
                return $query->where('organizer_by', $filters['organizer']);
            })
            ->when(isset($filters['event_organizer_id']), function ($query) use ($filters) {
                return $query->where('event_organizer_id', $filters['event_organizer_id']);
            })
            ->when(isset($filters['is_approve']), function ($query) use ($filters) {
                return $query->where('is_approve', $filters['is_approve']);
            })

            ->when(isset($filters['is_approveIN']), function ($query) use ($filters) {
                return $query->whereIn('is_approve', $filters['is_approveIN']);
            })
            ->when(isset($filters['status_and_isactive']), function ($query) use ($filters) {
                return $query->where(function ($q) {
                    $q->where(function ($q2) {
                        $q2->where('status', 0)
                            ->whereIn('is_approve', [0, 1, 2, 3, 4]);
                    })
                        ->orWhere(function ($q2) {
                            $q2->where('status', 1)
                                ->whereIn('is_approve', [0, 2, 3, 4]);
                        });
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

    public function update(string $id, array $data): bool
    {
        return $this->events->where('id', $id)->update($data);
    }
    public function delete(array $params): bool
    {
        $this->events->where($params)->delete();
        return true;
    }
}
