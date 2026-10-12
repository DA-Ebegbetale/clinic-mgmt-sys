<?php
declare(strict_types=1);

function render_icon(string $name): string
{
    return match ($name) {
        'dashboard' => '<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 13h6V4H4v9zm0 7h6v-5H4v5zm10 0h6V11h-6v9zm0-16v5h6V4h-6z" fill="currentColor"/></svg>',
        'users' => '<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zM8 11c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5C15 14.17 10.33 13 8 13zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.9 1.97 3.45V19h7v-2.5C24 14.17 19.33 13 16 13z" fill="currentColor"/></svg>',
        'archive' => '<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.54 5.23l-1.39-1.68A2 2 0 0 0 17.62 3H6.38a2 2 0 0 0-1.53.55L3.46 5.23A2 2 0 0 0 3 6.55V19a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.55a2 2 0 0 0-.46-1.32zM12 17l-4-4h3V9h2v4h3l-4 4z" fill="currentColor"/></svg>',
        'logs' => '<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v2H4V4zm0 14h10v2H4v-2zm0-7h16v2H4v-2zm12 0l4 4-4 4v-3H9v-2h7v-3z" fill="currentColor"/></svg>',
        'patients' => '<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"/></svg>',
        'consultations' => '<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v12H7l-3 3V4zm3 4h10v2H7V8zm0 4h7v2H7v-2z" fill="currentColor"/></svg>',
        'profile' => '<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"/></svg>',
        'logout' => '<svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17v-2h4v-2h-4v-2l-5 3 5 3zm8-13H8a2 2 0 0 0-2 2v3h2V6h10v12H8v-3H6v3a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z" fill="currentColor"/></svg>',
        default => '',
    };
}