<?php

namespace App\Contracts\Repositories;

interface EventsRepositoryInterface extends RepositoryInterface
{

    public function sendMails(array $data):bool;
    
}