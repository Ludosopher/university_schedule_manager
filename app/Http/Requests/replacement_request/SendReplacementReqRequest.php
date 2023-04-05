<?php

namespace App\Http\Requests\replacement_request;

use App\ReplacementRequest;
use Illuminate\Foundation\Http\FormRequest;

class SendReplacementReqRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $replacement_request_status_groups = config('enum.replacement_request_status_groups');
        $data = $this->request->all();
        
        return [
            'updating_id' => 'required|integer|exists:App\ReplacementRequest,id',
            'is_sent' => 'nullable|boolean',
            'replaceable_lesson_id' => 'required|integer|exists:App\Lesson,id',
            'replaceable_lesson_id' => function ($attribute, $value, $fail) use ($data, $replacement_request_status_groups) {
                $is_using = ReplacementRequest::whereIn('status_id', $replacement_request_status_groups['active'])
                                                ->where(function ($query) use ($data, $value) {
                                                    $query->orWhere([
                                                        ['replacing_lesson_id', $value],
                                                        ['replacing_date', $data['replaceable_date']],
                                                        ])->orWhere([
                                                            ['replaceable_lesson_id', $value],
                                                            ['replaceable_date', $data['replaceable_date']],
                                                        ]);
                                                })->exists();
                if ($is_using) $fail(__('user_validation.lesson_is_in_replacement_already')); 
            },
            'replaceable_date' => 'nullable|date',
            'replacing_lesson_id' => 'required|integer|exists:App\Lesson,id',
            'replacing_lesson_id' => function ($attribute, $value, $fail) use ($data, $replacement_request_status_groups) {
                $is_using = ReplacementRequest::whereIn('status_id', $replacement_request_status_groups['active'])
                                                ->where(function ($query) use ($data, $value) {
                                                    $query->orWhere([
                                                        ['replacing_lesson_id', $value],
                                                        ['replacing_date', $data['replacing_date']],
                                                        ])->orWhere([
                                                            ['replaceable_lesson_id', $value],
                                                            ['replaceable_date', $data['replacing_date']],
                                                        ]);
                                                })->exists();
                if ($is_using) $fail(__('user_validation.lesson_is_in_replacement_already')); 
            },
            'replacing_date' => 'nullable|date',
            'is_regular' => 'nullable|boolean',
            'replacing_teacher_id' => 'required|integer|exists:App\Teacher,id',
        ];
    }
}
