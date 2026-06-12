import assert from 'node:assert/strict';
import { readdirSync, readFileSync, statSync } from 'node:fs';
import { relative } from 'node:path';
import test from 'node:test';

const resourcesJsPath = new URL('../../resources/js/', import.meta.url);

function collectSourceFiles(directory: URL): string[] {
    const files: string[] = [];

    for (const entry of readdirSync(directory)) {
        const entryPath = new URL(entry, directory);
        const absolutePath = entryPath.pathname;

        if (statSync(absolutePath).isDirectory()) {
            files.push(...collectSourceFiles(new URL(`${entry}/`, directory)));

            continue;
        }

        if (/\.(ts|vue)$/.test(absolutePath)) {
            files.push(absolutePath);
        }
    }

    return files;
}

test('source files do not import Nuxt UI runtime internals', () => {
    for (const file of collectSourceFiles(resourcesJsPath)) {
        const source = readFileSync(file, 'utf8');

        assert.doesNotMatch(
            source,
            /@nuxt\/ui\/runtime\//,
            relative(resourcesJsPath.pathname, file),
        );
    }
});

test('app toast wrapper imports the public Nuxt UI composables entry', () => {
    const wrapper = readFileSync(
        new URL(
            '../../resources/js/composables/useAppToast.ts',
            import.meta.url,
        ),
        'utf8',
    );

    assert.match(wrapper, /from '@nuxt\/ui\/composables'/);
});
