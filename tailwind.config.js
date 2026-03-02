import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
const colors = require('tailwindcss/colors');


/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream//*.blade.php',
        './storage/framework/views/*.php',
        './resources/views//*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                unahblue: '#003366',
                unahgold: '#FFCC00',
                red: colors.red,
            },
            backgroundImage: {
                'unah-gradient': 'linear-gradient(-45deg, #003366, #FFCC00, #b3d9ff, #ffffcc)',
            },
            animation: {
                'gradient-move': 'gradientMove 6s ease infinite',
            },
            keyframes: {
                gradientMove: {
                    '0%': { backgroundPosition: '0% 50%' },
                    '50%': { backgroundPosition: '100% 50%' },
                    '100%': { backgroundPosition: '0% 50%' },
                },
            }
        },
    },

    plugins: [forms, typography],
};