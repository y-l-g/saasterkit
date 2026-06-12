import prettier from 'eslint-config-prettier/flat';
import vue from 'eslint-plugin-vue';

import {
    defineConfigWithVueTs,
    vueTsConfigs,
} from '@vue/eslint-config-typescript';

export default defineConfigWithVueTs(
    vue.configs['flat/essential'],
    vueTsConfigs.recommended,
    {
        ignores: [
            'vendor',
            'node_modules',
            'public',
            'tests/Frontend',
            'tests/Frontend/**',
            'bootstrap/ssr',
            'tailwind.config.js',
            'resources/js/components/ui/*',
        ],
    },
    {
        rules: {
            'vue/multi-word-component-names': 'off',
            '@typescript-eslint/no-explicit-any': 'off',
            'no-restricted-imports': [
                'error',
                {
                    patterns: ['@nuxt/ui/runtime/*'],
                },
            ],
        },
    },
    prettier,
);
