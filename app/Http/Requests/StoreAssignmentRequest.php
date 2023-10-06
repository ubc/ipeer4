<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

use App\Models\Assignment;
use App\Rules\BoolStr;
use App\Rules\DateTimeIso8601;
use App\Traits\Request\PrepareBool;

class StoreAssignmentRequest extends FormRequest
{
    use PrepareBool;

    public function authorize(): bool
    {
        return $this->user()->can('create', [Assignment::class, $this->course]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'string|required',
            'desc' => 'string|nullable',
            'has_self_eval' => [new BoolStr, 'nullable'],
            'is_published' => [new BoolStr, 'nullable'],
            'due' => [new DateTimeIso8601, 'required'],
            'open_from' => [new DateTimeIso8601, 'required', 'before:due'],
            'open_until' => [new DateTimeIso8601, 'nullable',
                             'after_or_equal:due'],
            'results_from' => [new DateTimeIso8601, 'nullable',
                               'after_or_equal:due'],
            'results_until' => [new DateTimeIso8601, 'nullable',
                                'after:results_from'],
            'course_id' => 'required|exists:App\Models\Course,id'
        ];
    }

    /**
     * Bool values might be strings, change them to actual bools
     */
    protected function prepareForValidation(): void
    {
        $this->prepareBool('has_self_eval');
        $this->prepareBool('is_published');
    }
}
