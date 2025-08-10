/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './src/**/*.{html,js,jsx,ts,tsx}',
        '../../../templates/themes/react/**/*.phtml'
        ],
        theme: {
            extend: {
                colors: {
                    forest: {
                        dark: '#1a2e05',
                        deep: '#2d5016',
                        main: '#4a7c59',
                        sage: '#87a96b',
                        light: '#a8c686',
                        mist: '#c8d5b9',
                    },
                    earth: {
                        bark: '#8b4513',
                        soil: '#654321',
                        stone: '#696969',
                        sand: '#c2b280',
                    },
                    nature: {
                        cream: '#f5f5dc',
                        moss: '#8fbc8f',
                        fern: '#4f7942',
                        pine: '#355e3b',
                    }
                },
                animation: {
                    'breathe': 'breathe 4s ease-in-out infinite',
                    'float-gentle': 'floatGentle 6s ease-in-out infinite',
                    'sway': 'sway 8s ease-in-out infinite',
                    'fade-in': 'fadeIn 2s ease-in',
                    'leaf-fall': 'leafFall 15s linear infinite',
                    'wind-rustle': 'windRustle 3s ease-in-out infinite',
                },
                keyframes: {
                    breathe: {
                        '0%, 100%': {
                            transform: 'scale(1)',
                            opacity: '0.8'
                        },
                        '50%': {
                            transform: 'scale(1.02)',
                            opacity: '1'
                        }
                    },
                    floatGentle: {
                        '0%, 100%': {
                            transform: 'translateY(0px) rotate(0deg)',
                        },
                        '50%': {
                            transform: 'translateY(-10px) rotate(2deg)',
                        }
                    },
                    sway: {
                        '0%, 100%': {
                            transform: 'translateX(0px) rotate(0deg)',
                        },
                        '25%': {
                            transform: 'translateX(5px) rotate(1deg)',
                        },
                        '75%': {
                            transform: 'translateX(-5px) rotate(-1deg)',
                        }
                    },
                    fadeIn: {
                        '0%': {
                            opacity: '0',
                            transform: 'translateY(20px)'
                        },
                        '100%': {
                            opacity: '1',
                            transform: 'translateY(0)'
                        }
                    },
                    leafFall: {
                        '0%': {
                            transform: 'translateY(-100vh) rotate(0deg)',
                            opacity: '0'
                        },
                        '10%': {
                            opacity: '1'
                        },
                        '90%': {
                            opacity: '1'
                        },
                        '100%': {
                            transform: 'translateY(100vh) rotate(360deg)',
                            opacity: '0'
                        }
                    },
                    windRustle: {
                        '0%, 100%': {
                            transform: 'translateX(0px)',
                        },
                        '25%': {
                            transform: 'translateX(2px)',
                        },
                        '75%': {
                            transform: 'translateX(-2px)',
                        }
                    }
                },
                fontFamily: {
                    'nature': ['Georgia', 'serif'],
                    'calm': ['Lora', 'serif'],
                },
                backdropBlur: {
                    xs: '2px',
                },
                boxShadow: {
                    'soft': '0 4px 20px rgba(0, 0, 0, 0.1)',
                    'nature': '0 8px 32px rgba(77, 124, 89, 0.2)',
                    'earth': '0 6px 24px rgba(139, 69, 19, 0.15)',
                }
            },
    },
    plugins: [],
    }
