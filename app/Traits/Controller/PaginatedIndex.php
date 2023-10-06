<?php

namespace App\Traits\Controller;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

use App\Http\Requests\Paginated\AbstractPaginatedRequest;

trait PaginatedIndex {
    /**
     * Pagination requests need to return a set of pagination params that are
     * used by the frontend, this abstracts out a paginated index call that
     * can be used by the controllers to avoid duplicating a lot of code.
     *
     * @param $request - The paginated request contains validated pagination
     * params.
     * @param $baseQuery - For non-nested models, this can just be something
     * like User::query(). But for nested models, we might need to use
     * something like $courses->users().
     * @param $filter - List of fields that we'll look for the search term in.
     * E.g.: For users, we'll look in emails, name, username, etc.
     */
    protected function paginatedIndex(
        AbstractPaginatedRequest $request,
        Builder|Relation $baseQuery,
        array $filterFields
    ): array {
        $data = $request->validated();

        $query = $baseQuery->orderBy($data['sort_by'], $data['sort_dir']);
        if ($data['filter']) {
            $term = '%' . escapeLike($data['filter']) . '%';
            $query = $query->where(
                function ($q) use ($term, $filterFields) {
                    foreach ($filterFields as $i => $field) {
                        if ($i == 0) $q = $q->where($field, 'LIKE', $term);
                        else $q = $q->orWhere($field, 'LIKE', $term);
                    }
                }
            );
        }
        $ret = $query->paginate($data['per_page']);
        // withQueryString() means we include the pagination params originally
        // sent
        return array_merge($ret->withQueryString()->toArray(),
            // additional params for Quasar pagination
            ['sort_by' => $data['sort_by'], 'descending' => $data['descending']]
        );
    }
}
