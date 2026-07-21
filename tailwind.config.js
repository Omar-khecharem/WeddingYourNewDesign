/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './app/Views/**/*.php',
    './public_html/app/Views/**/*.php',
    './app/Components/**/*.php',
    './public_html/app/Components/**/*.php',
  ],
  theme: {
    extend: {
      fontFamily: {
        'montserrat': ['Montserrat', 'sans-serif'],
        'playfair': ['Playfair Display', 'serif'],
        'cormorant': ['Cormorant Garamond', 'serif'],
      },
      colors: {
        'premium': {
          'charcoal': '#1F2937',
          'navy': '#2C3E50',
          'burgundy': '#B8845A',
          'cabernet': '#9E6F46',
          'crimson': '#A67C56',
          'rose': '#D4A88C',
          'blush': '#F0EAE3',
          'champagne': '#D4AF37',
          'gold': '#C4956A',
          'ivory': '#F9F8F4',
          'cream': '#FFFFFF',
          'warm-gray': '#E5E2DA',
          'stone': '#D1CCC4',
          'taupe': '#9CA3AF',
          'mink': '#4B5563',
          'dark': '#1A1A2E',
          'emerald': '#10B981',
          'saffron': '#F59E0B',
          'royal': '#3B82F6',
        },
        'primary-red': '#B8845A',
        'accent-orange': '#9E6F46',
      },
      keyframes: {
        slideInRight: {
          '0%': { transform: 'translateX(100%)', opacity: '0' },
          '100%': { transform: 'translateX(0)', opacity: '1' },
        },
        slideInUp: {
          '0%': { transform: 'translateY(20px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        fadeInUp: {
          '0%': { opacity: '0', transform: 'translateY(20px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        shimmer: {
          '0%': { backgroundPosition: '-200% 0' },
          '100%': { backgroundPosition: '200% 0' },
        },
      },
      animation: {
        slideInRight: 'slideInRight 0.3s ease-out',
        slideInUp: 'slideInUp 0.3s ease-out',
        fadeIn: 'fadeIn 0.3s ease-out',
        fadeInUp: 'fadeInUp 0.5s ease-out',
        shimmer: 'shimmer 1.5s infinite',
      },
    },
  },
  plugins: [],
}
