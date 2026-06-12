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

test('target blank links use safe rel values', () => {
    for (const file of collectSourceFiles(resourcesJsPath)) {
        const lines = readFileSync(file, 'utf8').split('\n');

        lines.forEach((line, index) => {
            if (
                !line.includes('target="_blank"') &&
                !/target:\s*['"]_blank['"]/.test(line)
            ) {
                return;
            }

            const context = lines
                .slice(Math.max(0, index - 6), index + 7)
                .join('\n');

            assert.match(
                context,
                /rel(?:=|:)\s*['"][^'"]*\bnoopener\b[^'"]*\bnoreferrer\b[^'"]*['"]/,
                `${relative(resourcesJsPath.pathname, file)}:${index + 1}`,
            );
        });
    }
});
