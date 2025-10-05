// const defaultTheme = require('tailwindcss/defaultTheme')
import colors from "tailwindcss/colors";

/** @type {import('tailwindcss').Config} */
export default {
  important: true,
  darkMode: ["selector", "body.body--dark"],
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx,vue}",

    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        // primary: {
        //   50:  '#1976d214',
        //   100: '#BBDEFB',
        //   200: '#90CAF9',
        //   300: '#64B5F6',
        //   400: '#42A5F5',
        //   500: '#2196F3',
        //   600: '#1E88E5',
        //   700: '#1976D2', //main color
        //   800: '#1565C0',
        //   900: '#0D47A1',
        // },
        danger: colors.red[500],
      },
    },
  },
  plugins: [],
};