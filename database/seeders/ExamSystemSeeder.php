<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Import all the models we need
use App\Models\School;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Subject;
use App\Models\MonthlyExam;
use App\Models\ExamSubject;
use App\Models\QuestionBank;
use App\Models\Question;
use App\Models\Choice;
use App\Models\ExamQuestion;
use App\Models\StudentAttempt;
use App\Models\AttemptAnswer;
use App\Models\ProctoringEvent;

class ExamSystemSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a school
        $school = School::factory()->create([
            'name' => 'Greenwood High School',
            'address' => '123 Education Street, Learning City',
            'phone' => '+1-555-0123',
            'email' => 'info@greenwoodhigh.edu',
        ]);

        // Create grades
        $grades = Grade::factory()->count(3)->create([
            'school_id' => $school->id,
        ]);

        // Create sections for each grade
        $sections = collect();
        foreach ($grades as $grade) {
            $sectionA = Section::factory()->create([
                'school_id' => $school->id,
                'grade_id' => $grade->id,
                'name' => 'Section A',
            ]);
            $sections->push($sectionA);

            $sectionB = Section::factory()->create([
                'school_id' => $school->id,
                'grade_id' => $grade->id,
                'name' => 'Section B',
            ]);
            $sections->push($sectionB);
        }

        // Create subjects
        $subjects = Subject::factory()->count(5)->create([
            'school_id' => $school->id,
        ]);

        // Create teachers
        $teachers = Teacher::factory()->count(10)->create([
            'school_id' => $school->id,
        ]);

        // Create students
        $students = Student::factory()->count(100)->create([
            'school_id' => $school->id,
        ]);

        // Convert sections to array for easier access
        $sectionsArray = [];
        foreach ($sections as $section) {
            $sectionsArray[$section->grade_id][] = $section;
        }

        // Create enrollments (students to grades/sections)
        foreach ($students as $student) {
            // Randomly assign to a grade and section
            $grade = $grades->random();
            $gradeSections = $sectionsArray[$grade->id] ?? [];
            if (!empty($gradeSections)) {
                $section = $gradeSections[array_rand($gradeSections)];

                Enrollment::factory()->create([
                    'student_id' => $student->id,
                    'grade_id' => $grade->id,
                    'section_id' => $section->id,
                ]);
            }
        }

        // Create question banks
        $questionBanks = QuestionBank::factory()->count(5)->create([
            'school_id' => $school->id,
        ]);

        // Create questions for each bank
        $questions = [];
        foreach ($questionBanks as $bank) {
            // Create 10 questions per bank
            for ($i = 1; $i <= 10; $i++) {
                $question = Question::factory()->create([
                    'bank_id' => $bank->id,
                    'author_id' => $teachers->random()->id,
                    'type' => fake()->randomElement(['mcq', 'tf', 'numeric', 'short', 'essay', 'file']),
                    'prompt' => "Sample question {$i} for {$bank->name}",
                ]);

                $questions[] = $question;

                // For MCQ and TF questions, create choices
                if (in_array($question->type, ['mcq', 'tf'])) {
                    // Create 4 choices for MCQ
                    if ($question->type === 'mcq') {
                        $correctChoice = rand(1, 4);
                        for ($j = 1; $j <= 4; $j++) {
                            Choice::factory()->create([
                                'question_id' => $question->id,
                                'choice_text' => "Choice option {$j}",
                                'is_correct' => ($j === $correctChoice),
                                'position' => $j,
                            ]);
                        }
                    }
                    // Create 2 choices for TF (True/False)
                    else {
                        Choice::factory()->create([
                            'question_id' => $question->id,
                            'choice_text' => 'True',
                            'is_correct' => true,
                            'position' => 1,
                        ]);

                        Choice::factory()->create([
                            'question_id' => $question->id,
                            'choice_text' => 'False',
                            'is_correct' => false,
                            'position' => 2,
                        ]);
                    }
                }
            }
        }

        // Create monthly exams
        $exams = [];
        foreach ($grades as $grade) {
            foreach ($sections as $section) {
                if ($section->grade_id === $grade->id) {
                    $exam = MonthlyExam::factory()->create([
                        'school_id' => $school->id,
                        'grade_id' => $grade->id,
                        'section_id' => $section->id,
                        'month' => rand(1, 12),
                        'year' => 2025,
                        'exam_date' => now()->addDays(rand(1, 30)),
                        'description' => "Monthly exam for Grade {$grade->name}, Section {$section->name}",
                        'online_enabled' => true,
                        'duration_minutes' => rand(30, 120),
                    ]);

                    $exams[] = $exam;

                    // Link subjects to this exam
                    foreach ($subjects as $subject) {
                        ExamSubject::factory()->create([
                            'monthly_exam_id' => $exam->id,
                            'subject_id' => $subject->id,
                        ]);
                    }

                    // Link questions to this exam
                    $questionCount = min(20, count($questions));
                    $questionKeys = array_keys($questions);
                    shuffle($questionKeys);
                    $selectedQuestionKeys = array_slice($questionKeys, 0, $questionCount);

                    foreach ($selectedQuestionKeys as $index => $questionKey) {
                        $question = $questions[$questionKey];
                        ExamQuestion::factory()->create([
                            'monthly_exam_id' => $exam->id,
                            'question_id' => $question->id,
                            'marks' => rand(1, 5),
                            'sequence' => $index + 1,
                        ]);
                    }
                }
            }
        }

        // Create some student attempts
        foreach ($exams as $exam) {
            // Get students in this grade/section
            $enrollments = Enrollment::where('grade_id', $exam->grade_id)
                ->where('section_id', $exam->section_id)
                ->get();

            // Create attempts for some students
            $attemptStudents = $enrollments->random(min(10, $enrollments->count()));
            foreach ($attemptStudents as $enrollment) {
                $attempt = StudentAttempt::factory()->create([
                    'monthly_exam_id' => $exam->id,
                    'student_id' => $enrollment->student_id,
                    'status' => fake()->randomElement(['in_progress', 'submitted', 'graded']),
                    'attempt_token' => Str::random(32),
                ]);

                // Create some answers for this attempt
                $examQuestions = ExamQuestion::where('monthly_exam_id', $exam->id)->get();
                foreach ($examQuestions as $eq) {
                    $question = Question::find($eq->question_id);
                    if ($question) {
                        $answerData = [
                            'attempt_id' => $attempt->id,
                            'question_id' => $question->id,
                        ];

                        // Create different types of answers based on question type
                        switch ($question->type) {
                            case 'mcq':
                                $correctChoice = Choice::where('question_id', $question->id)
                                    ->where('is_correct', true)
                                    ->first();
                                if ($correctChoice) {
                                    $answerData['choice_id'] = $correctChoice->id;
                                }
                                break;
                            case 'tf':
                                $answerData['answer_text'] = fake()->randomElement(['true', 'false']);
                                break;
                            case 'numeric':
                                $answerData['answer_text'] = (string) rand(1, 100);
                                break;
                            case 'short':
                                $answerData['answer_text'] = 'Sample short answer';
                                break;
                            case 'essay':
                                $answerData['answer_text'] = 'This is a sample essay answer that demonstrates the student\'s understanding of the topic. It includes several sentences to show depth of knowledge.';
                                break;
                            case 'file':
                                $answerData['uploaded_file'] = 'uploads/sample-file-' . Str::random(10) . '.pdf';
                                break;
                        }

                        AttemptAnswer::factory()->create($answerData);
                    }
                }

                // Create some proctoring events for this attempt
                for ($i = 1; $i <= rand(1, 5); $i++) {
                    ProctoringEvent::factory()->create([
                        'attempt_id' => $attempt->id,
                        'event_type' => fake()->randomElement(['tab_hidden', 'tab_visible', 'snapshot_captured']),
                        'event_time' => now()->subMinutes(rand(1, 120)),
                    ]);
                }
            }
        }
    }
}
