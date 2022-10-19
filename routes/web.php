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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', 'HomeController@index')->name('home');

Route::middleware(['auth'])->group(function () {
    
    Route::match(['get', 'post'],'/teacher/get-all', 'TeacherController@getTeachers')->name('teachers');
    Route::get('/teacher/add-form', 'TeacherController@addTeacherForm')->name('teacher-form');
    Route::post('/teacher/add', 'TeacherController@addOrUpdateTeacher')->name('teacher-add');
    Route::get('/teacher/update', 'TeacherController@addTeacherForm')->name('teacher-update');
    Route::get('/teacher/delete', 'TeacherController@deleteTeacher')->name('teacher-delete');
    Route::get('/teacher/schedule', 'TeacherController@getTeacherSchedule')->name('teacher-schedule');
    Route::get('/teacher/replacement', 'TeacherController@getTeachersForReplacement')->name('teacher-replacement'); 
    
    Route::match(['get', 'post'],'/group/get-all', 'GroupController@getGroups')->name('groups');
    Route::get('/group/add-form', 'GroupController@addGroupForm')->name('group-form');
    Route::post('/group/add', 'GroupController@addOrUpdateGroup')->name('group-add');
    Route::get('/group/update', 'GroupController@addGroupForm')->name('group-update');
    Route::get('/group/delete', 'GroupController@deleteGroup')->name('group-delete');
    Route::get('/group/schedule', 'GroupController@getGroupSchedule')->name('group-schedule');

    Route::match(['get', 'post'],'/lesson/get-all', 'LessonController@getLessons')->name('lessons');
    Route::get('/lesson/add-form', 'LessonController@addLessonForm')->name('lesson-form');
    Route::post('/lesson/add', 'LessonController@addOrUpdateLesson')->name('lesson-add');
    Route::get('/lesson/update', 'LessonController@addLessonForm')->name('lesson-update');
    Route::get('/lesson/delete', 'LessonController@deleteLesson')->name('lesson-delete');
    
    
});



Auth::routes();


