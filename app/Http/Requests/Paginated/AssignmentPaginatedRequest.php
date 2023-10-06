<?php

namespace App\Http\Requests\Paginated;

use Illuminate\Support\Facades\Log;

use App\Http\Requests\Paginated\AbstractPaginatedRequest;

class AssignmentPaginatedRequest extends AbstractPaginatedRequest
{
    protected array $sortFields = ['id', 'name', 'due', 'open_from',
        'open_until', 'results_from', 'results_until'];
}
