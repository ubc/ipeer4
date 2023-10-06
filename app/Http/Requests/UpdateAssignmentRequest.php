<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\Assignment;
use App\Rules\BoolStr;
use App\Rules\DateTimeIso8601;
use App\Traits\Request\PrepareBool;

class UpdateAssignmentRequest extends FormRequest
{
    use PrepareBool;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', [Assignment::class, $this->course]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'string|nullable',
            'desc' => 'string|nullable',
            'has_self_eval' => [new BoolStr, 'nullable'],
            'is_published' => [new BoolStr, 'nullable'],
            'due' => [new DateTimeIso8601, 'nullable'],
            'open_from' => [new DateTimeIso8601, 'nullable', 'before:due'],
            'open_until' => [new DateTimeIso8601, 'nullable',
                             'after_or_equal:due'],
            'results_from' => [new DateTimeIso8601, 'nullable',
                               'after_or_equal:due'],
            'results_until' => [new DateTimeIso8601, 'nullable',
                                'after:results_from'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->prepareBool('has_self_eval');
        $this->prepareBool('is_published');
    }
}
