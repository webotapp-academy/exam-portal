<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Exam;
use App\Models\Option;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@examportal.test'],
            [
                'name' => 'Portal Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $examiner = User::firstOrCreate(
            ['email' => 'examiner@examportal.test'],
            [
                'name' => 'Jane Examiner',
                'password' => Hash::make('password'),
                'role' => 'examiner',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'student@examportal.test'],
            [
                'name' => 'Alex Student',
                'password' => Hash::make('password'),
                'role' => 'student',
                'is_active' => true,
            ]
        );

        $catLaravel = Category::firstOrCreate(
            ['slug' => 'laravel-php'],
            ['name' => 'Laravel & PHP', 'description' => 'Modern PHP 8+ and Laravel framework concepts.']
        );

        $catMysql = Category::firstOrCreate(
            ['slug' => 'database-mysql'],
            ['name' => 'Database & MySQL', 'description' => 'SQL queries, indexing, transactions, and normalization.']
        );

        $catSecurity = Category::firstOrCreate(
            ['slug' => 'web-security'],
            ['name' => 'Web Security & HTTP', 'description' => 'OWASP, CSRF, XSS, authentication, and HTTP fundamentals.']
        );

        // Exam 1: Instant Answer Practice Exam
        $examInstant = Exam::firstOrCreate(
            ['slug' => 'laravel-php-instant-practice'],
            [
                'category_id' => $catLaravel->id,
                'created_by' => $admin->id,
                'title' => 'Laravel & PHP Mastery (Instant Answer)',
                'description' => 'Practice core Laravel & PHP concepts with immediate feedback and detailed explanations after every question.',
                'instructions' => "1. Select an option and click 'Check Answer' to see the instant result and explanation.\n2. You can mark questions for review using the palette.\n3. Negative marking is disabled for this practice exam.",
                'duration_minutes' => 20,
                'pass_percentage' => 50.00,
                'mode' => 'instant_answer',
                'lock_after_instant_reveal' => true,
                'allow_negative_marking' => false,
                'shuffle_questions' => true,
                'shuffle_options' => true,
                'max_attempts' => null,
                'show_leaderboard' => true,
                'is_published' => true,
            ]
        );

        $laravelQuestions = [
            [
                'category_id' => $catLaravel->id,
                'question_text' => 'Which Artisan command creates a new Eloquent model along with a migration, factory, and controller?',
                'type' => 'single_choice',
                'difficulty' => 'easy',
                'marks' => 1.00,
                'negative_marks' => 0.00,
                'explanation' => 'Passing -mfc (or --all / -a) to `php artisan make:model` generates the migration, factory, and controller together.',
                'options' => [
                    ['php artisan make:model Post -mfc', true],
                    ['php artisan make:model Post --mvc', false],
                    ['php artisan generate:model Post --full', false],
                    ['php artisan model:create Post -all', false],
                ],
            ],
            [
                'category_id' => $catLaravel->id,
                'question_text' => 'Which of the following are valid Eloquent relationship types in Laravel? (Select all that apply)',
                'type' => 'multiple_choice',
                'difficulty' => 'medium',
                'marks' => 2.00,
                'negative_marks' => 0.00,
                'explanation' => 'Laravel Eloquent provides `hasMany`, `belongsToMany`, `morphTo`, `hasOne`, `belongsTo`, etc. `joinsWith` is not an Eloquent relationship method.',
                'options' => [
                    ['hasMany', true],
                    ['belongsToMany', true],
                    ['joinsWith', false],
                    ['morphTo', true],
                ],
            ],
            [
                'category_id' => $catLaravel->id,
                'question_text' => 'True or False: Laravel’s `@csrf` Blade directive protects against Cross-Site Request Forgery attacks on POST/PUT/DELETE forms.',
                'type' => 'true_false',
                'difficulty' => 'easy',
                'marks' => 1.00,
                'negative_marks' => 0.00,
                'explanation' => 'The `@csrf` directive generates a hidden `_token` input field validated by `VerifyCsrfToken` / `ValidateCsrfToken` middleware.',
                'options' => [
                    ['True', true],
                    ['False', false],
                ],
            ],
            [
                'category_id' => $catLaravel->id,
                'question_text' => 'Which method prevents the N+1 query problem when loading related models in Eloquent?',
                'type' => 'single_choice',
                'difficulty' => 'medium',
                'marks' => 1.00,
                'negative_marks' => 0.00,
                'explanation' => 'Eager loading via `Model::with(...)` fetches related records in a minimal number of queries instead of one query per parent model.',
                'options' => [
                    ['Model::with("relation")->get()', true],
                    ['Model::lazy("relation")->get()', false],
                    ['Model::cache("relation")->get()', false],
                    ['Model::preload("relation")->get()', false],
                ],
            ],
            [
                'category_id' => $catLaravel->id,
                'question_text' => 'In PHP 8.2+, what does `readonly class` guarantee?',
                'type' => 'single_choice',
                'difficulty' => 'hard',
                'marks' => 2.00,
                'negative_marks' => 0.00,
                'explanation' => 'Declaring a class `readonly` makes all declared properties implicitly `readonly` and prevents dynamic properties.',
                'options' => [
                    ['All properties are implicitly readonly and cannot be modified after initialization', true],
                    ['Methods inside the class cannot return values', false],
                    ['The class cannot be instantiated with `new`', false],
                    ['Properties can only be read by child classes', false],
                ],
            ],
        ];

        if ($examInstant->questions()->count() === 0) {
            foreach ($laravelQuestions as $idx => $qData) {
                $question = Question::create([
                    'category_id' => $qData['category_id'],
                    'created_by' => $admin->id,
                    'question_text' => $qData['question_text'],
                    'type' => $qData['type'],
                    'difficulty' => $qData['difficulty'],
                    'marks' => $qData['marks'],
                    'negative_marks' => $qData['negative_marks'],
                    'explanation' => $qData['explanation'],
                    'is_active' => true,
                ]);
                foreach ($qData['options'] as $oIdx => [$text, $isCorrect]) {
                    Option::create([
                        'question_id' => $question->id,
                        'option_text' => $text,
                        'is_correct' => $isCorrect,
                        'sort_order' => $oIdx + 1,
                    ]);
                }
                $examInstant->questions()->attach($question->id, ['sort_order' => $idx + 1]);
            }
            $examInstant->recalculateTotalMarks();
        }

        // Exam 2: Standard Timed Assessment
        $examStandard = Exam::firstOrCreate(
            ['slug' => 'mysql-security-timed-assessment'],
            [
                'category_id' => $catMysql->id,
                'created_by' => $examiner->id,
                'title' => 'MySQL & Web Security (Timed Assessment)',
                'description' => 'Standard timed exam with negative marking enabled. Answers and explanations are revealed after final submission.',
                'instructions' => "1. You have 15 minutes to complete this assessment.\n2. Incorrect answers incur negative marking as indicated per question.\n3. Switching browser tabs is monitored.",
                'duration_minutes' => 15,
                'pass_percentage' => 60.00,
                'mode' => 'standard',
                'lock_after_instant_reveal' => false,
                'allow_negative_marking' => true,
                'shuffle_questions' => true,
                'shuffle_options' => true,
                'max_attempts' => 3,
                'show_leaderboard' => true,
                'is_published' => true,
            ]
        );

        $mysqlQuestions = [
            [
                'category_id' => $catMysql->id,
                'question_text' => 'Which MySQL storage engine supports ACID transactions and foreign key constraints?',
                'type' => 'single_choice',
                'difficulty' => 'easy',
                'marks' => 2.00,
                'negative_marks' => 0.50,
                'explanation' => 'InnoDB is MySQL/MariaDB’s default transactional storage engine with row-level locking and foreign key support.',
                'options' => [
                    ['InnoDB', true],
                    ['MyISAM', false],
                    ['MEMORY', false],
                    ['CSV', false],
                ],
            ],
            [
                'category_id' => $catMysql->id,
                'question_text' => 'What is the primary purpose of database indexing?',
                'type' => 'single_choice',
                'difficulty' => 'medium',
                'marks' => 2.00,
                'negative_marks' => 0.50,
                'explanation' => 'Indexes speed up data retrieval (SELECT, WHERE, JOIN, ORDER BY) at the cost of slightly slower writes and extra disk space.',
                'options' => [
                    ['Speed up read/lookup queries on indexed columns', true],
                    ['Encrypt column values automatically', false],
                    ['Eliminate the need for primary keys', false],
                    ['Compress table backups', false],
                ],
            ],
            [
                'category_id' => $catSecurity->id,
                'question_text' => 'Which HTTP status code indicates that the client is unauthenticated and must log in?',
                'type' => 'single_choice',
                'difficulty' => 'easy',
                'marks' => 2.00,
                'negative_marks' => 0.50,
                'explanation' => '401 Unauthorized indicates missing or invalid authentication credentials; 403 Forbidden indicates authenticated but insufficient permissions.',
                'options' => [
                    ['401 Unauthorized', true],
                    ['403 Forbidden', false],
                    ['404 Not Found', false],
                    ['422 Unprocessable Entity', false],
                ],
            ],
            [
                'category_id' => $catSecurity->id,
                'question_text' => 'True or False: Parameterized queries (prepared statements) prevent SQL Injection attacks.',
                'type' => 'true_false',
                'difficulty' => 'easy',
                'marks' => 2.00,
                'negative_marks' => 0.50,
                'explanation' => 'Prepared statements separate SQL code from user-supplied data, preventing attackers from altering query structure.',
                'options' => [
                    ['True', true],
                    ['False', false],
                ],
            ],
        ];

        if ($examStandard->questions()->count() === 0) {
            foreach ($mysqlQuestions as $idx => $qData) {
                $question = Question::create([
                    'category_id' => $qData['category_id'],
                    'created_by' => $examiner->id,
                    'question_text' => $qData['question_text'],
                    'type' => $qData['type'],
                    'difficulty' => $qData['difficulty'],
                    'marks' => $qData['marks'],
                    'negative_marks' => $qData['negative_marks'],
                    'explanation' => $qData['explanation'],
                    'is_active' => true,
                ]);
                foreach ($qData['options'] as $oIdx => [$text, $isCorrect]) {
                    Option::create([
                        'question_id' => $question->id,
                        'option_text' => $text,
                        'is_correct' => $isCorrect,
                        'sort_order' => $oIdx + 1,
                    ]);
                }
                $examStandard->questions()->attach($question->id, ['sort_order' => $idx + 1]);
            }
            $examStandard->recalculateTotalMarks();
        }

        $this->call(SubjectExamsSeeder::class);
    }
}
