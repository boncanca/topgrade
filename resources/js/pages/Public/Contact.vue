<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { Mail, MapPin, CheckCircle2, AlertCircle } from '@lucide/vue';
import { computed, ref } from 'vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

defineOptions({
    layout: PublicLayout,
});

const props = defineProps<{
    clubSettings?: {
        email?: string;
        phone?: string;
        address?: string;
        instagram?: string;
    };
}>();

const page = usePage();
const club = computed(() => {
    const globalSettings = (page.props as any).clubSettings;

    return {
        email:
            props.clubSettings?.email ||
            globalSettings?.email ||
            'info@topgradelondonfc.co.uk',
        phone: props.clubSettings?.phone || globalSettings?.phone || '',
        address:
            props.clubSettings?.address ||
            globalSettings?.address ||
            'Tottenham (N17) & Hackney (E9), London',
        instagram:
            props.clubSettings?.instagram ||
            globalSettings?.social_links?.instagram ||
            'https://www.instagram.com/topgradelondonfc/',
    };
});

const form = useForm({
    name: '',
    email: '',
    phone: '',
    company: '',
    position: '',
    subject: '',
    message: '',
});

const submitted = ref(false);

function submit() {
    form.post('/contact', {
        onSuccess: () => {
            submitted.value = true;
            form.reset();
        },
    });
}
</script>

