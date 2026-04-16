<?php
/**
 * Theme Configuration
 *
 * Central configuration for all available themes.
 * Used by themepicker.php and theme_loader.php
 *
 * Each theme requires:
 * - id: Unique identifier (integer)
 * - name: Display name
 * - icon: Font Awesome icon class
 * - class: CSS class and data-bs-theme value (e.g., 'light', 'dark', 'modern')
 * - useParticles: Boolean (true to use particles.js animation)
 * - useTailAnimation: Boolean (true to use tail canvas animation)
 * 
 * Configuration:
 * - theme_default: Default theme ID
 * - cookie: Single cookie name for theme and animation preferences
 */

// Define themes array
$config['themes'] = [
    // Light theme - uses particles animation
    ['id' => 1, 'name' => 'Light', 'icon' => 'fa-sun-o', 'class' => 'light', 'useParticles' => true],

    // Dark theme - uses tail animation (or particles fallback)
    ['id' => 2, 'name' => 'Dark', 'icon' => 'fa-moon-o', 'class' => 'dark', 'useParticles' => true, 'useTailAnimation' => true],

    // Modern theme - plain theme without animations (modern minimalist design)
    ['id' => 3, 'name' => 'Modern', 'icon' => 'fa-laptop', 'class' => 'modern'],

    // Plain theme - ocean
    ['id' => 4, 'name' => 'Plain', 'icon' => 'fa-tree', 'class' => 'ocean'],

    // Bootstrap Bootswatch themes
    ['id' => 5, 'name' => 'Brite', 'icon' => 'fa-lightbulb-o', 'class' => 'brite', 'cssFile' => 'brite.css'],
    ['id' => 6, 'name' => 'Cerulean', 'icon' => 'fa-cloud', 'class' => 'cerulean', 'cssFile' => 'cerulean.css'],
    // ['id' => 7, 'name' => 'Cyborg', 'icon' => 'fa-microchip', 'class' => 'cyborg', 'cssFile' => 'cyborg.css'],
    // ['id' => 8, 'name' => 'Darkly', 'icon' => 'fa-moon-o', 'class' => 'darkly', 'cssFile' => 'darkly.css'],
    ['id' => 9, 'name' => 'Litera', 'icon' => 'fa-font', 'class' => 'litera', 'cssFile' => 'litera.css'],
    ['id' => 10, 'name' => 'Lumen', 'icon' => 'fa-lightbulb-o', 'class' => 'lumen', 'cssFile' => 'lumen.css'],
    ['id' => 11, 'name' => 'Lux', 'icon' => 'fa-star', 'class' => 'lux', 'cssFile' => 'lux.css'],
    ['id' => 12, 'name' => 'Materia', 'icon' => 'fa-cube', 'class' => 'materia', 'cssFile' => 'materia.css'],
    ['id' => 13, 'name' => 'Minty', 'icon' => 'fa-leaf', 'class' => 'minty', 'cssFile' => 'minty.css'],
    ['id' => 14, 'name' => 'Morph', 'icon' => 'fa-magic', 'class' => 'morph', 'cssFile' => 'morph.css'],
    ['id' => 15, 'name' => 'Quartz', 'icon' => 'fa-diamond', 'class' => 'quartz', 'cssFile' => 'quartz.css'],
    ['id' => 16, 'name' => 'Simplex', 'icon' => 'fa-check', 'class' => 'simplex', 'cssFile' => 'simplex.css'],
    ['id' => 17, 'name' => 'Sketchy', 'icon' => 'fa-pencil', 'class' => 'sketchy', 'cssFile' => 'sketchy.css'],
    // ['id' => 18, 'name' => 'Slate', 'icon' => 'fa-tablet', 'class' => 'slate', 'cssFile' => 'slate.css'],
    ['id' => 19, 'name' => 'Spacelab', 'icon' => 'fa-flask', 'class' => 'spacelab', 'cssFile' => 'spacelab.css'],
];

// Default theme ID
$config['theme_default'] = 1; // Light mode

// Cookie configuration - single cookie for theme and animation preferences
$config['cookie'] = 'my-theme';
