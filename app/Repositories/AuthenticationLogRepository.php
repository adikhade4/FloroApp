<?php
namespace App\Repositories;
use App\Models\AuthenticationLog;


class AuthenticationLogRepository extends Repository
{
    public function __construct(AuthenticationLog $model)
    {
        $this->model = $model;
    }
}