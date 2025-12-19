import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/react";
import { createRoot } from "react-dom/client";
import { ThemeProvider } from "./providers/theme-provider";
import { Ziggy } from "./ziggy.js";

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.jsx", { eager: true });

        // Try exact match first (case-sensitive)
        let pagePath = `./Pages/${name}.jsx`;

        if (!pages[pagePath]) {
            // Try with first segment capitalized
            const normalizedName = name
                .split("/")
                .map((part, index) =>
                    index === 0
                        ? part.charAt(0).toUpperCase() + part.slice(1)
                        : part
                )
                .join("/");

            pagePath = `./Pages/${normalizedName}.jsx`;
        }

        if (!pages[pagePath]) {
            console.error("Available pages:", Object.keys(pages));
            console.error("Looking for:", pagePath);
            console.error("Original name:", name);
            throw new Error(`Inertia page not found: ${name}`);
        }

        return pages[pagePath];
    },

    setup({ el, App, props }) {
        // Ziggy injected (tidak wajib tapi tetap aman)
        if (props.initialPage.props?.ziggy) {
            props.initialPage.props.ziggy = {
                ...Ziggy,
                location: new URL(Ziggy.url).href,
            };
        }

        createRoot(el).render(
            <ThemeProvider defaultTheme="light">
                <App {...props} />
            </ThemeProvider>
        );
    },
});
