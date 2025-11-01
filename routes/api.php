<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    SchoolController,
    GradeController,
    SectionController,
    TeacherController,
    StudentController,
    EnrollmentController,
    SubjectController,
    TeacherSubjectController,
    MonthlyExamController,
    ExamSubjectController,
    QuestionBankController,
    QuestionController,
    ChoiceController,
    ExamQuestionController,
    StudentAttemptController,
    AttemptAnswerController,
    ProctoringEventController,
    ExamAggregateController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Schools
Route::apiResource('schools', SchoolController::class);

// Grades
Route::apiResource('grades', GradeController::class);

// Sections
Route::apiResource('sections', SectionController::class);

// Teachers
Route::apiResource('teachers', TeacherController::class);

// Students
Route::apiResource('students', StudentController::class);

// Enrollments
Route::apiResource('enrollments', EnrollmentController::class);

// Subjects
Route::apiResource('subjects', SubjectController::class);

// Teacher Subjects
Route::apiResource('teacher-subjects', TeacherSubjectController::class);

// Monthly Exams
Route::apiResource('monthly-exams', MonthlyExamController::class);

// Exam Subjects
Route::apiResource('exam-subjects', ExamSubjectController::class);

// Question Banks
Route::apiResource('question-banks', QuestionBankController::class);

// Questions
Route::apiResource('questions', QuestionController::class);

// Choices
Route::apiResource('choices', ChoiceController::class);

// Exam Questions
Route::apiResource('exam-questions', ExamQuestionController::class);

// Student Attempts
Route::apiResource('student-attempts', StudentAttemptController::class);

// Attempt Answers
Route::apiResource('attempt-answers', AttemptAnswerController::class);

// Proctoring Events
Route::apiResource('proctoring-events', ProctoringEventController::class);

// Exam Aggregates
Route::apiResource('exam-aggregates', ExamAggregateController::class);
