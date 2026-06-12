import assert from 'node:assert/strict';
import { readdirSync, readFileSync, statSync } from 'node:fs';
import { relative } from 'node:path';
import test from 'node:test';

const resourcesJsPath = new URL('../../resources/js/', import.meta.url);
const passwordInputPath = new URL(
    '../../resources/js/components/PasswordInput.vue',
    import.meta.url,
);
const passwordInput = readFileSync(passwordInputPath, 'utf8');

function collectVueFiles(directory: URL): string[] {
    const files: string[] = [];

    for (const entry of readdirSync(directory)) {
        const entryPath = new URL(entry, directory);
        const absolutePath = entryPath.pathname;

        if (statSync(absolutePath).isDirectory()) {
            files.push(...collectVueFiles(new URL(`${entry}/`, directory)));

            continue;
        }

        if (absolutePath.endsWith('.vue')) {
            files.push(absolutePath);
        }
    }

    return files;
}

test('password reveal control remains accessible and non-submitting', () => {
    assert.match(passwordInput, /useAttrs/);
    assert.match(passwordInput, /:aria-controls="controlledInputId"/);
    assert.match(passwordInput, /:aria-pressed="showPassword"/);
    assert.match(passwordInput, /type="button"/);
    assert.doesNotMatch(passwordInput, /tabindex="-1"/);
    assert.doesNotMatch(passwordInput, /as HTMLElement/);
});

test('password fields use the shared password input wrapper', () => {
    for (const file of collectVueFiles(resourcesJsPath)) {
        if (file === passwordInputPath.pathname) {
            continue;
        }

        const source = readFileSync(file, 'utf8');

        assert.doesNotMatch(
            source,
            /type="password"/,
            relative(resourcesJsPath.pathname, file),
        );
    }
});
