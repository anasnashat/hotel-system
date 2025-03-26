import '../css/app.css';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { createPinia } from 'pinia';
import { initializeTheme } from './composables/useAppearance';





// Extend ImportMeta interface for Vite...
declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string;

        [key: string]: string | boolean | undefined;
    }

    interface ImportMeta {
        readonly env: ImportMetaEnv;
        readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
    }
}

// import { useFavoriteStore } from './stores/favorite';


const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        const pinia = createPinia();  // ✅ Create Pinia instance
        app.use(plugin)
            .use(pinia)
            .use(router)
           .use(ZiggyVue)
           .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// Set light/dark mode on page load
initializeTheme();
