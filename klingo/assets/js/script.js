// Basic JS functions

document.addEventListener('DOMContentLoaded', function () {
    const lessons = document.querySelectorAll('.lesson-item');

    lessons.forEach(item => {
        item.addEventListener('click', () => {
            const lessonId = item.getAttribute('data-lesson-id');
            window.location.href = `learn.php?lesson_id=${lessonId}`;
        });
    });
});

// Quiz Submit
function submitQuiz(lessonId) {
    const answers = {}; 
    document.querySelectorAll('input[type="text"]').forEach(input => {
        answers[input.name] = input.value.trim();
    });

    fetch('../api/submit_quiz.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `lesson_id=${lessonId}&score=100`
    }).then(response => response.text())
    .then(data => {
        alert('Quiz Submitted Successfully!');
        window.location.href = 'dashboard.php';
    }).catch(error => {
        console.error('Error:', error);
    });
}
