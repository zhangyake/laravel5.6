<?php

namespace App\Repositories;

use App\Models\Permission;


class PermissionRepository
{
    use BaseRepository;

    protected  $model;

    public function __construct(Permission $model)
    {
        $this->model = $model;
    }
}