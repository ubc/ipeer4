<?php

namespace App\Http\Requests\Paginated;

use Illuminate\Support\Facades\Log;

use App\Http\Requests\Paginated\AbstractPaginatedRequest;

class CourseUserPaginatedRequest extends AbstractPaginatedRequest
{
    protected array $sortFields = ['id', 'name', 'created_at', 'updated_at',
        'username', 'email', 'role_name'];
}
