# Database Seeders

This document explains how to use the database seeders to populate the exam system with sample data.

## Overview

The exam system includes a comprehensive seeder that creates a full set of sample data including:
- Schools, grades, and sections
- Teachers and students
- Subjects and enrollments
- Question banks, questions, and choices
- Monthly exams and exam questions
- Student attempts and answers
- Proctoring events

## Running the Seeders

To populate your database with sample data, run the following command:

```bash
php artisan db:seed
```

This will run the `DatabaseSeeder`, which in turn calls the `ExamSystemSeeder` to create all the sample data.

## Seeding Process

The seeder creates data in the following order:

1. **School** - Creates a sample school
2. **Grades** - Creates sample grades (9, 10, 11, 12)
3. **Sections** - Creates sections for each grade
4. **Subjects** - Creates sample subjects
5. **Teachers** - Creates sample teachers
6. **Students** - Creates 100 sample students
7. **Enrollments** - Enrolls students in grades and sections
8. **Question Banks** - Creates question banks for different subjects
9. **Questions** - Creates sample questions of all types
10. **Choices** - Creates choices for MCQ and TF questions
11. **Monthly Exams** - Creates monthly exams for each grade/section
12. **Exam Subjects** - Links subjects to exams
13. **Exam Questions** - Links questions to exams
14. **Student Attempts** - Creates sample exam attempts
15. **Attempt Answers** - Creates sample answers for attempts
16. **Proctoring Events** - Creates sample proctoring events

## Customizing the Seeders

You can customize the seeder by modifying the `ExamSystemSeeder.php` file:

- Change the number of students, teachers, questions, etc.
- Modify the sample data values
- Adjust the relationships between entities

## Factories

Each model has a corresponding factory that defines how to create sample instances:

- `SchoolFactory`
- `GradeFactory`
- `SectionFactory`
- `TeacherFactory`
- `StudentFactory`
- `SubjectFactory`
- `EnrollmentFactory`
- `MonthlyExamFactory`
- `QuestionBankFactory`
- `QuestionFactory`
- `ChoiceFactory`
- `ExamQuestionFactory`
- `StudentAttemptFactory`
- `AttemptAnswerFactory`
- `ProctoringEventFactory`

## Resetting the Database

To reset the database and re-seed with fresh data:

```bash
php artisan migrate:fresh --seed
```

This will:
1. Drop all tables
2. Run all migrations
3. Seed the database with sample data

## Using in Development

The seeders are particularly useful for:
- Testing the application with realistic data
- Demonstrating features to stakeholders
- Development and debugging
- Creating consistent test environments

## Data Generated

The seeder creates:
- 1 School
- 3 Grades
- 6 Sections (2 per grade)
- 5 Subjects
- 10 Teachers
- 100 Students
- 5 Question Banks
- 50 Questions (10 per bank)
- 200+ Choices (for MCQ/TF questions)
- 18 Monthly Exams (2 per grade/section combination)
- 100+ Student Attempts
- 1000+ Attempt Answers
- 200+ Proctoring Events

This provides a comprehensive dataset for testing all aspects of the exam system.
