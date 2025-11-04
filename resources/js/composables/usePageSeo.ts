import { useHead } from '@unhead/vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const appUrl = import.meta.env.VITE_APP_URL || '';

interface PageMeta {
    title?: string;
    description?: string;
    url?: string;
    // image?: string;
}

export function usePageSeo(meta: PageMeta) {
    const title = meta.title ? `${meta.title} - ${appName}` : appName;
    const description =
        meta.description || 'The default description for your SaaS.';
    const url = meta.url ? `${appUrl}${meta.url}` : appUrl;
    // const image = meta.image || `${appUrl}/images/default-social-image.png`;

    useHead({
        title: title,
        meta: [
            { name: 'description', content: description },
            { property: 'og:title', content: title },
            { property: 'og:description', content: description },
            { property: 'og:url', content: url },
            // { property: 'og:image', content: image },
            { property: 'og:type', content: 'website' },
            { property: 'og:site_name', content: appName },
            // { name: 'twitter:card', content: 'summary_large_image' },
            { name: 'twitter:title', content: title },
            { name: 'twitter:description', content: description },
            // { name: 'twitter:image', content: image },
        ],
        link: [{ rel: 'canonical', href: url }],
    });
}
