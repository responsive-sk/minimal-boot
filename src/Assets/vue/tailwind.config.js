/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './src/**/*.{html,js,vue,ts}',
    '../../../templates/themes/vue/**/*.phtml'
  ],
  theme: {
    extend: {
      colors: {
        cyber: {
          pink: '#ff0080',
          cyan: '#00ffff',
          purple: '#8000ff',
          green: '#00ff41',
          yellow: '#ffff00',
          dark: '#0a0a0a',
          darker: '#050505',
        },
        neon: {
          blue: '#00d4ff',
          pink: '#ff006e',
          green: '#39ff14',
          purple: '#bf00ff',
          orange: '#ff8c00',
        }
      },
      animation: {
        'glow-pulse': 'glowPulse 2s ease-in-out infinite alternate',
        'matrix-rain': 'matrixRain 20s linear infinite',
        'cyber-flicker': 'cyberFlicker 0.15s infinite linear alternate',
        'neon-glow': 'neonGlow 1.5s ease-in-out infinite alternate',
        'hologram': 'hologram 3s ease-in-out infinite',
        'glitch': 'glitch 0.3s ease-in-out infinite',
        'typing': 'typing 3.5s steps(40, end), blink-caret 0.75s step-end infinite',
      },
      keyframes: {
        glowPulse: {
          '0%': { 
            boxShadow: '0 0 5px currentColor, 0 0 10px currentColor, 0 0 15px currentColor',
            textShadow: '0 0 5px currentColor'
          },
          '100%': { 
            boxShadow: '0 0 10px currentColor, 0 0 20px currentColor, 0 0 30px currentColor',
            textShadow: '0 0 10px currentColor'
          }
        },
        matrixRain: {
          '0%': { transform: 'translateY(-100vh)' },
          '100%': { transform: 'translateY(100vh)' }
        },
        cyberFlicker: {
          '0%, 100%': { opacity: '1' },
          '50%': { opacity: '0.8' }
        },
        neonGlow: {
          '0%': { 
            textShadow: '0 0 5px currentColor, 0 0 10px currentColor, 0 0 15px currentColor, 0 0 20px currentColor'
          },
          '100%': { 
            textShadow: '0 0 10px currentColor, 0 0 20px currentColor, 0 0 30px currentColor, 0 0 40px currentColor'
          }
        },
        hologram: {
          '0%, 100%': { 
            transform: 'translateY(0px)',
            filter: 'hue-rotate(0deg)'
          },
          '50%': { 
            transform: 'translateY(-5px)',
            filter: 'hue-rotate(90deg)'
          }
        },
        glitch: {
          '0%': { transform: 'translate(0)' },
          '20%': { transform: 'translate(-2px, 2px)' },
          '40%': { transform: 'translate(-2px, -2px)' },
          '60%': { transform: 'translate(2px, 2px)' },
          '80%': { transform: 'translate(2px, -2px)' },
          '100%': { transform: 'translate(0)' }
        },
        typing: {
          'from': { width: '0' },
          'to': { width: '100%' }
        },
        'blink-caret': {
          'from, to': { borderColor: 'transparent' },
          '50%': { borderColor: '#00ffff' }
        }
      },
      fontFamily: {
        'cyber': ['Orbitron', 'monospace'],
        'matrix': ['Courier New', 'monospace'],
      },
      backdropBlur: {
        xs: '2px',
      }
    },
  },
  plugins: [],
}
