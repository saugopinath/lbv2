// Tailwind CSS v4 configuration
// While v4 primarily uses CSS-based config, this file can still be used for content scanning and safelisting

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    safelist: [
        // Background colors
        ...[
            "pink",
            "indigo",
            "green",
            "orange",
            "violet",
            "lime",
            "sky",
            "amber",
            "fuchsia",
            "rose",
            "emerald",
            "blue",
            "teal",
        ].flatMap((color) =>
            [
                "50",
                "100",
                "200",
                "300",
                "400",
                "500",
                "600",
                "700",
                "800",
                "900",
            ].flatMap((shade) => [
                `bg-${color}-${shade}`,
                `text-${color}-${shade}`,
                `border-${color}-${shade}`,
                `from-${color}-${shade}`,
                `to-${color}-${shade}`,
                `via-${color}-${shade}`,
                `hover:bg-${color}-${shade}`,
                `hover:text-${color}-${shade}`,
                `hover:border-${color}-${shade}`,
            ]),
        ),
    ],
};
