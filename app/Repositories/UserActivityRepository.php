<?php
namespace App\Repositories;
use App\Models\UserActivity;


class UserActivityRepository extends Repository
{
    public function __construct(UserActivity $model)
    {
        $this->model = $model;
    }
}