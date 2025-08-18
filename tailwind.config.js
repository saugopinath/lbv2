module.exports = {
    purge: [],
    darkMode: false, // or 'media' or 'class'
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
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
        extend: {},
    },
    variants: {
        extend: {},
    },
    plugins: [],
};
