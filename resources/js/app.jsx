import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/react";
import { createRoot } from "react-dom/client";
import { ThemeProvider } from "./providers/theme-provider";
import { Ziggy } from "./ziggy.js";

createInertiaApp({
    resolve: (name) => {
        // Load semua file page di dua folder:
        const pages = import.meta.glob(
            [
                "./pages/**/*.jsx",      // folder default
                "./pages/app/**/*.jsx",  // folder app yang kamu pakai
            ],
            { eager: true }
        );

        // Pencarian dengan prioritas folder `app/`
        return (
            pages[`./pages/app/${name}.jsx`] ??
            pages[`./pages/${name}.jsx`] ??
            null
        );
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
