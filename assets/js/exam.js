document.addEventListener('DOMContentLoaded', () => {
    const questions = [...document.querySelectorAll('.exam-question')];
    const answeredCount = document.getElementById('answered-count');

    if (!questions.length) return;

    let answered = 0;
    let correctAnswers = 0;

    function updateProgress() {
        if (answeredCount) {
            answeredCount.textContent = String(answered);
        }
    }

    questions.forEach((question) => {
        const correct = question.dataset.correct;
        const buttons = [...question.querySelectorAll('.exam-option')];
        const feedback = question.querySelector('.exam-feedback');

        buttons.forEach((button) => {
            button.addEventListener('click', () => {
                if (question.dataset.answered === '1') return;

                question.dataset.answered = '1';
                answered += 1;

                const selected = button.dataset.key;
                const isCorrect = selected === correct;

                if (isCorrect) {
                    correctAnswers += 1;
                }

                buttons.forEach((item) => {
                    item.disabled = true;
                    if (item.dataset.key === correct) {
                        item.classList.add('correct');
                    }
                });

                if (!isCorrect) {
                    button.classList.add('wrong');
                }

                if (feedback) {
                    feedback.className = 'exam-feedback ' + (isCorrect ? 'correct' : 'wrong');
                    feedback.textContent = isCorrect
                        ? '✓ Richtig!'
                        : '✗ Falsch – die richtige Antwort ist markiert.';
                }

                updateProgress();

                if (answered === questions.length) {
                    const percent = Math.round((correctAnswers / questions.length) * 100);
                    const result = document.createElement('section');
                    result.className = 'exam-result';
                    result.innerHTML =
                        '<h2>Prüfung abgeschlossen</h2>' +
                        '<div class="exam-score"><strong>' + correctAnswers + ' / ' + questions.length + '</strong></div>' +
                        '<div id="score-percent">' + percent + ' % richtig</div>';
                    const wrapper = document.querySelector('.exam-wrapper');
                    if (wrapper) {
                        wrapper.appendChild(result);
                        result.scrollIntoView({behavior:'smooth', block:'start'});
                    }
                }
            });
        });
    });

    updateProgress();
});
