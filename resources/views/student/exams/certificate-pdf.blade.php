<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Scorecard - {{ $attempt->verificationCode() }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1e293b; margin: 0; padding: 30px; }
        .frame { border: 6px double #4f46e5; padding: 35px; text-align: center; }
        .brand { font-size: 16px; font-weight: bold; color: #4f46e5; letter-spacing: 2px; text-transform: uppercase; }
        h1 { font-size: 32px; margin: 12px 0 8px; color: #0f172a; }
        .sub { font-size: 14px; color: #64748b; margin-bottom: 24px; }
        .student { font-size: 26px; font-weight: bold; color: #1e293b; margin: 10px 0; }
        .exam { font-size: 18px; color: #4f46e5; font-weight: bold; margin-bottom: 24px; }
        .grid { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .grid td { border: 1px solid #cbd5e1; padding: 12px; font-size: 14px; }
        .label { font-size: 11px; color: #64748b; text-transform: uppercase; display: block; }
        .val { font-size: 18px; font-weight: bold; margin-top: 4px; }
        .pass { color: #059669; }
        .fail { color: #dc2626; }
        .footer { margin-top: 28px; font-size: 11px; color: #64748b; }
    </style>
</head>
<body>
    <div class="frame">
        <div class="brand">ExamPortal Official Scorecard</div>
        <h1>{{ $attempt->is_passed ? 'Certificate of Achievement' : 'Exam Result Summary' }}</h1>
        <div class="sub">This document certifies the exam performance of</div>

        <div class="student">{{ $attempt->user->name }}</div>
        <div class="exam">{{ $attempt->exam->title }} ({{ $attempt->exam->category->name ?? 'General' }})</div>

        <table class="grid">
            <tr>
                <td>
                    <span class="label">Status</span>
                    <span class="val {{ $attempt->is_passed ? 'pass' : 'fail' }}">
                        {{ $attempt->is_passed ? 'PASSED' : 'NOT PASSED' }}
                    </span>
                </td>
                <td>
                    <span class="label">Score</span>
                    <span class="val">{{ $attempt->score }} / {{ $attempt->total_marks }}</span>
                </td>
                <td>
                    <span class="label">Percentage</span>
                    <span class="val">{{ $attempt->percentage }}%</span>
                </td>
                <td>
                    <span class="label">Correct / Wrong</span>
                    <span class="val">{{ $attempt->correct_count }} / {{ $attempt->wrong_count }}</span>
                </td>
                <td>
                    <span class="label">Time Taken</span>
                    <span class="val">{{ $attempt->durationFormatted() }}</span>
                </td>
            </tr>
        </table>

        <div class="footer">
            Submitted on {{ $attempt->submitted_at?->format('F j, Y g:i A') }} &bull;
            Verification ID: {{ $attempt->verificationCode() }}
        </div>
    </div>
</body>
</html>

