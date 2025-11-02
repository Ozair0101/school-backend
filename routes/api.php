<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
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

// Authentication routes (public)
Route::post('/login', [AuthController::class, 'login']);

// Protected authentication routes
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Protected API routes - require authentication
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
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
    Route::post('monthly-exams/{monthlyExam}/start', [MonthlyExamController::class, 'start']);
    Route::get('monthly-exams/{monthlyExam}/questions', [MonthlyExamController::class, 'questions']);
    Route::post('monthly-exams/{monthlyExam}/presign', [MonthlyExamController::class, 'presign']);

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
    Route::post('exam-questions/batch', [ExamQuestionController::class, 'batch']);

    // Student Attempts
    Route::apiResource('student-attempts', StudentAttemptController::class);
    Route::post('student-attempts/{studentAttempt}/answer', [StudentAttemptController::class, 'saveAnswer']);
    Route::post('student-attempts/{studentAttempt}/submit', [StudentAttemptController::class, 'submit']);
    Route::get('student-attempts/{studentAttempt}/status', [StudentAttemptController::class, 'status']);

    // Attempt Answers
    Route::apiResource('attempt-answers', AttemptAnswerController::class);

    // Proctoring Events
    Route::apiResource('proctoring-events', ProctoringEventController::class);
    Route::post('proctoring-events/batch', [ProctoringEventController::class, 'batch']);

    // Exam Aggregates
    Route::apiResource('exam-aggregates', ExamAggregateController::class);
});
