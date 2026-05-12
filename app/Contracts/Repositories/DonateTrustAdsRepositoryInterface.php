<?php

namespace App\Contracts\Repositories;

interface DonateTrustAdsRepositoryInterface extends RepositoryInterface
{
    public function sendMails($email,$subject,$message);
}

?>