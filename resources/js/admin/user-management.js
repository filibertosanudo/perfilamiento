/**
 * user-management.js
 * Con Alpine x-show el modal ya está en el DOM desde el inicio,
 * así que usamos eventos de Alpine para las animaciones.
 */

// Animación modal: entrada
function animateModalIn() {
    const backdrop = document.querySelector('[role="dialog"] .fixed.inset-0');
    const panel    = document.querySelector('[role="dialog"] .relative.bg-white');

    if (backdrop) {
        backdrop.animate(
            [{ opacity: 0 }, { opacity: 1 }],
            { duration: 200, easing: 'ease-out', fill: 'forwards' }
        );
    }
    if (panel) {
        panel.animate(
            [
                { opacity: 0, transform: 'translateY(16px) scale(0.96)' },
                { opacity: 1, transform: 'translateY(0px)  scale(1)'   },
            ],
            { duration: 280, easing: 'cubic-bezier(0.34, 1.56, 0.64, 1)', fill: 'forwards' }
        );
    }
}

// Animación modal: salida
function animateModalOut() {
    const backdrop = document.querySelector('[role="dialog"] .fixed.inset-0');
    const panel    = document.querySelector('[role="dialog"] .relative.bg-white');

    if (backdrop) {
        backdrop.animate(
            [{ opacity: 1 }, { opacity: 0 }],
            { duration: 180, easing: 'ease-in', fill: 'forwards' }
        );
    }
    if (panel) {
        panel.animate(
            [
                { opacity: 1, transform: 'translateY(0px)  scale(1)'    },
                { opacity: 0, transform: 'translateY(10px) scale(0.97)' },
            ],
            { duration: 180, easing: 'ease-in', fill: 'forwards' }
        );
    }
}

// Filas de tabla: entrada escalonada
function animateTableRows() {
    document.querySelectorAll('tbody tr').forEach((row, i) => {
        row.animate(
            [
                { opacity: 0, transform: 'translateY(8px)' },
                { opacity: 1, transform: 'translateY(0)'   },
            ],
            { duration: 220, delay: i * 35, easing: 'ease-out', fill: 'forwards' }
        );
    });
}

// Flash message: entrada + auto-dismiss
function initFlashMessage() {
    const flash = document.querySelector('[data-flash]');
    if (!flash) return;

    flash.animate(
        [{ opacity: 0, transform: 'translateY(-6px)' }, { opacity: 1, transform: 'translateY(0)' }],
        { duration: 300, easing: 'ease-out', fill: 'forwards' }
    );
    setTimeout(() => {
        const anim = flash.animate(
            [{ opacity: 1 }, { opacity: 0 }],
            { duration: 300, easing: 'ease-in', fill: 'forwards' }
        );
        anim.onfinish = () => flash.remove();
    }, 4000);
}

//  Integración Livewire 3 
document.addEventListener('livewire:init', () => {

    // Escucha los eventos que dispatcha el componente
    Livewire.on('modal-opened', () => {
        requestAnimationFrame(animateModalIn);
    });

    Livewire.on('modal-closed', () => {
        animateModalOut();
    });

    // Reanimar filas tras cada actualización (búsqueda, paginación, CRUD)
    Livewire.hook('commit', ({ succeed }) => {
        succeed(() => requestAnimationFrame(() => {
            animateTableRows();
            initFlashMessage();
        }));
    });

});

// Init 
document.addEventListener('DOMContentLoaded', () => {
    animateTableRows();
    initFlashMessage();
});