import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

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
    let page: any = null;
    try {
        page = usePage();
    } catch {
        // outside Inertia component context
    }

    const siteUrl = computed(() => page?.props?.siteSettings?.site_url || DEFAULT_SITE_URL);
    const siteName = computed(() => page?.props?.siteSettings?.site_name || 'TopGrade London FC');

    const canonicalUrl = computed(() => {
        const base = siteUrl.value;
        if (!options.path || options.path === '/' || options.path === '') {
            return base;
        }
        return `${base}${options.path.startsWith('/') ? options.path : `/${options.path}`}`;
    });

    const ogImageUrl = computed(() => {
        const defaultImg = page?.props?.siteSettings?.default_og_image || DEFAULT_OG_IMAGE;
        const img = options.image || defaultImg;
        if (img.startsWith('http://') || img.startsWith('https://')) {
            return img;
        }
        return `${siteUrl.value}${img.startsWith('/') ? img : `/${img}`}`;
    });

    const robotsContent = computed(() => {
        return options.noindex ? 'noindex, nofollow' : 'index, follow';
    });

    return {
        siteUrl,
        siteName,
        title: options.title,
        description: options.description,
        type: options.type || 'website',
        canonicalUrl,
        ogImageUrl,
        robotsContent,
        noindex: options.noindex ?? false,
    };
}