<template>
    <SeoHead
        title="Contact TopGrade London FC | London Youth Football"
        description="Contact TopGrade London FC coaching staff and management for training sessions, trial bookings, and youth squad enquiries in Tottenham and Hackney."
        path="/contact"
    />

    <div class="bg-tg-bg text-tg-text min-h-screen">
        <!-- Hero Section -->
        <section
            class="border-tg-border relative flex min-h-[36vh] items-end overflow-hidden border-b pb-12 pt-24 sm:min-h-[40vh] sm:pt-28"
        >
            <div
                class="absolute inset-0 bg-cover bg-center opacity-35"
                style="
                    background-image: url('/images/club/youth-squad-team-topgrade-london-fc.webp');
                "
            />
            <div
                class="from-tg-bg via-tg-bg/85 absolute inset-0 bg-gradient-to-t to-transparent"
            />

            <div
                class="relative z-10 mx-auto w-full max-w-7xl space-y-3 px-4 sm:px-6 lg:px-8"
            >
                <span
                    class="rounded-xs bg-tg-bg-deep border-tg-border text-tg-accent inline-block border px-3 py-1 text-xs font-bold uppercase tracking-widest"
                >
                    Contact Club
                </span>

                <h1
                    class="text-tg-text-strong text-3xl font-normal uppercase tracking-tight sm:text-5xl"
                    style="font-family: var(--tg-display)"
                >
                    Contact TopGrade London FC
                </h1>
                <p class="text-tg-text max-w-2xl text-sm sm:text-base">
                    Have questions about club teams, trial sessions, or weekly
                    training? Contact our coaching and administrative staff.
                </p>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid items-start gap-12 lg:grid-cols-2">
                <!-- Contact Info -->
                <div class="space-y-8">
                    <div>
                        <h2
                            class="text-tg-text-strong mb-2 text-2xl font-normal uppercase tracking-tight sm:text-3xl"
                            style="font-family: var(--tg-display)"
                        >
                            Club Office &amp; Enquiries
                        </h2>
                        <p class="text-tg-text-muted text-sm">
                            We are here to help parents, players, and supporters
                            with club information.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <!-- Email -->
                        <div
                            class="bg-tg-bg-deep/80 border-tg-border flex items-start gap-4 rounded-sm border p-5"
                        >
                            <Mail
                                class="text-tg-accent mt-0.5 h-5 w-5 shrink-0"
                            />
                            <div>
                                <h3
                                    class="text-tg-text-strong text-xs font-bold uppercase tracking-wider"
                                >
                                    Email
                                </h3>
                                <a
                                    :href="`mailto:${club.email}`"
                                    class="text-tg-accent mt-0.5 block text-sm transition-colors hover:underline"
                                >
                                    {{ club.email }}
                                </a>
                            </div>
                        </div>

                        <!-- Location -->
                        <div
                            class="bg-tg-bg-deep/80 border-tg-border flex items-start gap-4 rounded-sm border p-5"
                        >
                            <MapPin
                                class="text-tg-accent mt-0.5 h-5 w-5 shrink-0"
                            />
                            <div>
                                <h3
                                    class="text-tg-text-strong text-xs font-bold uppercase tracking-wider"
                                >
                                    Club Bases
                                </h3>
                                <p class="text-tg-text mt-0.5 text-sm">
                                    {{ club.address }}
                                </p>
                            </div>
                        </div>

                        <!-- Instagram -->
                        <div
                            class="bg-tg-bg-deep/80 border-tg-border flex items-start gap-4 rounded-sm border p-5"
                        >
                            <svg
                                class="text-tg-accent mt-0.5 h-5 w-5 shrink-0"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.75"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <rect
                                    x="2"
                                    y="2"
                                    width="20"
                                    height="20"
                                    rx="5"
                                    ry="5"
                                />
                                <circle cx="12" cy="12" r="4" />
                                <circle
                                    cx="17.5"
                                    cy="6.5"
                                    r="0.8"
                                    fill="currentColor"
                                    stroke="none"
                                />
                            </svg>
                            <div>
                                <h3
                                    class="text-tg-text-strong text-xs font-bold uppercase tracking-wider"
                                >
                                    Instagram
                                </h3>
                                <a
                                    :href="club.instagram"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-tg-accent mt-0.5 block text-sm transition-colors hover:underline"
                                >
                                    @topgradelondonfc
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div
                    class="border-tg-border bg-tg-bg-deep rounded-sm border p-6 shadow-lg sm:p-8 lg:p-10"
                >
                    <!-- Top Error Alert -->
                    <div
                        v-if="form.hasErrors"
                        class="rounded-xs mb-6 flex items-start gap-3 border border-rose-500/40 bg-rose-500/10 p-4 text-rose-300"
                    >
                        <AlertCircle
                            class="mt-0.5 h-5 w-5 shrink-0 text-rose-400"
                        />
                        <div>
                            <h4
                                class="text-xs font-bold uppercase tracking-wider text-rose-200"
                            >
                                Submission Error
                            </h4>
                            <p class="mt-0.5 text-xs text-rose-300/90">
                                Please correct the highlighted errors below and
                                try sending again.
                            </p>
                        </div>
                    </div>

                    <form
                        v-if="!submitted"
                        class="space-y-6"
                        @submit.prevent="submit"
                    >
                        <!-- Name -->
                        <div>
                            <label
                                class="text-tg-text-muted mb-1.5 block text-xs font-semibold uppercase tracking-wider"
                            >
                                Full Name <span class="text-tg-accent">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                :class="[
                                    form.errors.name
                                        ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500'
                                        : 'border-tg-border focus:border-tg-accent focus:ring-tg-accent',
                                    'rounded-xs bg-tg-bg text-tg-text-strong placeholder:text-tg-text-muted/60 w-full px-4 py-3 text-base outline-none transition-colors focus:ring-1',
                                ]"
                                placeholder="Your full name"
                            />
                            <p
                                v-if="form.errors.name"
                                class="mt-1.5 text-xs font-medium text-rose-400"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label
                                class="text-tg-text-muted mb-1.5 block text-xs font-semibold uppercase tracking-wider"
                            >
                                Email Address
                                <span class="text-tg-accent">*</span>
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                required
                                :class="[
                                    form.errors.email
                                        ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500'
                                        : 'border-tg-border focus:border-tg-accent focus:ring-tg-accent',
                                    'rounded-xs bg-tg-bg text-tg-text-strong placeholder:text-tg-text-muted/60 w-full px-4 py-3 text-base outline-none transition-colors focus:ring-1',
                                ]"
                                placeholder="you@example.com"
                            />
                            <p
                                v-if="form.errors.email"
                                class="mt-1.5 text-xs font-medium text-rose-400"
                            >
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label
                                class="text-tg-text-muted mb-1.5 block text-xs font-semibold uppercase tracking-wider"
                            >
                                Phone Number (optional)
                            </label>
                            <input
                                v-model="form.phone"
                                type="tel"
                                :class="[
                                    form.errors.phone
                                        ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500'
                                        : 'border-tg-border focus:border-tg-accent focus:ring-tg-accent',
                                    'rounded-xs bg-tg-bg text-tg-text-strong placeholder:text-tg-text-muted/60 w-full px-4 py-3 text-base outline-none transition-colors focus:ring-1',
                                ]"
                                placeholder="+44 7123 456789"
                            />
                            <p
                                v-if="form.errors.phone"
                                class="mt-1.5 text-xs font-medium text-rose-400"
                            >
                                {{ form.errors.phone }}
                            </p>
                        </div>

                        <!-- Subject -->
                        <div>
                            <label
                                class="text-tg-text-muted mb-1.5 block text-xs font-semibold uppercase tracking-wider"
                            >
                                Subject <span class="text-tg-accent">*</span>
                            </label>
                            <select
                                v-model="form.subject"
                                required
                                :class="[
                                    form.errors.subject
                                        ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500'
                                        : 'border-tg-border focus:border-tg-accent focus:ring-tg-accent',
                                    'rounded-xs bg-tg-bg text-tg-text-strong w-full px-4 py-3 text-base outline-none transition-colors focus:ring-1',
                                ]"
                            >
                                <option value="">Select a subject</option>
                                <option value="Trial Session Enquiry">
                                    Trial Session Enquiry
                                </option>
                                <option value="Squad & Training Question">
                                    Squad &amp; Training Question
                                </option>
                                <option value="Club Information">
                                    General Club Information
                                </option>
                                <option value="Other">Other</option>
                            </select>
                            <p
                                v-if="form.errors.subject"
                                class="mt-1.5 text-xs font-medium text-rose-400"
                            >
                                {{ form.errors.subject }}
                            </p>
                        </div>

                        <!-- Message -->
                        <div>
                            <div
                                class="mb-1.5 flex items-center justify-between"
                            >
                                <label
                                    class="text-tg-text-muted block text-xs font-semibold uppercase tracking-wider"
                                >
                                    Message
                                    <span class="text-tg-accent">*</span>
                                </label>
                                <span class="text-tg-text-muted text-[11px]">
                                    Detailed enquiries help us respond faster
                                </span>
                            </div>
                            <textarea
                                v-model="form.message"
                                required
                                rows="6"
                                :class="[
                                    form.errors.message
                                        ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500'
                                        : 'border-tg-border focus:border-tg-accent focus:ring-tg-accent',
                                    'rounded-xs bg-tg-bg text-tg-text-strong placeholder:text-tg-text-muted/60 w-full resize-y px-4 py-3 text-base leading-relaxed outline-none transition-colors focus:ring-1',
                                ]"
                                placeholder="Please share details about player age, previous football experience, or any questions for our coaches..."
                            />
                            <p
                                v-if="form.errors.message"
                                class="mt-1.5 text-xs font-medium text-rose-400"
                            >
                                {{ form.errors.message }}
                            </p>
                        </div>

                        <!-- Submit -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="tg-btn w-full cursor-pointer justify-center py-3.5 text-xs font-bold uppercase tracking-wider disabled:opacity-50"
                        >
                            {{
                                form.processing
                                    ? 'Sending Message...'
                                    : 'Send Message'
                            }}
                        </button>
                    </form>

                    <!-- Expanded Success Message Layout -->
                    <div v-else class="space-y-6 px-4 py-10 text-center">
                        <div
                            class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-full border border-emerald-500/30 bg-emerald-500/10 text-emerald-400"
                        >
                            <CheckCircle2 class="h-8 w-8" />
                        </div>
                        <div class="space-y-2">
                            <h3
                                class="text-tg-text-strong text-2xl font-normal uppercase tracking-tight sm:text-3xl"
                                style="font-family: var(--tg-display)"
                            >
                                Message Sent Successfully
                            </h3>
                            <p
                                class="text-tg-text mx-auto max-w-xl text-sm leading-relaxed sm:text-base"
                            >
                                Thank you for getting in touch with TopGrade
                                London FC. Our coaching and club admin team will
                                review your enquiry and respond to your email
                                address shortly.
                            </p>
                        </div>

                        <div
                            class="border-tg-border mx-auto max-w-md border-t pt-4"
                        >
                            <button
                                @click="
                                    submitted = false;
                                    form.reset();
                                "
                                class="tg-btn ghost px-6 py-2 text-xs"
                            >
                                Send Another Message
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
