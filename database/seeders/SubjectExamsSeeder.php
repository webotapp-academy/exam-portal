<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Exam;
use App\Models\Option;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubjectExamsSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::where('role', 'admin')->first() ?? User::first();
        if (! $creator) {
            return;
        }

        $subjects = [
            [
                'category' => ['name' => 'General Knowledge', 'slug' => 'general-knowledge', 'description' => 'World geography, science, current affairs, and trivia.'],
                'exam' => [
                    'title' => 'General Knowledge & World Affairs (Instant Answer)',
                    'slug' => 'gk-world-affairs-instant',
                    'description' => 'Test your awareness of world geography, science milestones, and international organizations with instant explanations.',
                    'instructions' => "1. Select your answer and click 'Check Answer' for instant feedback.\n2. No negative marking applies.",
                    'duration_minutes' => 15,
                    'pass_percentage' => 50,
                    'mode' => 'instant_answer',
                    'allow_negative_marking' => false,
                ],
                'questions' => [
                    [
                        'text' => 'Which planet in our solar system has the most confirmed moons?',
                        'difficulty' => 'medium',
                        'marks' => 1,
                        'explanation' => 'Saturn leads the solar system with over 140 confirmed moons, surpassing Jupiter.',
                        'options' => [['Saturn', true], ['Jupiter', false], ['Uranus', false], ['Neptune', false]],
                    ],
                    [
                        'text' => 'Where is the headquarters of the United Nations (UN) located?',
                        'difficulty' => 'easy',
                        'marks' => 1,
                        'explanation' => 'The UN Headquarters is located in New York City, United States.',
                        'options' => [['New York City', true], ['Geneva', false], ['Vienna', false], ['Paris', false]],
                    ],
                    [
                        'text' => 'Which gas makes up approximately 78% of Earth’s atmosphere?',
                        'difficulty' => 'easy',
                        'marks' => 1,
                        'explanation' => 'Earth’s atmosphere is composed of about 78% Nitrogen, 21% Oxygen, and 1% other gases.',
                        'options' => [['Nitrogen', true], ['Oxygen', false], ['Carbon Dioxide', false], ['Argon', false]],
                    ],
                    [
                        'text' => 'Which is the longest river in South America?',
                        'difficulty' => 'easy',
                        'marks' => 1,
                        'explanation' => 'The Amazon River is the longest river in South America and the largest by water discharge.',
                        'options' => [['Amazon River', true], ['Nile River', false], ['Paraná River', false], ['Orinoco River', false]],
                    ],
                    [
                        'text' => 'Who invented the World Wide Web in 1989?',
                        'difficulty' => 'easy',
                        'marks' => 1,
                        'explanation' => 'Sir Tim Berners-Lee invented the World Wide Web while working at CERN in 1989.',
                        'options' => [['Tim Berners-Lee', true], ['Vint Cerf', false], ['Alan Turing', false], ['Dennis Ritchie', false]],
                    ],
                ],
            ],
            [
                'category' => ['name' => 'History', 'slug' => 'history', 'description' => 'Ancient civilizations, revolutions, and modern world history.'],
                'exam' => [
                    'title' => 'World & Modern History (Timed Assessment)',
                    'slug' => 'world-modern-history-timed',
                    'description' => 'Evaluate your knowledge of key historical events, empires, and turning points.',
                    'instructions' => "1. Complete all 5 questions within 15 minutes.\n2. Answers and explanations will be shown after submission.",
                    'duration_minutes' => 15,
                    'pass_percentage' => 60,
                    'mode' => 'standard',
                    'allow_negative_marking' => false,
                ],
                'questions' => [
                    [
                        'text' => 'In which year did the Berlin Wall fall?',
                        'difficulty' => 'easy',
                        'marks' => 2,
                        'explanation' => 'The Berlin Wall fell on November 9, 1989, symbolizing the end of the Cold War.',
                        'options' => [['1989', true], ['1991', false], ['1985', false], ['1979', false]],
                    ],
                    [
                        'text' => 'Which ancient civilization built the citadel of Machu Picchu?',
                        'difficulty' => 'easy',
                        'marks' => 2,
                        'explanation' => 'Machu Picchu was built by the Inca Empire in the 15th century in modern-day Peru.',
                        'options' => [['Inca Empire', true], ['Maya Civilization', false], ['Aztec Empire', false], ['Roman Empire', false]],
                    ],
                    [
                        'text' => 'Who was the first woman to win a Nobel Prize?',
                        'difficulty' => 'medium',
                        'marks' => 2,
                        'explanation' => 'Marie Curie won the Nobel Prize in Physics in 1903 (and later in Chemistry in 1911).',
                        'options' => [['Marie Curie', true], ['Rosalind Franklin', false], ['Ada Lovelace', false], ['Florence Nightingale', false]],
                    ],
                    [
                        'text' => 'The Renaissance began in the 14th century in which European country?',
                        'difficulty' => 'easy',
                        'marks' => 2,
                        'explanation' => 'The Renaissance originated in Florence, Italy, before spreading across Europe.',
                        'options' => [['Italy', true], ['France', false], ['England', false], ['Germany', false]],
                    ],
                    [
                        'text' => 'Which treaty formally ended World War I in 1919?',
                        'difficulty' => 'medium',
                        'marks' => 2,
                        'explanation' => 'The Treaty of Versailles was signed on June 28, 1919, formally ending World War I.',
                        'options' => [['Treaty of Versailles', true], ['Treaty of Paris', false], ['Treaty of Tordesillas', false], ['Treaty of Utrecht', false]],
                    ],
                ],
            ],
            [
                'category' => ['name' => 'Basic Mathematics', 'slug' => 'basic-mathematics', 'description' => 'Arithmetic, percentages, ratios, and foundational algebra.'],
                'exam' => [
                    'title' => 'Basic Mathematics & Aptitude (Instant Answer)',
                    'slug' => 'basic-maths-aptitude-instant',
                    'description' => 'Sharpen your core math skills including percentages, averages, ratios, and simple equations.',
                    'instructions' => "1. Solve each problem and click 'Check Answer' to view the step-by-step solution.",
                    'duration_minutes' => 20,
                    'pass_percentage' => 50,
                    'mode' => 'instant_answer',
                    'allow_negative_marking' => false,
                ],
                'questions' => [
                    [
                        'text' => 'What is 15% of 240?',
                        'difficulty' => 'easy',
                        'marks' => 1,
                        'explanation' => '10% of 240 is 24, and 5% is 12. Therefore, 15% = 24 + 12 = 36.',
                        'options' => [['36', true], ['32', false], ['40', false], ['42', false]],
                    ],
                    [
                        'text' => 'If 5x + 3 = 28, what is the value of x?',
                        'difficulty' => 'easy',
                        'marks' => 1,
                        'explanation' => 'Subtract 3 from both sides: 5x = 25. Divide by 5: x = 5.',
                        'options' => [['5', true], ['4', false], ['6', false], ['7', false]],
                    ],
                    [
                        'text' => 'What is the average of the numbers 12, 18, 24, and 30?',
                        'difficulty' => 'easy',
                        'marks' => 1,
                        'explanation' => 'Sum = 12 + 18 + 24 + 30 = 84. Divide by 4 numbers: 84 / 4 = 21.',
                        'options' => [['21', true], ['20', false], ['22', false], ['24', false]],
                    ],
                    [
                        'text' => 'A train travels 180 km in 3 hours. What is its average speed?',
                        'difficulty' => 'easy',
                        'marks' => 1,
                        'explanation' => 'Speed = Distance / Time = 180 km / 3 hours = 60 km/h.',
                        'options' => [['60 km/h', true], ['50 km/h', false], ['65 km/h', false], ['90 km/h', false]],
                    ],
                    [
                        'text' => 'What is the area of a rectangle with length 14 cm and width 6 cm?',
                        'difficulty' => 'easy',
                        'marks' => 1,
                        'explanation' => 'Area of a rectangle = length × width = 14 × 6 = 84 cm².',
                        'options' => [['84 cm²', true], ['40 cm²', false], ['72 cm²', false], ['96 cm²', false]],
                    ],
                ],
            ],
            [
                'category' => ['name' => 'English Grammar', 'slug' => 'english-grammar', 'description' => 'Tenses, prepositions, subject-verb agreement, and vocabulary.'],
                'exam' => [
                    'title' => 'English Grammar & Usage Mastery (Instant Answer)',
                    'slug' => 'english-grammar-instant',
                    'description' => 'Practice essential English grammar rules, tenses, prepositions, and sentence correction with instant explanations.',
                    'instructions' => "1. Choose the grammatically correct option and check your answer immediately.",
                    'duration_minutes' => 15,
                    'pass_percentage' => 50,
                    'mode' => 'instant_answer',
                    'allow_negative_marking' => false,
                ],
                'questions' => [
                    [
                        'text' => 'Choose the correct sentence:',
                        'difficulty' => 'easy',
                        'marks' => 1,
                        'explanation' => '"Each" is singular and requires a singular verb ("is"), not "are".',
                        'options' => [
                            ['Each of the students is ready for the exam.', true],
                            ['Each of the students are ready for the exam.', false],
                            ['Each of the student is ready for the exam.', false],
                            ['Each of the students were ready for the exam.', false],
                        ],
                    ],
                    [
                        'text' => 'Fill in the blank: She has been working here _____ 2021.',
                        'difficulty' => 'easy',
                        'marks' => 1,
                        'explanation' => 'Use "since" with a specific starting point in time (2021), and "for" with a duration.',
                        'options' => [['since', true], ['for', false], ['from', false], ['by', false]],
                    ],
                    [
                        'text' => 'Identify the correct passive voice form of: "The chef cooked a delicious meal."',
                        'difficulty' => 'medium',
                        'marks' => 1,
                        'explanation' => 'Past simple active ("cooked") converts to "was/were + past participle" ("was cooked") in passive voice.',
                        'options' => [
                            ['A delicious meal was cooked by the chef.', true],
                            ['A delicious meal is cooked by the chef.', false],
                            ['A delicious meal has been cooked by the chef.', false],
                            ['A delicious meal was cooking by the chef.', false],
                        ],
                    ],
                    [
                        'text' => 'Which word is a synonym for "Ubiquitous"?',
                        'difficulty' => 'hard',
                        'marks' => 2,
                        'explanation' => '"Ubiquitous" means present, appearing, or found everywhere (omnipresent).',
                        'options' => [['Omnipresent', true], ['Rare', false], ['Temporary', false], ['Ambiguous', false]],
                    ],
                    [
                        'text' => 'Fill in the blank: Neither the manager nor the employees _____ aware of the change.',
                        'difficulty' => 'medium',
                        'marks' => 1,
                        'explanation' => 'With "neither...nor", the verb agrees with the closer subject ("employees", which is plural → "were").',
                        'options' => [['were', true], ['was', false], ['is', false], ['has been', false]],
                    ],
                ],
            ],
            [
                'category' => ['name' => 'Computer Science', 'slug' => 'computer-science', 'description' => 'Data structures, algorithms, OS, networking, and architecture.'],
                'exam' => [
                    'title' => 'Computer Science Fundamentals (Timed Assessment)',
                    'slug' => 'computer-science-fundamentals-timed',
                    'description' => 'Core CS concepts covering Big-O complexity, data structures, operating systems, and networking.',
                    'instructions' => "1. 20-minute timed assessment.\n2. Negative marking of 0.25 applies to wrong answers.",
                    'duration_minutes' => 20,
                    'pass_percentage' => 60,
                    'mode' => 'standard',
                    'allow_negative_marking' => true,
                ],
                'questions' => [
                    [
                        'text' => 'What is the average-case time complexity of Binary Search on a sorted array of n elements?',
                        'difficulty' => 'easy',
                        'marks' => 2,
                        'negative_marks' => 0.25,
                        'explanation' => 'Binary Search halves the search space on each comparison, resulting in O(log n) time complexity.',
                        'options' => [['O(log n)', true], ['O(n)', false], ['O(n log n)', false], ['O(1)', false]],
                    ],
                    [
                        'text' => 'Which data structure follows the LIFO (Last-In, First-Out) principle?',
                        'difficulty' => 'easy',
                        'marks' => 2,
                        'negative_marks' => 0.25,
                        'explanation' => 'A Stack pushes and pops elements from the top, following Last-In, First-Out (LIFO).',
                        'options' => [['Stack', true], ['Queue', false], ['Linked List', false], ['Hash Table', false]],
                    ],
                    [
                        'text' => 'Which OSI layer is responsible for routing packets across different networks using IP addresses?',
                        'difficulty' => 'medium',
                        'marks' => 2,
                        'negative_marks' => 0.25,
                        'explanation' => 'The Network Layer (Layer 3) handles logical addressing (IP) and packet routing.',
                        'options' => [['Network Layer (Layer 3)', true], ['Data Link Layer (Layer 2)', false], ['Transport Layer (Layer 4)', false], ['Session Layer (Layer 5)', false]],
                    ],
                    [
                        'text' => 'In Operating Systems, which condition is NOT one of the four Coffman conditions for a deadlock?',
                        'difficulty' => 'hard',
                        'marks' => 2,
                        'negative_marks' => 0.25,
                        'explanation' => 'The four Coffman conditions are Mutual Exclusion, Hold and Wait, No Preemption, and Circular Wait. "Preemptive Scheduling" is not one of them.',
                        'options' => [['Preemptive Scheduling', true], ['Mutual Exclusion', false], ['Hold and Wait', false], ['Circular Wait', false]],
                    ],
                    [
                        'text' => 'What does DNS stand for in computer networking?',
                        'difficulty' => 'easy',
                        'marks' => 2,
                        'negative_marks' => 0.25,
                        'explanation' => 'DNS stands for Domain Name System, which translates human-readable domain names into IP addresses.',
                        'options' => [['Domain Name System', true], ['Dynamic Network Service', false], ['Data Node Server', false], ['Distributed Name Storage', false]],
                    ],
                ],
            ],
            [
                'category' => ['name' => 'Digital Marketing', 'slug' => 'digital-marketing', 'description' => 'SEO, SEM, PPC, social media analytics, and conversion optimization.'],
                'exam' => [
                    'title' => 'Digital Marketing & SEO Essentials (Instant Answer)',
                    'slug' => 'digital-marketing-seo-instant',
                    'description' => 'Master SEO, Google Ads, CTR/CPC metrics, and content marketing strategies with instant feedback.',
                    'instructions' => "1. Practice mode with instant answers and marketing explanations.",
                    'duration_minutes' => 15,
                    'pass_percentage' => 50,
                    'mode' => 'instant_answer',
                    'allow_negative_marking' => false,
                ],
                'questions' => [
                    [
                        'text' => 'In Digital Marketing, what does CTR stand for?',
                        'difficulty' => 'easy',
                        'marks' => 1,
                        'explanation' => 'CTR stands for Click-Through Rate: (Total Clicks / Total Impressions) × 100.',
                        'options' => [['Click-Through Rate', true], ['Cost To Reach', false], ['Conversion Tracking Ratio', false], ['Customer Target Rate', false]],
                    ],
                    [
                        'text' => 'Which HTML tag is most important for telling search engines the main topic of a web page?',
                        'difficulty' => 'easy',
                        'marks' => 1,
                        'explanation' => 'The `<title>` tag defines the page title shown in search engine results (SERPs) and is a key on-page SEO signal.',
                        'options' => [['<title> tag', true], ['<meta name="keywords">', false], ['<footer> tag', false], ['<script> tag', false]],
                    ],
                    [
                        'text' => 'What is the primary purpose of a "Canonical Tag" (rel="canonical") in SEO?',
                        'difficulty' => 'medium',
                        'marks' => 2,
                        'explanation' => 'A canonical tag tells search engines which URL is the master copy when duplicate or similar content exists across multiple URLs.',
                        'options' => [
                            ['Prevent duplicate content issues by specifying the preferred URL', true],
                            ['Block search engines from indexing a page', false],
                            ['Increase page load speed via caching', false],
                            ['Automatically translate a page into other languages', false],
                        ],
                    ],
                    [
                        'text' => 'If an ad campaign has 50,000 impressions and 1,000 clicks, what is its CTR?',
                        'difficulty' => 'medium',
                        'marks' => 2,
                        'explanation' => 'CTR = (1,000 / 50,000) × 100 = 2%.',
                        'options' => [['2%', true], ['5%', false], ['0.5%', false], ['20%', false]],
                    ],
                    [
                        'text' => 'Which metric measures the percentage of visitors who leave a website after viewing only one page?',
                        'difficulty' => 'easy',
                        'marks' => 1,
                        'explanation' => 'Bounce Rate represents single-page sessions where the user leaves without interacting further.',
                        'options' => [['Bounce Rate', true], ['Exit Rate', false], ['Churn Rate', false], ['Conversion Rate', false]],
                    ],
                ],
            ],
        ];

        foreach ($subjects as $entry) {
            $catData = $entry['category'];
            $category = Category::firstOrCreate(
                ['slug' => $catData['slug']],
                ['name' => $catData['name'], 'description' => $catData['description'], 'is_active' => true]
            );

            $examData = $entry['exam'];
            $exam = Exam::firstOrCreate(
                ['slug' => $examData['slug']],
                [
                    'category_id' => $category->id,
                    'created_by' => $creator->id,
                    'title' => $examData['title'],
                    'description' => $examData['description'],
                    'instructions' => $examData['instructions'],
                    'duration_minutes' => $examData['duration_minutes'],
                    'pass_percentage' => $examData['pass_percentage'],
                    'mode' => $examData['mode'],
                    'lock_after_instant_reveal' => true,
                    'allow_negative_marking' => $examData['allow_negative_marking'],
                    'shuffle_questions' => true,
                    'shuffle_options' => true,
                    'show_leaderboard' => true,
                    'is_published' => true,
                ]
            );

            if ($exam->questions()->count() === 0) {
                foreach ($entry['questions'] as $idx => $qData) {
                    $question = Question::create([
                        'category_id' => $category->id,
                        'created_by' => $creator->id,
                        'question_text' => $qData['text'],
                        'type' => 'single_choice',
                        'difficulty' => $qData['difficulty'],
                        'marks' => $qData['marks'],
                        'negative_marks' => $qData['negative_marks'] ?? 0,
                        'explanation' => $qData['explanation'],
                        'is_active' => true,
                    ]);

                    foreach ($qData['options'] as $oIdx => [$optText, $isCorrect]) {
                        Option::create([
                            'question_id' => $question->id,
                            'option_text' => $optText,
                            'is_correct' => $isCorrect,
                            'sort_order' => $oIdx + 1,
                        ]);
                    }

                    $exam->questions()->attach($question->id, ['sort_order' => $idx + 1]);
                }

                $exam->recalculateTotalMarks();
            }
        }
    }
}

