import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                primary: '#28242A',
                'primary-dark': '#1C1A1E',
                'primary-light': '#605C62',
                'background-dark': '#050505',
                'background-navy': '#0A090B',
                accent: '#3D3741',
                moonlight: '#D1D1D4',
            },
            fontFamily: {
                display: ['"Cinzel Decorative"', 'serif'],
                serif: ['"Playfair Display"', ...defaultTheme.fontFamily.serif],
                sans: ['Lato', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                'silver-glow': '0 0 15px rgba(96, 92, 98, 0.2)',
                'moonlight-glow': '0 0 25px rgba(96, 92, 98, 0.1)',
                'cold-shadow': '0 10px 30px -10px rgba(0, 0, 0, 0.9)',
            },
            backgroundImage: {
                'damask-pattern': "url('https://www.transparenttextures.com/patterns/black-scales.png')",
                'stone-texture': "url('https://www.transparenttextures.com/patterns/asfalt-dark.png')",
                'obsidian-texture': "linear-gradient(to bottom, rgba(5,5,5,0.95), rgba(10,9,11,0.98)), url('https://www.transparenttextures.com/patterns/dark-matter.png')",
                'cold-gradient': 'linear-gradient(to bottom, #050505, #0A090B)',
            },
        },
    },
    plugins: [],
};
