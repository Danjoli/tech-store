import '../css/app.css';
import './bootstrap';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h, type DefineComponent } from 'vue';

const pages = import.meta.glob('./Pages/**/*.vue') as Record<
    string,
    () => Promise<DefineComponent>
>;

createInertiaApp({
    title: (title) => title ? `${title} | Tech Store` : 'Tech Store',
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, pages),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#6eaef6',
    },
});
