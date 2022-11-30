<?php

namespace App;

use App\Helpers\LessonHelpers;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Lesson extends Model
{
    use Sortable;
    public $sortable = ['name', 'lesson_type_id', 'week_day_id','weekly_period_id', 'class_period_id', 'group_id', 'teacher_id', 'profession_level_name'];

    public function professionLevelNameSortable($query, $direction)
    {
        return $query->join('teachers', 'lessons.teacher_id', '=', 'teachers.id')
                    ->orderBy('last_name', $direction)
                    ->select('lessons.*');
    }

    public function class_period()
    {
        return $this->belongsTo(ClassPeriod::class);
    }

    public function lesson_room()
    {
        return $this->belongsTo(LessonRoom::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function week_day()
    {
        return $this->belongsTo(WeekDay::class);
    }

    public function weekly_period()
    {
        return $this->belongsTo(WeeklyPeriod::class);
    }

    public function lesson_type()
    {
        return $this->belongsTo(LessonType::class);
    }

    public $additional_attributes = ['groups_name'];

    public function getGroupsNameAttribute()
    {
        $study_degree = $this->groups[0]->study_degree->abbreviation;
        $study_form = $this->groups[0]->study_form->abbreviation;
        $faculty = $this->groups[0]->faculty->abbreviation;
        $cours = $this->groups[0]->course->number;

        $groups_name = "{$study_degree}.{$study_form}.{$faculty}-{$cours}-";

        if (count($this->groups) > 1) {
            $variative_part_arr = [];
            foreach ($this->groups as $group) {
                $study_program = $group->study_program->abbreviation;
                $variative_part_arr[] = $study_program;
            }
            return $groups_name.'['.implode('; ', $variative_part_arr).']';
        }

        $study_program = $this->groups[0]->study_program->abbreviation;
        $study_orientation = mb_strtolower($this->groups[0]->study_orientation->abbreviation);
        $additional_id = isset($this->groups[0]->additional_id) ? "/$this->additional_id" : "";

        return "{$groups_name}{$study_program}({$study_orientation}){$additional_id}";
    }

    public static function rules($request)
    {
        return [
            'name' => 'required|string',
            'lesson_type_id' => 'required|integer|exists:App\LessonType,id',
            'week_day_id' => 'required|integer|exists:App\WeekDay,id',
            'weekly_period_id' => 'required|integer|exists:App\WeeklyPeriod,id',
            'class_period_id' => 'required|integer|exists:App\ClassPeriod,id',
            'group_id' => 'required|array',
            'group_id' => function ($attribute, $value, $fail) use ($request) {
                if (LessonHelpers::searchSameLesson($request->all(), $attribute)) $fail('В указанном месте расписания данная группа уже занята');
            },
            'teacher_id' => 'required|integer|exists:App\Teacher,id',
            'teacher_id' => function ($attribute, $value, $fail) use ($request) {
                if (LessonHelpers::searchSameLesson($request->all(), $attribute)) $fail('В указанном месте расписания данный преподаватель уже занят');
            },
            'lesson_room_id' => 'required|integer|exists:App\LessonRoom,id',
            'lesson_room_id' => function ($attribute, $value, $fail) use ($request) {
                if (LessonHelpers::searchSameLesson($request->all(), $attribute)) $fail('В указанном месте расписания данная аудитория уже используется');
            },
            'updating_id' => 'nullable|integer|exists:App\Lesson,id',
            'date' => 'nullable|date',
        ];
    }

    public static function filterRules()
    {
        return [
            'name' => 'nullable|string',
            'lesson_type_id' => 'nullable|array',
            'week_day_id' => 'nullable|array',
            'weekly_period_id' => 'nullable|array',
            'class_period_id' => 'nullable|array',
            'group_id' => 'nullable|array',
            'teacher_id' => 'nullable|array',
            'lesson_room_id' => 'nullable|array',
            'week_number' => 'nullable|string',
        ];
    }

    public static function filterReplacementRules()
    {
        return [
            'week_day_id' => 'nullable|array',
            'weekly_period_id' => 'nullable|array',
            'class_period_id' => 'nullable|array',
            'faculty_id' => 'nullable|array',
            'department_id' => 'nullable|array',
            'professional_level_id' => 'nullable|array',
            'position_id' => 'nullable|array',
            'lesson_room_id' => 'nullable|array',
            'schedule_position' => 'nullable|array',
            'week_data' => 'nullable|string',

            'replace_rules.*.week_day_id' => 'nullable|integer|exists:App\WeekDay,id',
            'replace_rules.*.weekly_period_id' => 'nullable|integer|exists:App\WeeklyPeriod,id',
            'replace_rules.*.class_period_id' => 'nullable|integer|exists:App\ClassPeriod,id',
            'replace_rules.*.teacher_id' => 'nullable|integer|exists:App\Teacher,id',
        ];
    }

    public static function attrNames()
    {
        return [
            'name' => 'name',
            'lesson_type_id' => 'lesson type',
            'week_day_id' => 'week day',
            'weekly_period_id' => 'weekly period',
            'class_period_id' => 'class period',
            'group_id' => 'group',
            'teacher_id' => 'teacher',
            'lesson_room_id' => 'lesson room',
        ];
    }

    public static function filterConditions()
    {
        return [
            'name' => [
                'method' => 'where',
                'operator' => 'like'
            ],
            'lesson_type_id' => [
                'method' => 'whereIn'
            ],
            'week_day_id' => [
                'method' => 'whereIn'
            ],
            'weekly_period_id' => [
                'method' => 'whereIn'
            ],
            'class_period_id' => [
                'method' => 'whereIn'
            ],
            'group_id' => [
                'method' => 'whereHas',
                'operator' => [
                    'id' => [
                        'method' => 'where',
                        'operator' => '='
                    ],
                ],
                'eager_field' => 'groups',
            ],
            'teacher_id' => [
                'method' => 'whereIn',
            ],
            'lesson_room_id' => [
                'method' => 'whereIn',
            ],
            'week_number' => [
                'db_field' => 'WEEK(date) = ? OR date IS NULL',
                'method' => 'whereRaw',
                'calculated_value' => function ($week_number) {
                    return date('W', strtotime($week_number));
                } 
            ],
        ];
    }

    public static function filterReplacementConditions()
    {
        return [
            'week_day_id' => [
                'operator' => 'multi_not_equal'
            ],
            'weekly_period_id' => [
                'operator' => 'multi_not_equal'
            ],
            'class_period_id' => [
                'operator' => 'multi_not_equal'
            ],
            'faculty_id' => [
                'operator' => 'multi_not_equal'
            ],
            'department_id' => [
                'operator' => 'multi_not_equal'
            ],
            'professional_level_id' => [
                'operator' => 'multi_not_equal'
            ],
            'position_id' => [
                'operator' => 'multi_not_equal'
            ],
            'lesson_room_id' => [
                'operator' => 'multi_not_equal'
            ],
            'schedule_position_id' => [
                'operator' => 'multi_not_equal'
            ]
        ];
    }

    public static function getAddFormFields()
    {
        return [
            [
                'type' => 'input',
                'input_type' => 'text',
                'name' => 'name',
                'header' => 'Предмет',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'lesson_types',
                'name' => 'lesson_type',
                'header' => 'Вид',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'week_days',
                'name' => 'week_day',
                'header' => 'День недели',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'weekly_periods',
                'name' => 'weekly_period',
                'header' => 'Недельная периодичность',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'class_periods',
                'name' => 'class_period',
                'header' => 'Пара',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 5,
                    'explanation' => "Для выбора нескольких групп нажмите и удерживайте клавишу 'Ctrl'. Также и для отмены выбора."
                ],
                'plural_name' => 'groups',
                'name' => 'group',
                'header' => 'Группа',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'teachers',
                'name' => 'teacher',
                'header' => 'Преподаватель',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'lesson_rooms',
                'name' => 'lesson_room',
                'header' => 'Аудитория',
            ],
            [
                'type' => 'input',
                'input_type' => 'date',
                'name' => 'date',
                'header' => 'Дата',
            ],
        ];
    }

    public static function getReplacementFilterFormFields()
    {
        return [
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 3,
                    // 'explanation' => "Для выбора нескольких факультетов нажмите и удерживайте клавишу 'Ctrl'"
                ],
                'plural_name' => 'week_days',
                'name' => 'week_day',
                'header' => 'День недели',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 3,
                    // 'explanation' => "Для выбора нескольких факультетов нажмите и удерживайте клавишу 'Ctrl'"
                ],
                'plural_name' => 'weekly_periods',
                'name' => 'weekly_period',
                'header' => 'Недельная периодичность',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 3,
                    // 'explanation' => "Для выбора нескольких факультетов нажмите и удерживайте клавишу 'Ctrl'"
                ],
                'plural_name' => 'class_periods',
                'name' => 'class_period',
                'header' => 'Пара',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 3,
                    // 'explanation' => "Для выбора нескольких факультетов нажмите и удерживайте клавишу 'Ctrl'"
                ],
                'plural_name' => 'faculties',
                'name' => 'faculty',
                'header' => 'Факультет',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 3,
                    // 'explanation' => "Для выбора нескольких кафедр нажмите и удерживайте клавишу 'Ctrl'"
                ],
                'plural_name' => 'departments',
                'name' => 'department',
                'header' => 'Кафедра',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 3,
                    // 'explanation' => "Для выбора нескольких уровней нажмите и удерживайте клавишу 'Ctrl'"
                ],
                'plural_name' => 'professional_levels',
                'name' => 'professional_level',
                'header' => 'Профессиональный уровень',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 3,
                    // 'explanation' => "Для выбора нескольких должностей нажмите и удерживайте клавишу 'Ctrl'"
                ],
                'plural_name' => 'positions',
                'name' => 'position',
                'header' => 'Должность',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 3,
                    // 'explanation' => "Для выбора нескольких должностей нажмите и удерживайте клавишу 'Ctrl'"
                ],
                'plural_name' => 'lesson_rooms',
                'name' => 'lesson_room',
                'header' => 'Аудитория',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 3,
                    // 'explanation' => "Для выбора нескольких должностей нажмите и удерживайте клавишу 'Ctrl'"
                ],
                'plural_name' => 'schedule_positions',
                'name' => 'schedule_position',
                'header' => 'Позиция в расписании заменяющего преподавателя',
            ],
        ];
    }

    public static function getFilterFormFields()
    {
        return [
            [
                'type' => 'input',
                'input_type' => 'text',
                'name' => 'name',
                'header' => 'Предмет',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 2
                ],
                'plural_name' => 'lesson_types',
                'name' => 'lesson_type',
                'header' => 'Вид',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 2
                ],
                'plural_name' => 'week_days',
                'name' => 'week_day',
                'header' => 'День недели',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 2
                ],
                'plural_name' => 'weekly_periods',
                'name' => 'weekly_period',
                'header' => 'Недельная периодичность',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 2
                ],
                'plural_name' => 'class_periods',
                'name' => 'class_period',
                'header' => 'Пара',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 3
                ],
                'plural_name' => 'groups',
                'name' => 'group',
                'header' => 'Группа',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 3
                ],
                'plural_name' => 'teachers',
                'name' => 'teacher',
                'header' => 'Преподаватель',
            ],
            [
                'type' => 'objects-select',
                'multiple_options' => [
                    'is_multiple' => true,
                    'size' => 3
                ],
                'plural_name' => 'lesson_rooms',
                'name' => 'lesson_room',
                'header' => 'Аудитория',
            ],
            
        ];
    }

    public static function getProperties() {

        $groups = Group::orderBy('study_form_id')
                        ->orderBy('study_degree_id')
                        ->orderBy('faculty_id')
                        ->orderBy('course_id')
                        ->get();

        $teachers = Teacher::orderBy('last_name')->get();
        foreach ($teachers as &$teacher) {
            $teacher->name = $teacher->profession_level_name;
        }

        return [
            'lesson_types' => LessonType::select('id', 'name')->get(),
            'week_days' => WeekDay::select('id', 'name')->get(),
            'weekly_periods' => WeeklyPeriod::select('id', 'name')->get(),
            'class_periods' => ClassPeriod::select('id', 'name')->get(),
            'lesson_rooms' => LessonRoom::select('id', 'number AS name')->get(),
            'groups' => $groups,
            'teachers' => $teachers
        ];
    }

    public static function getReplacementProperties() {

        return [
            'lesson_types' => LessonType::select('id', 'name')->get(),
            'week_days' => WeekDay::select('id', 'name')->get(),
            'weekly_periods' => WeeklyPeriod::select('id', 'name')->get(),
            'class_periods' => ClassPeriod::get(),
            'faculties' => Faculty::select('id', 'name')->get(),
            'departments' => Department::select('id', 'name')->get(),
            'professional_levels' => ProfessionalLevel::select('id', 'name')->get(),
            'positions' => Position::select('id', 'name')->get(),
            'lesson_rooms' => LessonRoom::select('id', 'number AS name')->get(),
            'schedule_positions' => collect(config('enum.schedule_positions'))
        ];
    }
}
