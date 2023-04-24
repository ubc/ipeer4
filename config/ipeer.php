<?php

return [
    'version' => [
        'full' => '4.0.0',
        'mode' => config('app.env'),
        'debug' => config('app.debug')
    ],
    'paginate' => [
        'perPage' => 15,
        'sortBy' => 'id',
        'descending' => false,
    ],
];
