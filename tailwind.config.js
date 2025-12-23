import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // UMPSA Official Corporate Colors
                'umpsa': {
                    'primary': '#003A6C',      // Primary Blue
                    'secondary': '#0084C5',    // Secondary Teal
                    'accent': '#00AEEF',       // Accent Cyan
                    'dark-navy': '#002244',     // Dark Navy
                    'soft-gray': '#F4F7FC',     // Soft Gray
                    'neutral-gray': '#E6ECF2',  // Neutral Gray
                    'success': '#28A745',       // Success Green
                    'danger': '#DC3545',        // Danger Red
                    // Legacy colors (kept for compatibility)
                    'teal': '#009E9A',
                    'deep-blue': '#003B73',
                    'royal-blue': '#005AA7',
                    'yellow': '#F4D03F',
                    'white': '#FFFFFF',
                },
            },
        },
    },
    plugins: [],
};
