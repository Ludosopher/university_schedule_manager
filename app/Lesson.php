<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Lesson extends Model
{
    use Sortable;
    public $sortable = ['name', 'lesson_type_id', 'week_day_id','weekly_period_id', 'class_period_id', 'group_id', 'teacher_id'];

       
    public function class_period()
    {
        return $this->belongsTo(ClassPeriod::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
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

    public static function rules($request)
    {
        return [
            'name' => 'required|string',
            'lesson_type_id' => 'required|integer|exists:App\LessonType,id',
            'week_day_id' => 'required|integer|exists:App\WeekDay,id',
            'weekly_period_id' => 'required|integer|exists:App\WeeklyPeriod,id',
            'class_period_id' => 'required|integer|exists:App\ClassPeriod,id',
            'group_id' => 'required|integer|exists:App\Group,id',
            'teacher_id' => 'required|integer|exists:App\Teacher,id',
        ];
    }

    public static function filterRules()
    {
        return [
            'name' => 'nullable|string',
            'lesson_type_id' => 'nullable|integer|exists:App\LessonType,id',
            'week_day_id' => 'nullable|integer|exists:App\WeekDay,id',
            'weekly_period_id' => 'nullable|integer|exists:App\WeeklyPeriod,id',
            'class_period_id' => 'nullable|integer|exists:App\ClassPeriod,id',
            'group_id' => 'nullable|integer|exists:App\Group,id',
            'teacher_id' => 'nullable|integer|exists:App\Teacher,id',
        ];
    }

    public static function filterReplacementRules()
    {
        return [
            'week_day_id' => 'nullable|integer|exists:App\WeekDay,id',
            'weekly_period_id' => 'nullable|integer|exists:App\WeeklyPeriod,id',
            'class_period_id' => 'nullable|integer|exists:App\ClassPeriod,id',
            'faculty_id' => 'nullable|integer|exists:App\Faculty,id',
            'department_id' => 'nullable|integer|exists:App\Department,id',
            'professional_level_id' => 'nullable|integer|exists:App\ProfessionalLevel,id',
            'position_id' => 'nullable|integer|exists:App\Position,id',
            'schedule_position' => 'nullable|integer',

            'replace_rules.*.week_day_id' => 'nullable|integer|exists:App\WeekDay,id',
            'replace_rules.*.weekly_period_id' => 'nullable|integer|exists:App\WeeklyPeriod,id',
            'replace_rules.*.class_period_id' => 'nullable|integer|exists:App\ClassPeriod,id',
            'replace_rules.*.group_id' => 'nullable|integer|exists:App\Group,id',
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
                'method' => 'where',
                'operator' => '='
            ],
            'week_day_id' => [
                'method' => 'where',
                'operator' => '='
            ],
            'weekly_period_id' => [
                'method' => 'where',
                'operator' => '='
            ],
            'class_period_id' => [
                'method' => 'where',
                'operator' => '='
            ],
            'group_id' => [
                'method' => 'where',
                'operator' => '='
            ],
            'teacher_id' => [
                'method' => 'where',
                'operator' => '='
            ]
        ];
    }

    public static function filterReplacementConditions()
    {
        return [
            'week_day_id' => [
                'operator' => 'not_equal'
            ],
            'weekly_period_id' => [
                'operator' => 'not_equal'
            ],
            'class_period_id' => [
                'operator' => 'not_equal'
            ],
            'faculty_id' => [
                'operator' => 'not_equal'
            ],
            'department_id' => [
                'operator' => 'not_equal'
            ],
            'professional_level_id' => [
                'operator' => 'not_equal'
            ],
            'position_id' => [
                'operator' => 'not_equal'
            ],
            'schedule_position_id' => [
                'operator' => 'not_equal'
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
        ];
    }

    public static function getReplacementFilterFormFields()
    {
        return [
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
                'plural_name' => 'faculties',
                'name' => 'faculty',
                'header' => 'Факультет',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'departments',
                'name' => 'department',
                'header' => 'Кафедра',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'professional_levels',
                'name' => 'professional_level',
                'header' => 'Профессиональный уровень',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'positions',
                'name' => 'position',
                'header' => 'Должность',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'schedule_positions',
                'name' => 'schedule_position',
                'header' => 'Позиция в расписании',
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
                'plural_name' => 'groups',
                'name' => 'group',
                'header' => 'Группа',
            ],
            [
                'type' => 'objects-select',
                'plural_name' => 'teachers',
                'name' => 'teacher',
                'header' => 'Преподаватель',
            ]
        ];
    }

    public static function getProperties() {
        
        $teachers = Teacher::get();
        foreach ($teachers as &$teacher) {
            $teacher->name = $teacher->profession_level_name;
        }
        
        return [
            'lesson_types' => LessonType::select('id', 'name')->get(),
            'week_days' => WeekDay::select('id', 'name')->get(),
            'weekly_periods' => WeeklyPeriod::select('id', 'name')->get(),
            'class_periods' => ClassPeriod::select('id', 'name')->get(),
            'groups' => Group::select('id', 'name')->get(),
            'teachers' => $teachers,
        ];
    }

    public static function getReplacementProperties() {
        
        return [
            'lesson_types' => LessonType::select('id', 'name')->get(),
            'week_days' => WeekDay::select('id', 'name')->get(),
            'weekly_periods' => WeeklyPeriod::select('id', 'name')->get(),
            'class_periods' => ClassPeriod::select('id', 'name')->get(),
            'faculties' => Faculty::select('id', 'name')->get(),
            'departments' => Department::select('id', 'name')->get(),
            'professional_levels' => ProfessionalLevel::select('id', 'name')->get(),
            'positions' => Position::select('id', 'name')->get(),
            'schedule_positions' => collect(config('enum.schedule_positions'))
        ];
    }
}
