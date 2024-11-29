const modal = document.getElementById('modal');
const overlay = document.getElementById('overlay');
const openModal = document.getElementById('openModal');
const closeModal = document.getElementById('closeModal');

openModal.addEventListener('click', () => {
    modal.style.display = 'block';
    overlay.style.display = 'block';
});

closeModal.addEventListener('click', () => {
    modal.style.display = 'none';
    overlay.style.display = 'none';
});

overlay.addEventListener('click', () => {
    modal.style.display = 'none';
    overlay.style.display = 'none';
});
