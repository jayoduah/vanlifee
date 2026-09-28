/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    cream: '#fff7ed',
                    orange: '#ff8c38',
                    dark: '#161616',
                    gray: '#aaaaaa'
                },
                tag: {
                    simple: '#e17654',
                    rugged: '#115E59',
                    luxury: '#161616'
                }
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif'], // Standard modern font for Figma replicas
            }
        },
    },
    plugins: [],
}
