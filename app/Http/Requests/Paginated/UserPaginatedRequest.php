<?php

namespace App\Http\Requests\Paginated;

use Illuminate\Support\Facades\Log;

use App\Http\Requests\Paginated\AbstractPaginatedRequest;

class UserPaginatedRequest extends AbstractPaginatedRequest
{
    protected array $sortFields = [
        'id',
        'username',
        'name',
        'email',
        'created_at',
        'updated_at',
    ];
}
