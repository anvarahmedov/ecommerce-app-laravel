import defaultTheme from 'tailwindcss/defaultTheme';
//import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.tsx',
    ],

    theme: {
        extend: {
            fontFamily: {
                //merriweather: ['Merriweather', 'serif']
                trebuchet: ['"Trebuchet MS"', 'sans-serif']
                        },            colors: {
                purpure: '#F020D8',
                mozilla_gray: '#808080'
            }
        },
    },

   

    plugins: [require('daisyui')],
};
