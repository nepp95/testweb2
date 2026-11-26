// Vraag om bevestiging bij formulieren met data-confirm (bijvoorbeeld verwijderen).
document.addEventListener('submit', (event) => {
    const message = event.target.dataset.confirm;

    if (message && !window.confirm(message)) {
        event.preventDefault();
    }
});
