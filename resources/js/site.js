// Mobiel menu
const toggle = document.querySelector('.nav-toggle');
const nav = document.getElementById('site-nav');

if (toggle && nav) {
    const setOpen = (open) => {
        toggle.setAttribute('aria-expanded', String(open));
        nav.classList.toggle('is-open', open);
    };

    toggle.addEventListener('click', () => {
        setOpen(toggle.getAttribute('aria-expanded') !== 'true');
    });

    nav.addEventListener('click', (event) => {
        if (event.target.closest('a')) {
            setOpen(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && nav.classList.contains('is-open')) {
            setOpen(false);
            toggle.focus();
        }
    });
}

// Knoppen die een onderwerp vooraf kiezen in het formulier
const subject = document.getElementById('f-onderwerp');

document.querySelectorAll('[data-onderwerp]').forEach((link) => {
    link.addEventListener('click', () => {
        const option = subject && [...subject.options].find((candidate) => candidate.value === link.dataset.onderwerp);

        if (option) {
            subject.value = option.value;
        }
    });
});

// Contactformulier: dezelfde controles als op de server, zodat je meteen ziet wat er mist.
// De server blijft leidend; zonder JavaScript werkt het formulier gewoon.
const form = document.getElementById('contact-form');

if (form) {
    const checks = [
        { input: form.elements.naam, valid: (input) => input.value.trim() !== '' },
        { input: form.elements.contact, valid: (input) => input.value.trim().length >= 6 },
        { input: form.elements.privacy, valid: (input) => input.checked },
    ];

    const showError = (check, show) => {
        const message = document.getElementById(check.input.dataset.error);

        message.hidden = !show;
        check.input.setAttribute('aria-invalid', String(show));

        if (show) {
            check.input.setAttribute('aria-describedby', message.id);
        } else {
            check.input.removeAttribute('aria-describedby');
        }
    };

    checks.forEach((check) => {
        const eventName = check.input.type === 'checkbox' ? 'change' : 'input';

        check.input.addEventListener(eventName, () => {
            if (check.input.getAttribute('aria-invalid') === 'true' && check.valid(check.input)) {
                showError(check, false);
            }
        });
    });

    form.addEventListener('submit', (event) => {
        let firstInvalid = null;

        checks.forEach((check) => {
            const isValid = check.valid(check.input);

            showError(check, !isValid);

            if (!isValid && !firstInvalid) {
                firstInvalid = check.input;
            }
        });

        if (firstInvalid) {
            event.preventDefault();
            firstInvalid.focus();
        }
    });
}
