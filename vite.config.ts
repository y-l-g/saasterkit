import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import ui from '@nuxt/ui/vite';
import tailwindcss from '@tailwindcss/vite';
import { Unhead as UnheadVite } from '@unhead/bundler/vite';
import { unheadVueComposablesImports } from '@unhead/vue';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import Icons from 'unplugin-icons/vite';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        inertia(),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        ui({
            router: 'inertia',
            autoImport: {
                imports: [unheadVueComposablesImports],
            },
            ui: {
                breadcrumb: {
                    variants: {
                        active: {
                            true: {
                                link: 'text-toned font-normal',
                            },
                            false: {
                                link: 'text-toned font-normal',
                            },
                        },
                    },
                },
                dashboardSidebarCollapse: {
                    base: 'hidden lg:flex text-toned',
                },
                dashboardSidebarToggle: {
                    base: 'lg:hidden text-toned',
                },
            },
        }),
        Icons({
            compiler: 'vue3',
            autoInstall: true,
        }),
        UnheadVite(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
            clientPort: 5173,
        },
        watch: {
            ignored: ['**/vendor/**', '**/storage/**'],
        },
    },
    ssr: {
        noExternal: ['@nuxt/ui'],
    },
});
