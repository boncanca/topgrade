/**
 * Block type schema registry.
 *
 * Each block type defines:
 *  - label: human-readable name shown in the picker and block header
 *  - icon:  emoji/text icon shown in the block type picker
 *  - payloadFields: fields stored in `payload` (the primary content)
 *  - settingsFields: fields stored in `settings` (presentation/layout options)
 *
 * Field types:
 *  - text       → <input type="text">
 *  - textarea   → <textarea> (plain text / HTML)
 *  - richtext   → Tiptap WYSIWYG editor
 *  - url        → <input type="url">
 *  - select     → <select> with options[]
 *  - toggle     → <input type="checkbox">
 *  - image      → Spatie media upload + URL fallback
 *  - repeater   → array of sub-fields (for feature_list items)
 */

export interface BlockFieldDefinition {
    key: string;
    label: string;
    type: 'text' | 'textarea' | 'richtext' | 'url' | 'select' | 'toggle' | 'image' | 'repeater';
    required?: boolean;
    placeholder?: string;
    options?: Array<{ value: string; label: string }>;
    /** Sub-fields for repeater type */
    subFields?: Array<Omit<BlockFieldDefinition, 'subFields'>>;
    /** Default value when a new block of this type is created */
    default?: unknown;
}

export interface BlockTypeSchema {
    label: string;
    icon: string;
    description: string;
    payloadFields: BlockFieldDefinition[];
    settingsFields?: BlockFieldDefinition[];
    /** Default payload values when adding a new block */
    defaultPayload: Record<string, unknown>;
    /** Default settings values when adding a new block */
    defaultSettings?: Record<string, unknown>;
}

export const blockSchemas: Record<string, BlockTypeSchema> = {
    hero: {
        label: 'Hero',
        icon: '🦸',
        description: 'Full-width hero section with title, subtitle, and optional CTA button.',
        payloadFields: [
            { key: 'title', label: 'Title', type: 'text', required: true, placeholder: 'Enter headline…' },
            { key: 'subtitle', label: 'Subtitle', type: 'textarea', placeholder: 'Supporting text below the headline…' },
            { key: 'button_text', label: 'Button Text', type: 'text', placeholder: 'Book a Trial' },
            { key: 'button_url', label: 'Button URL', type: 'url', placeholder: '/bookings' },
        ],
        settingsFields: [
            {
                key: 'theme',
                label: 'Theme',
                type: 'select',
                options: [
                    { value: 'light', label: 'Light' },
                    { value: 'dark', label: 'Dark' },
                    { value: 'glass', label: 'Glass' },
                ],
                default: 'light',
            },
            {
                key: 'align',
                label: 'Text Alignment',
                type: 'select',
                options: [
                    { value: 'left', label: 'Left' },
                    { value: 'center', label: 'Centre' },
                    { value: 'right', label: 'Right' },
                ],
                default: 'center',
            },
        ],
        defaultPayload: { title: '', subtitle: '', button_text: '', button_url: '' },
        defaultSettings: { theme: 'light', align: 'center' },
    },

    rich_text: {
        label: 'Rich Text',
        icon: '📝',
        description: 'WYSIWYG rich text content block.',
        payloadFields: [
            { key: 'body', label: 'Content', type: 'richtext', required: true },
        ],
        defaultPayload: { body: '' },
    },

    cta: {
        label: 'Call to Action',
        icon: '📣',
        description: 'Highlighted section with a title, description and CTA button.',
        payloadFields: [
            { key: 'title', label: 'Title', type: 'text', required: true, placeholder: 'Ready to start?' },
            { key: 'description', label: 'Description', type: 'textarea', placeholder: 'Brief supporting text…' },
            { key: 'button_text', label: 'Button Text', type: 'text', placeholder: 'Get Started' },
            { key: 'button_url', label: 'Button URL', type: 'url', placeholder: '/contact' },
        ],
        defaultPayload: { title: '', description: '', button_text: '', button_url: '' },
    },

    image: {
        label: 'Image',
        icon: '🖼️',
        description: 'Single image with optional caption. Supports file upload or external URL.',
        payloadFields: [
            { key: 'image', label: 'Image', type: 'image', required: true },
            { key: 'alt', label: 'Alt Text', type: 'text', placeholder: 'Describe the image for screen readers…' },
            { key: 'caption', label: 'Caption', type: 'text', placeholder: 'Optional caption shown below the image…' },
        ],
        settingsFields: [
            {
                key: 'size',
                label: 'Display Size',
                type: 'select',
                options: [
                    { value: 'full', label: 'Full Width' },
                    { value: 'large', label: 'Large (max-w-4xl)' },
                    { value: 'medium', label: 'Medium (max-w-2xl)' },
                ],
                default: 'full',
            },
        ],
        defaultPayload: { image: { url: '', mediaId: null }, alt: '', caption: '' },
        defaultSettings: { size: 'full' },
    },

    two_column: {
        label: 'Two Column',
        icon: '⬜⬜',
        description: 'Two equal-width columns with rich text content.',
        payloadFields: [
            { key: 'left_title', label: 'Left Column Title', type: 'text', placeholder: 'Left heading…' },
            { key: 'left_body', label: 'Left Column Content', type: 'richtext' },
            { key: 'right_title', label: 'Right Column Title', type: 'text', placeholder: 'Right heading…' },
            { key: 'right_body', label: 'Right Column Content', type: 'richtext' },
        ],
        settingsFields: [
            {
                key: 'ratio',
                label: 'Column Ratio',
                type: 'select',
                options: [
                    { value: '1:1', label: 'Equal (50/50)' },
                    { value: '2:1', label: 'Wide left (66/33)' },
                    { value: '1:2', label: 'Wide right (33/66)' },
                ],
                default: '1:1',
            },
        ],
        defaultPayload: { left_title: '', left_body: '', right_title: '', right_body: '' },
        defaultSettings: { ratio: '1:1' },
    },

    feature_list: {
        label: 'Feature List',
        icon: '✅',
        description: 'A title with a list of icon + text feature items.',
        payloadFields: [
            { key: 'title', label: 'Section Title', type: 'text', placeholder: 'Why choose us?' },
            {
                key: 'items',
                label: 'Features',
                type: 'repeater',
                subFields: [
                    { key: 'icon', label: 'Icon (emoji or text)', type: 'text', placeholder: '⚡' },
                    { key: 'text', label: 'Feature Text', type: 'text', placeholder: 'Expert coaching…' },
                ],
            },
        ],
        defaultPayload: { title: '', items: [{ icon: '', text: '' }] },
    },
};

/** Get all block type keys sorted by label. */
export const blockTypeList = Object.entries(blockSchemas).map(([type, schema]) => ({
    type,
    label: schema.label,
    icon: schema.icon,
    description: schema.description,
}));
