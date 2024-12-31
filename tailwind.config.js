/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
   './*.php',
   './js/*.js',
   
],
  theme: {
    extend: {},
  },
  plugins: [
   require('@tailwindcss/forms'),
   require('@tailwindcss/typography'),
  ],
}

