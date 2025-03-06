/** @type {import('tailwindcss').Config} */
const forms = require('@tailwindcss/forms')

module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./storage/framework/views/*.php",
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php"
  ],
  theme: {
    extend: {
      colors: {
        'napps': {
          primary: '#1E3A8A',    // Deep Blue
          accent: '#FFD700',     // Gold
          bg: '#F8F9FA',         // Light Gray
          text: '#333333',       // Dark Gray
          success: '#10B981',    // Green
          warning: '#F59E0B',    // Amber
          danger: '#EF4444',     // Red
          info: '#3B82F6',       // Blue
        }
      }
    },
  },
  plugins: [
    forms
  ],
}
