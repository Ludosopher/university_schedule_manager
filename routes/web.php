<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index')->name('home')->middleware('setlocate');
Route::get('/about', 'HomeController@about')->name('about')->middleware('setlocate');
Route::post('/user/set-locate', 'UserController@setLocate')->name('user-set-locate');

Route::middleware(['auth', 'setlocate'])->group(function () {

    Route::match(['get', 'post'],'/user/get-all', 'UserController@getUsers')->name('users')->middleware('admin');
    Route::get('/user/add-form', 'UserController@addUserForm')->name('user-form')->middleware('admin');
    Route::post('/user/admin-update', 'UserController@adminUpdateUser')->name('user-admin-update')->middleware('admin');
    Route::post('/user/self-update', 'UserController@selfUpdateUser')->name('user-self-update')->middleware('user');
    Route::get('/user/delete', 'UserController@deleteUser')->name('user-delete')->middleware('admin');
    Route::get('/user/account-main', 'UserController@getAccountMain')->name('user-account-main');
    
    Route::match(['get', 'post'], '/teacher/get-all', 'TeacherController@getTeachers')->name('teachers');
    Route::get('/teacher/add-form', 'TeacherController@addTeacherForm')->name('teacher-add-form')->middleware('moderator');
    Route::post('/teacher/add-update', 'TeacherController@addOrUpdateTeacher')->name('teacher-add-update')->middleware('moderator');
    Route::get('/teacher/update-form', 'TeacherController@addTeacherForm')->name('teacher-update-form')->middleware('moderator');
    Route::get('/teacher/delete', 'TeacherController@deleteTeacher')->name('teacher-delete')->middleware('moderator');
    Route::match(['get', 'post'], '/teacher/schedule', 'TeacherController@getTeacherSchedule')->name('teacher-schedule');
    Route::match(['get', 'post'], '/teacher/month-schedule', 'TeacherController@getMonthTeacherSchedule')->name('teacher-month-schedule');
    Route::post('/teacher/schedule/export-to-doc', 'TeacherController@exportScheduleToDoc')->name('teacher-schedule-doc-export');
    Route::post('/teacher/month-schedule/export-to-doc', 'TeacherController@exportMonthScheduleToDoc')->name('teacher-month-schedule-doc-export');
    Route::match(['get', 'post'], '/teacher/reschedule', 'TeacherController@getTeacherReschedule')->name('teacher-reschedule');
    Route::post('/teacher/reschedule/export-to-doc', 'TeacherController@exportRescheduleToDoc')->name('teacher-reschedule-doc-export');
    
    Route::match(['get', 'post'],'/group/get-all', 'GroupController@getGroups')->name('groups');
    Route::get('/group/add-form', 'GroupController@addGroupForm')->name('group-add-form')->middleware('moderator');
    Route::post('/group/add-update', 'GroupController@addOrUpdateGroup')->name('group-add-update')->middleware('moderator');
    Route::get('/group/update-form', 'GroupController@addGroupForm')->name('group-update-form')->middleware('moderator');
    Route::get('/group/delete', 'GroupController@deleteGroup')->name('group-delete')->middleware('moderator');
    Route::match(['get', 'post'], '/group/schedule', 'GroupController@getGroupSchedule')->name('group-schedule');
    Route::match(['get', 'post'], '/group/month-schedule', 'GroupController@getMonthGroupSchedule')->name('group-month-schedule');
    Route::post('/group/schedule/export-to-doc', 'GroupController@exportScheduleToDoc')->name('group-schedule-doc-export');
    Route::post('/group/month-schedule/export-to-doc', 'GroupController@exportMonthScheduleToDoc')->name('group-month-schedule-doc-export');
    Route::match(['get', 'post'],  '/group/reschedule', 'GroupController@getGroupReschedule')->name('group-reschedule');
    Route::post('/group/reschedule/export-to-doc', 'GroupController@exportRescheduleToDoc')->name('group-reschedule-doc-export');

    Route::match(['get', 'post'],'/lesson/get-all', 'LessonController@getLessons')->name('lessons');
    Route::get('/lesson/add-form', 'LessonController@addLessonForm')->name('lesson-add-form')->middleware('moderator');
    Route::post('/lesson/add', 'LessonController@addOrUpdateLesson')->name('lesson-add-update')->middleware('moderator');
    Route::get('/lesson/update-form', 'LessonController@addLessonForm')->name('lesson-update-form')->middleware('moderator');
    Route::get('/lesson/delete', 'LessonController@deleteLesson')->name('lesson-delete')->middleware('moderator');
    Route::match(['get', 'post'], '/lesson/replacement', 'LessonController@getReplacementVariants')->name('lesson-replacement');
    Route::post('/lesson/replacement/export-to-doc', 'LessonController@exportReplacementToDoc')->name('lesson-replacement-doc-export');
    Route::post('/lesson/replacement-schedule/export-to-doc', 'LessonController@exportReplacementScheduleToDoc')->name('lesson-replacement-schedule-doc-export');
    Route::match(['get', 'post'], '/lesson/rescheduling', 'LessonController@getReschedulingVariants')->name('lesson-rescheduling');

    Route::match(['get', 'post'], '/replacement-request/get-all', 'ReplacementRequestController@getReplacementRequests')->name('replacement_requests');
    Route::match(['get', 'post'], '/replacement-request/get-my', 'ReplacementRequestController@getMyReplacementRequests')->name('my_replacement_requests');
    Route::post('/replacement-request/add', 'ReplacementRequestController@addReplacementRequest')->name('replacement-request-add')->middleware('replacer');
    Route::get('/replacement-request/update', 'ReplacementRequestController@updateReplacementRequest')->name('replacement-request-update')->middleware('replacer');
    Route::get('/replacement-request/delete', 'ReplacementRequestController@deleteReplacementRequest')->name('replacement-request-delete')->middleware('replacer');
    Route::post('/replacement-request/send', 'ReplacementRequestController@sendReplacementRequest')->name('replacement-request-send');
    Route::post('/replacement-request/chat', 'ReplacementRequestController@openReplacementRequestChat')->name('replacement-request-chat');

    Route::get('/settings/get', 'SettingController@getSettings')->name('settings-get')->middleware('admin');
    Route::post('/settings/update', 'SettingController@updateSettings')->name('settings-update')->middleware('admin');
});



Auth::routes();
