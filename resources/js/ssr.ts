import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import ui from '@nuxt/ui/vue-plugin';
import { createHead, renderSSRHead } from '@unhead/vue/server';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createSSRApp, DefineComponent, h } from 'vue';
import { renderToString } from 'vue/server-renderer';
import PersistentLayout from './layouts/PersistentLayout.vue';

createServer(
    (page) => {
        const head = createHead();
        return createInertiaApp({
            page,
            render: renderToString,
            resolve: (name) => {
                const page = resolvePageComponent(
                    `./pages/${name}.vue`,
                    import.meta.glob<DefineComponent>('./pages/**/*.vue'),
                );
                page.then((module) => {
                    module.default.layout = PersistentLayout;
                });
                return page;
            },
            setup: ({ App, props, plugin }) =>
                createSSRApp({ render: () => h(App, props) })
                    .use(plugin)
                    .use(head)
                    .use(ui),
        }).then(async (app) => {
            const payload = await renderSSRHead(head);
            app.head.push(payload.headTags);
            return app;
        });
    },
    { cluster: true },
);
