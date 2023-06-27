<?php

namespace App\Http\Requests\Paginated;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

use App\Rules\BoolStr;

abstract class AbstractPaginatedRequest extends FormRequest
{
    protected array $sortFields = ['id'];

    /**
     * Determine if the user is authorized to make this request.
     */
    /*public function authorize(): bool
    {
        return false;
    }*/

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'per_page' => 'integer|max:100|min:1',
            'descending' => 'boolean',
            'sort_by' => Rule::in($this->sortFields),
            'sort_dir' => Rule::in(['desc', 'asc']),
            'filter' => 'string',
        ];
    }

    /**
     * Add derived and default values to the validated parameters before
     * running the validator.
     * Pagination params are optional, in cases where they are omitted, we need
     * to provide default values so that pagination will still work.
     * Frontend Quasar pagination also wants some params not provided, so we
     * add them here.
     */
    protected function prepareForValidation(): void
    {
        $data = $this->all();
        // set default values for empty params
        $data['per_page'] ??= config('ipeer.paginate.perPage');
        $data['descending'] = toBoolean($data['descending'] ??
                              config('ipeer.paginate.descending'));
        $data['sort_by'] ??= config('ipeer.paginate.sortBy');
        $data['sort_dir'] = $data['descending'] ? 'desc' : 'asc';
        $data['filter'] ??= '';
        $this->merge($data);
    }
}
