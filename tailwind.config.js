import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                // Tambahkan pk-primary agar cocok dengan kelas bg-pk-primary di app.css
                'pk-primary': '#4f7c52',
                'pk-primary-dark': '#3c6340',

                'pk-dark': '#1e362b',
                'pk-green': '#4f7c52',
                'pk-green-dark': '#3c6340',
                'pk-cream': '#eef0e5',
                'pk-orange': '#d99a45',
                'pk-red': '#c1502e',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                serif: ['"Lora"', ...defaultTheme.fontFamily.serif],
            },
        },
    },
    plugins: [forms],
};