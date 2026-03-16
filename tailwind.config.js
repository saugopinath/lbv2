import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/**/*.js",
        "./vendor/masmerise/livewire-toaster/resources/views/*.blade.php",
    ],

    darkMode: "class",

    safelist: [
        "bg-pink-500",
        "bg-indigo-500",
        "bg-green-500",
        "bg-orange-500",
        "border-pink-500",
        "border-indigo-500",
        "border-green-500",
        "border-orange-500",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
