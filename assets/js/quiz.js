document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.quiz-options').forEach((quiz) => {
        const correct = quiz.dataset.correct;
        const buttons = quiz.querySelectorAll('.quiz-option');

        buttons.forEach((button) => {
            button.addEventListener('click', () => {
                if (quiz.dataset.answered === '1') return;

                quiz.dataset.answered = '1';
                const selected = button.dataset.key;
                const isCorrect = selected === correct;

                buttons.forEach((item) => {
                    item.disabled = true;
                    if (item.dataset.key === correct) {
                        item.classList.add('correct');
                    }
                });

                if (!isCorrect) {
                    button.classList.add('wrong');
                }

                const feedback = document.createElement('div');
                feedback.className = 'quiz-feedback ' + (isCorrect ? 'correct' : 'wrong');
                feedback.textContent = isCorrect ? '✓ Richtig!' : '✗ Falsch – die richtige Antwort ist markiert.';
                quiz.appendChild(feedback);
            });
        });
    });
});
