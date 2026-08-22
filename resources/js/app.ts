// import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';

createInertiaApp({
    title: (title) => {
        return title ? `${title} | Tech Store` : 'Tech Store';
    },
});
