import { computed } from 'vue';

export interface SeoOptions {
    title: string;
    description: string;
    path?: string;
    image?: string;
    type?: string;
    noindex?: boolean;
}

const DEFAULT_SITE_URL = 'https://topgradelondonfc.co.uk';
const DEFAULT_OG_IMAGE = '/images/og/topgrade-london-fc.jpg';

export function useSeo(options: SeoOptions) {
    const siteUrl = DEFAULT_SITE_URL;

    const canonicalUrl = computed(() => {
        if (!options.path || options.path === '/' || options.path === '') {
            return siteUrl;
        }
        return `${siteUrl}${options.path.startsWith('/') ? options.path : `/${options.path}`}`;
    });

    const ogImageUrl = computed(() => {
        const img = options.image || DEFAULT_OG_IMAGE;
        if (img.startsWith('http://') || img.startsWith('https://')) {
            return img;
        }
        return `${siteUrl}${img.startsWith('/') ? img : `/${img}`}`;
    });

    const robotsContent = computed(() => {
        return options.noindex ? 'noindex, nofollow' : 'index, follow';
    });

    return {
        siteUrl,
        title: options.title,
        description: options.description,
        type: options.type || 'website',
        canonicalUrl,
        ogImageUrl,
        robotsContent,
        noindex: options.noindex ?? false,
    };
}
