

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-rich-text]').forEach((container) => {
        const editor = container.querySelector('[data-rich-text-editor]');
        const source = container.querySelector('[data-rich-text-source]');
        const toolbar = container.querySelector('[data-rich-text-toolbar]');

        if (!editor || !source || !toolbar) {
            return;
        }

        editor.hidden = false;
        source.hidden = true;
        source.required = false;
        toolbar.hidden = false;

        const syncSource = () => {
            source.value = editor.innerHTML;
        };

        editor.addEventListener('input', syncSource);
        toolbar.addEventListener('mousedown', (event) => {
            if (event.target.closest('button')) {
                event.preventDefault();
            }
        });
        toolbar.addEventListener('click', (event) => {
            const button = event.target.closest('button[data-command]');
            if (!button) {
                return;
            }

            editor.focus();
            document.execCommand(button.dataset.command, false, button.dataset.value || null);
            syncSource();
        });
        container.closest('form').addEventListener('submit', (event) => {
            syncSource();

            if (editor.innerText.trim() === '') {
                event.preventDefault();
                editor.focus();
                editor.setAttribute('aria-invalid', 'true');
            } else {
                editor.removeAttribute('aria-invalid');
            }
        });
    });
});
