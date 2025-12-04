
function showForm(formId) {
    const overlay = document.getElementById('form-overlay');
    const mainContent = document.getElementById('main-content');


    overlay.style.display = 'flex';
    mainContent.classList.add('blur');

    document.querySelectorAll('.form-box').forEach(form => form.classList.remove('active'));

    document.getElementById(formId).classList.add('active');
}

document.getElementById('form-overlay').addEventListener('click', function(e) {
    if (e.target === this) { 
        this.style.display = 'none';
        document.getElementById('main-content').classList.remove('blur');
    }
});


document.addEventListener('keydown', function(e) {
    if (e.key === "Escape") {
        const overlay = document.getElementById('form-overlay');
        overlay.style.display = 'none';
        document.getElementById('main-content').classList.remove('blur');
    }
});
