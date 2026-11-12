# ExamPortal — Online MCQ Exam & Instant Practice Platform

A full-featured online MCQ examination and instant practice portal built with **Laravel 12**, **Tailwind CSS**, **Alpine.js**, and **MySQL (XAMPP / phpMyAdmin)**.

---

## Key Features

- **Dual Exam Modes**
  - **Instant Answer Mode (Practice):** AJAX-powered immediate answer verification with green/red option highlights, marks awarded, optional post-reveal locking, and detailed explanations.
  - **Standard Timed Mode (Assessment):** Server-enforced countdown timer with automatic submission on expiry, hidden answer keys until completion, and optional negative marking.
- **Interactive Exam Runner**
  - Live completion progress bar and color-coded **Question Palette** (Current, Answered, Marked for Review, Unanswered).
  - Randomized question and option ordering per attempt.
  - Tab-switch integrity counter (`visibilitychange` tracking), question bookmarking, and question issue reporting.
- **Post-Exam Analytics & Certificates**
  - Detailed question-by-question answer review with explanations.
  - Exam **Leaderboards** ranked by score and completion time.
  - Downloadable **PDF Scorecards & Certificates** with unique verification IDs (`barryvdh/laravel-dompdf`).
- **Admin & Examiner Panel**
  - Role-based access control (`admin`, `examiner`, `student`).
  - Category & Subject CRUD, Question Bank CRUD (single choice, multiple choice, true/false, image support), and **Bulk CSV Import** with a downloadable sample template.
  - Exam Builder with question assignment, results viewer, and CSV export.
  - Student-reported question moderation and user role management.

---

## Tech Stack

- **Backend:** PHP 8.2+, Laravel 12, Laravel Breeze
- **Database:** MySQL / MariaDB (`exam` database via XAMPP / phpMyAdmin)
- **Frontend:** Blade, Tailwind CSS, Alpine.js, Vite
- **PDF Generation:** `barryvdh/laravel-dompdf`

---

## Seeded Subjects & Exams

1. **Laravel & PHP** — *Laravel & PHP Mastery (Instant Answer)*
2. **Database & MySQL** — *MySQL & Web Security (Timed Assessment)*
3. **General Knowledge** — *General Knowledge & World Affairs (Instant Answer)*
4. **History** — *World & Modern History (Timed Assessment)*
5. **Basic Mathematics** — *Basic Mathematics & Aptitude (Instant Answer)*
6. **English Grammar** — *English Grammar & Usage Mastery (Instant Answer)*
7. **Computer Science** — *Computer Science Fundamentals (Timed Assessment)*
8. **Digital Marketing** — *Digital Marketing & SEO Essentials (Instant Answer)*

---

## Local Setup (XAMPP + MySQL)

1. **Clone the repository**
   ```bash
   git clone https://github.com/webotapp-academy/exam-portal.git
   cd exam-portal
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Update `.env` with your XAMPP MySQL `exam` database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=exam
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Migrate & seed the database**
   ```bash
   php artisan storage:link
   php artisan migrate --seed
   ```

5. **Run tests**
   ```bash
   php artisan test
   ```

---

## Demo Accounts

All seeded accounts use the password: `password`

| Role | Email |
| :--- | :--- |
| **Admin** | `admin@examportal.test` |
| **Examiner** | `examiner@examportal.test` |
| **Student** | `student@examportal.test` |
