const fallbackOrigin = 'http://localhost';

export function normalizedPath(url: string | undefined): string {
    if (!url) {
        return '';
    }

    try {
        const path = new URL(url, fallbackOrigin).pathname.replace(/\/+$/, '');

        return path || '/';
    } catch {
        return '';
    }
}

export function isCurrentUrl(
    currentUrl: string,
    targetUrl: string | undefined,
    exact = false,
): boolean {
    const currentPath = normalizedPath(currentUrl);
    const targetPath = normalizedPath(targetUrl);

    if (!currentPath || !targetPath) {
        return false;
    }

    if (exact || targetPath === '/') {
        return currentPath === targetPath;
    }

    return (
        currentPath === targetPath || currentPath.startsWith(`${targetPath}/`)
    );
}
