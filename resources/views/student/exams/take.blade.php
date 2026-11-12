<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase {{ $exam->isInstantAnswerMode() ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $exam->isInstantAnswerMode() ? 'Instant Answer Mode' : 'Timed Exam' }}
                    </span>
                    <span class="text-xs text-slate-400 font-medium">Attempt #{{ $attempt->attempt_number }}</span>
                </div>
                <h2 class="font-extrabold text-xl text-slate-900 mt-1">{{ $exam->title }}</h2>
            </div>
            <div class="flex items-center gap-3">
                @if($remainingSeconds !== null)
                    <div id="timer-badge" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-mono font-bold text-sm shadow-sm flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span id="timer-display">--:--</span>
                    </div>
                @else
                    <div class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold">Untimed Practice</div>
                @endif
                <form id="submit-exam-form" method="POST" action="{{ route('attempts.submit', $attempt) }}" onsubmit="return confirm('Submit exam and finalize your score?');">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700 shadow-sm transition">
                        Finish &amp; Submit
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div
        x-data="examRunner()"
        x-init="init()"
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 space-y-4"
    >
        {{-- Live Progress Bar --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 px-5 py-3 shadow-xs flex items-center gap-4">
            <span class="text-xs font-bold text-slate-600 whitespace-nowrap">
                Progress: <span x-text="answeredCount()"></span> / <span x-text="questions.length"></span>
            </span>
            <div class="flex-1 h-2.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-indigo-600 to-emerald-500 transition-all duration-300" :style="`width: ${progressPercent()}%`"></div>
            </div>
            <span class="text-xs font-bold text-indigo-600" x-text="progressPercent() + '%'"></span>
        </div>

        {{-- Tab switch warning --}}
        <div x-show="tabSwitches > 0" x-cloak class="rounded-xl bg-amber-50 border border-amber-300 px-4 py-2.5 text-xs text-amber-900 flex items-center justify-between">
            <span><strong>Integrity Notice:</strong> Tab/window switches detected: <span x-text="tabSwitches" class="font-bold"></span>. Please stay on this exam page.</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- Main Question Area --}}
            <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-8">
                <template x-if="currentQuestion">
                    <div>
                        <div class="flex flex-wrap items-center justify-between gap-2 pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-extrabold">
                                    Q<span x-text="currentIndex + 1"></span> / <span x-text="questions.length"></span>
                                </span>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 uppercase" x-text="currentQuestion.type.replace('_', ' ')"></span>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700" x-text="currentQuestion.marks + ' Marks'"></span>
                                <template x-if="currentQuestion.negative_marks > 0">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-50 text-red-700" x-text="'-' + currentQuestion.negative_marks + ' Neg'"></span>
                                </template>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" @click="toggleBookmark()" class="px-3 py-1.5 rounded-lg border text-xs font-semibold transition"
                                    :class="currentQuestion.is_bookmarked ? 'bg-amber-50 border-amber-300 text-amber-700' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                                    <span x-text="currentQuestion.is_bookmarked ? '★ Bookmarked' : '☆ Bookmark'"></span>
                                </button>
                                <button type="button" @click="toggleFlag()" class="px-3 py-1.5 rounded-lg border text-xs font-semibold transition"
                                    :class="currentQuestion.is_marked_for_review ? 'bg-purple-50 border-purple-300 text-purple-700' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                                    <span x-text="currentQuestion.is_marked_for_review ? '⚑ Marked' : '⚐ Review'"></span>
                                </button>
                                <button type="button" @click="openReportModal()" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs text-slate-500 hover:text-red-600">
                                    Report
                                </button>
                            </div>
                        </div>

                        <div class="mt-6">
                            <p class="text-lg sm:text-xl font-semibold text-slate-900 leading-relaxed whitespace-pre-line" x-text="currentQuestion.question_text"></p>
                            <template x-if="currentQuestion.question_image">
                                <img :src="currentQuestion.question_image" alt="Question illustration" class="mt-4 max-h-72 rounded-xl border border-slate-200">
                            </template>
                        </div>

                        {{-- Lettered Option Cards --}}
                        <div class="mt-6 space-y-3">
                            <template x-for="(opt, oIdx) in currentQuestion.options" :key="opt.id">
                                <label
                                    @click.prevent="selectOption(opt.id)"
                                    class="flex items-center gap-3.5 p-4 rounded-xl border-2 cursor-pointer transition select-none"
                                    :class="optionClass(opt.id)"
                                >
                                    <span class="h-8 w-8 shrink-0 rounded-lg flex items-center justify-center text-xs font-extrabold border"
                                          :class="isSelected(opt.id) ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-slate-50 text-slate-600 border-slate-200'"
                                          x-text="String.fromCharCode(65 + oIdx)">
                                    </span>
                                    <input
                                        :type="currentQuestion.type === 'multiple_choice' ? 'checkbox' : 'radio'"
                                        :checked="isSelected(opt.id)"
                                        :disabled="isLocked()"
                                        class="sr-only"
                                    >
                                    <div class="flex-1 text-sm sm:text-base font-medium text-slate-800" x-text="opt.option_text"></div>
                                    <template x-if="currentQuestion.instant_revealed && currentQuestion.correct_option_ids.includes(opt.id)">
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">✓ Correct</span>
                                    </template>
                                    <template x-if="currentQuestion.instant_revealed && isSelected(opt.id) && !currentQuestion.correct_option_ids.includes(opt.id)">
                                        <span class="px-2.5 py-0.5 rounded-full bg-red-100 text-red-800 text-xs font-bold">✕ Wrong</span>
                                    </template>
                                </label>
                            </template>
                        </div>

                        @if($exam->isInstantAnswerMode())
                            <div class="mt-6 flex items-center gap-3">
                                <button
                                    type="button"
                                    @click="checkInstantAnswer()"
                                    :disabled="currentQuestion.selected_option_ids.length === 0 || isLocked() || saving"
                                    class="px-6 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed shadow-sm transition"
                                >
                                    <span x-text="currentQuestion.instant_revealed ? 'Answer Checked' : 'Check Answer'"></span>
                                </button>
                                <span x-show="saving" class="text-xs font-medium text-slate-500">Checking...</span>
                            </div>

                            <div x-show="currentQuestion.instant_revealed" x-cloak class="mt-5 rounded-2xl p-5 border"
                                :class="currentQuestion.is_correct ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200'">
                                <div class="flex items-center justify-between font-extrabold text-sm"
                                    :class="currentQuestion.is_correct ? 'text-emerald-800' : 'text-red-800'">
                                    <span x-text="currentQuestion.is_correct ? '✓ Correct Answer!' : '✕ Incorrect Answer'"></span>
                                    <span x-text="'Marks Awarded: ' + currentQuestion.marks_awarded"></span>
                                </div>
                                <template x-if="currentQuestion.explanation">
                                    <div class="mt-3 pt-3 border-t border-black/5 text-sm text-slate-800 leading-relaxed">
                                        <strong class="block text-xs uppercase tracking-wider text-slate-500 mb-1">Explanation</strong>
                                        <span x-text="currentQuestion.explanation"></span>
                                    </div>
                                </template>
                            </div>
                        @endif

                        <div class="mt-8 pt-5 border-t border-slate-100 flex items-center justify-between">
                            <button type="button" @click="prev()" :disabled="currentIndex === 0"
                                class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40 transition">
                                &larr; Previous
                            </button>
                            <button type="button" @click="next()" :disabled="currentIndex === questions.length - 1"
                                class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 disabled:opacity-40 transition">
                                Next Question &rarr;
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Sticky Question Palette Sidebar --}}
            <div class="lg:col-span-4 space-y-4">
                <div class="sticky top-20 bg-white rounded-2xl border border-slate-200/90 shadow-sm p-5">
                    <h3 class="text-sm font-extrabold text-slate-900 mb-3">Question Palette</h3>
                    <div class="grid grid-cols-5 gap-2">
                        <template x-for="(q, idx) in questions" :key="q.id">
                            <button
                                type="button"
                                @click="goTo(idx)"
                                class="h-10 rounded-xl text-sm font-bold border transition"
                                :class="paletteClass(q, idx)"
                                x-text="idx + 1"
                            ></button>
                        </template>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 grid grid-cols-2 gap-2.5 text-xs font-medium text-slate-600">
                        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-indigo-600 inline-block"></span> Current</div>
                        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-emerald-500 inline-block"></span> Answered / Right</div>
                        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-purple-500 inline-block"></span> Marked Review</div>
                        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-slate-200 inline-block"></span> Unanswered</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Report Modal --}}
        <div x-show="reportOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
                <h3 class="text-lg font-bold text-slate-900">Report Question Issue</h3>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Reason</label>
                    <select x-model="reportReason" class="w-full rounded-xl border-slate-300 text-sm">
                        <option value="Wrong answer key">Wrong answer key</option>
                        <option value="Typo or unclear wording">Typo or unclear wording</option>
                        <option value="Outdated information">Outdated information</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Details (optional)</label>
                    <textarea x-model="reportDetails" rows="3" class="w-full rounded-xl border-slate-300 text-sm"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="reportOpen = false" class="px-4 py-2 rounded-xl border text-sm font-medium">Cancel</button>
                    <button type="button" @click="submitReport()" class="px-4 py-2 rounded-xl bg-red-600 text-white text-sm font-semibold">Submit Report</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function examRunner() {
            return {
                questions: @json($questionsPayload),
                currentIndex: 0,
                saving: false,
                tabSwitches: {{ (int) $attempt->tab_switch_count }},
                remainingSeconds: {{ $remainingSeconds === null ? 'null' : (int) $remainingSeconds }},
                isInstantMode: {{ $exam->isInstantAnswerMode() ? 'true' : 'false' }},
                lockAfterReveal: {{ $exam->lock_after_instant_reveal ? 'true' : 'false' }},
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                reportOpen: false,
                reportReason: 'Wrong answer key',
                reportDetails: '',

                get currentQuestion() {
                    return this.questions[this.currentIndex] || null;
                },

                answeredCount() {
                    return this.questions.filter(q => q.selected_option_ids.length > 0).length;
                },

                progressPercent() {
                    if (!this.questions.length) return 0;
                    return Math.round((this.answeredCount() / this.questions.length) * 100);
                },

                init() {
                    if (this.remainingSeconds !== null) {
                        this.startTimer();
                    }
                    document.addEventListener('visibilitychange', () => {
                        if (document.hidden) {
                            fetch('{{ route('attempts.tab-switch', $attempt) }}', {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' }
                            }).then(r => r.json()).then(d => {
                                if (d.tab_switch_count !== undefined) this.tabSwitches = d.tab_switch_count;
                            });
                        }
                    });
                },

                startTimer() {
                    const display = document.getElementById('timer-display');
                    const badge = document.getElementById('timer-badge');
                    const tick = () => {
                        if (this.remainingSeconds <= 0) {
                            display.textContent = '00:00';
                            document.getElementById('submit-exam-form').submit();
                            return;
                        }
                        if (this.remainingSeconds <= 120 && badge) {
                            badge.classList.remove('bg-slate-900');
                            badge.classList.add('bg-red-600');
                        }
                        const m = String(Math.floor(this.remainingSeconds / 60)).padStart(2, '0');
                        const s = String(this.remainingSeconds % 60).padStart(2, '0');
                        display.textContent = `${m}:${s}`;
                        this.remainingSeconds--;
                    };
                    tick();
                    setInterval(tick, 1000);
                },

                isLocked() {
                    return this.isInstantMode && this.lockAfterReveal && this.currentQuestion.instant_revealed;
                },

                isSelected(optId) {
                    return this.currentQuestion.selected_option_ids.includes(optId);
                },

                selectOption(optId) {
                    if (this.isLocked()) return;
                    if (this.currentQuestion.type === 'multiple_choice') {
                        const idx = this.currentQuestion.selected_option_ids.indexOf(optId);
                        if (idx === -1) {
                            this.currentQuestion.selected_option_ids.push(optId);
                        } else {
                            this.currentQuestion.selected_option_ids.splice(idx, 1);
                        }
                    } else {
                        this.currentQuestion.selected_option_ids = [optId];
                    }
                    this.persistAnswer(false);
                },

                checkInstantAnswer() {
                    if (!this.isInstantMode || this.currentQuestion.selected_option_ids.length === 0) return;
                    this.persistAnswer(true);
                },

                async persistAnswer(checkInstant) {
                    this.saving = true;
                    try {
                        const res = await fetch('{{ route('attempts.answer', $attempt) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrf,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                question_id: this.currentQuestion.id,
                                selected_option_ids: this.currentQuestion.selected_option_ids,
                                check_instant: checkInstant
                            })
                        });
                        const data = await res.json();
                        if (data.expired && data.redirect) {
                            window.location.href = data.redirect;
                            return;
                        }
                        if (data.mode === 'instant_answer' && data.correct_option_ids) {
                            this.currentQuestion.instant_revealed = true;
                            this.currentQuestion.is_correct = data.is_correct;
                            this.currentQuestion.correct_option_ids = data.correct_option_ids;
                            this.currentQuestion.explanation = data.explanation;
                            this.currentQuestion.marks_awarded = data.marks_awarded;
                        }
                    } finally {
                        this.saving = false;
                    }
                },

                async toggleFlag() {
                    const res = await fetch('{{ route('attempts.flag', $attempt) }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                        body: JSON.stringify({ question_id: this.currentQuestion.id })
                    });
                    const data = await res.json();
                    this.currentQuestion.is_marked_for_review = data.is_marked_for_review;
                },

                async toggleBookmark() {
                    const url = `/questions/${this.currentQuestion.id}/bookmark`;
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    this.currentQuestion.is_bookmarked = data.bookmarked;
                },

                openReportModal() {
                    this.reportDetails = '';
                    this.reportOpen = true;
                },

                async submitReport() {
                    const url = `/questions/${this.currentQuestion.id}/report`;
                    await fetch(url, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
                        body: JSON.stringify({ reason: this.reportReason, details: this.reportDetails })
                    });
                    this.reportOpen = false;
                    alert('Question reported for review. Thank you!');
                },

                optionClass(optId) {
                    const q = this.currentQuestion;
                    if (q.instant_revealed) {
                        if (q.correct_option_ids.includes(optId)) return 'border-emerald-500 bg-emerald-50/70';
                        if (this.isSelected(optId)) return 'border-red-500 bg-red-50/70';
                        return 'border-slate-200 bg-slate-50 opacity-70';
                    }
                    return this.isSelected(optId)
                        ? 'border-indigo-600 bg-indigo-50/60 shadow-xs'
                        : 'border-slate-200 hover:border-indigo-300 hover:bg-slate-50/50';
                },

                paletteClass(q, idx) {
                    if (idx === this.currentIndex) return 'bg-indigo-600 text-white border-indigo-600 shadow-xs';
                    if (q.instant_revealed) {
                        return q.is_correct
                            ? 'bg-emerald-500 text-white border-emerald-500'
                            : 'bg-red-500 text-white border-red-500';
                    }
                    if (q.is_marked_for_review) return 'bg-purple-500 text-white border-purple-500';
                    if (q.selected_option_ids.length > 0) return 'bg-emerald-100 text-emerald-800 border-emerald-300';
                    return 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200';
                },

                goTo(idx) { this.currentIndex = idx; },
                prev() { if (this.currentIndex > 0) this.currentIndex--; },
                next() { if (this.currentIndex < this.questions.length - 1) this.currentIndex++; }
            };
        }
    </script>
</x-app-layout>
