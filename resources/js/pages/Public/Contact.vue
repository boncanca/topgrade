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
        email: props.clubSettings?.email || globalSettings?.email || 'info@topgradelondonfc.co.uk',
        phone: props.clubSettings?.phone || globalSettings?.phone || '',
        address: props.clubSettings?.address || globalSettings?.address || 'Tottenham (N17) & Hackney (E9), London',
        instagram: props.clubSettings?.instagram || globalSettings?.social_links?.instagram || 'https://www.instagram.com/topgradelondonfc/',
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

    <div class="min-h-screen bg-tg-bg text-tg-text">
        <!-- Hero Section -->
        <section class="relative min-h-[36vh] sm:min-h-[40vh] flex items-end overflow-hidden pb-12 pt-24 sm:pt-28 border-b border-tg-border">
            <div
                class="absolute inset-0 bg-cover bg-center opacity-35"
                style="background-image: url('/images/club/club-training-london.jpg')"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-tg-bg via-tg-bg/85 to-transparent" />

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-3">
                <span class="inline-block px-3 py-1 rounded-xs bg-tg-bg-deep border border-tg-border text-tg-accent text-xs font-bold tracking-widest uppercase">
                    Contact Club
                </span>

                <h1
                    class="text-3xl sm:text-5xl font-normal uppercase tracking-tight text-tg-text-strong"
                    style="font-family: var(--tg-display);"
                >
                    Contact TopGrade London FC
                </h1>
                <p class="text-tg-text text-sm sm:text-base max-w-2xl">
                    Have questions about club teams, trial sessions, or weekly training? Contact our coaching and administrative staff.
                </p>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="grid gap-12 lg:grid-cols-2 items-start">
                <!-- Contact Info -->
                <div class="space-y-8">
                    <div>
                        <h2
                            class="text-2xl sm:text-3xl font-normal uppercase tracking-tight text-tg-text-strong mb-2"
                            style="font-family: var(--tg-display);"
                        >
                            Club Office &amp; Enquiries
                        </h2>
                        <p class="text-tg-text-muted text-sm">We are here to help parents, players, and supporters with club information.</p>
                    </div>

                    <div class="space-y-4">
                        <!-- Email -->
                        <div class="flex items-start gap-4 p-5 rounded-sm bg-tg-bg-deep/80 border border-tg-border">
                            <Mail class="w-5 h-5 text-tg-accent shrink-0 mt-0.5" />
                            <div>
                                <h3 class="font-bold text-tg-text-strong text-xs uppercase tracking-wider">Email</h3>
                                <a :href="`mailto:${club.email}`" class="text-tg-accent hover:underline text-sm transition-colors mt-0.5 block">
                                    {{ club.email }}
                                </a>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="flex items-start gap-4 p-5 rounded-sm bg-tg-bg-deep/80 border border-tg-border">
                            <MapPin class="w-5 h-5 text-tg-accent shrink-0 mt-0.5" />
                            <div>
                                <h3 class="font-bold text-tg-text-strong text-xs uppercase tracking-wider">Club Bases</h3>
                                <p class="text-tg-text text-sm mt-0.5">
                                    {{ club.address }}
                                </p>
                            </div>
                        </div>

                        <!-- Instagram -->
                        <div class="flex items-start gap-4 p-5 rounded-sm bg-tg-bg-deep/80 border border-tg-border">
                            <svg class="w-5 h-5 text-tg-accent shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                                <circle cx="12" cy="12" r="4" />
                                <circle cx="17.5" cy="6.5" r="0.8" fill="currentColor" stroke="none" />
                            </svg>
                            <div>
                                <h3 class="font-bold text-tg-text-strong text-xs uppercase tracking-wider">Instagram</h3>
                                <a
                                    :href="club.instagram"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-tg-accent hover:underline text-sm transition-colors mt-0.5 block"
                                >
                                    @topgradelondonfc
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="rounded-sm border border-tg-border bg-tg-bg-deep p-6 sm:p-8 lg:p-10 shadow-lg">
                    <!-- Top Error Alert -->
                    <div
                        v-if="form.hasErrors"
                        class="mb-6 flex items-start gap-3 rounded-xs border border-rose-500/40 bg-rose-500/10 p-4 text-rose-300"
                    >
                        <AlertCircle class="w-5 h-5 text-rose-400 shrink-0 mt-0.5" />
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-rose-200">
                                Submission Error
                            </h4>
                            <p class="text-xs text-rose-300/90 mt-0.5">
                                Please correct the highlighted errors below and try sending again.
                            </p>
                        </div>
                    </div>

                    <form v-if="!submitted" class="space-y-6" @submit.prevent="submit">
                        <!-- Name -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-tg-text-muted mb-1.5">
                                Full Name <span class="text-tg-accent">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                :class="[
                                    form.errors.name ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500' : 'border-tg-border focus:border-tg-accent focus:ring-tg-accent',
                                    'w-full rounded-xs bg-tg-bg px-4 py-3 text-base text-tg-text-strong placeholder:text-tg-text-muted/60 outline-none focus:ring-1 transition-colors'
                                ]"
                                placeholder="Your full name"
                            />
                            <p v-if="form.errors.name" class="mt-1.5 text-xs text-rose-400 font-medium">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-tg-text-muted mb-1.5">
                                Email Address <span class="text-tg-accent">*</span>
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                required
                                :class="[
                                    form.errors.email ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500' : 'border-tg-border focus:border-tg-accent focus:ring-tg-accent',
                                    'w-full rounded-xs bg-tg-bg px-4 py-3 text-base text-tg-text-strong placeholder:text-tg-text-muted/60 outline-none focus:ring-1 transition-colors'
                                ]"
                                placeholder="you@example.com"
                            />
                            <p v-if="form.errors.email" class="mt-1.5 text-xs text-rose-400 font-medium">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-tg-text-muted mb-1.5">
                                Phone Number (optional)
                            </label>
                            <input
                                v-model="form.phone"
                                type="tel"
                                :class="[
                                    form.errors.phone ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500' : 'border-tg-border focus:border-tg-accent focus:ring-tg-accent',
                                    'w-full rounded-xs bg-tg-bg px-4 py-3 text-base text-tg-text-strong placeholder:text-tg-text-muted/60 outline-none focus:ring-1 transition-colors'
                                ]"
                                placeholder="+44 7123 456789"
                            />
                            <p v-if="form.errors.phone" class="mt-1.5 text-xs text-rose-400 font-medium">
                                {{ form.errors.phone }}
                            </p>
                        </div>

                        <!-- Subject -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-tg-text-muted mb-1.5">
                                Subject <span class="text-tg-accent">*</span>
                            </label>
                            <select
                                v-model="form.subject"
                                required
                                :class="[
                                    form.errors.subject ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500' : 'border-tg-border focus:border-tg-accent focus:ring-tg-accent',
                                    'w-full rounded-xs bg-tg-bg px-4 py-3 text-base text-tg-text-strong outline-none focus:ring-1 transition-colors'
                                ]"
                            >
                                <option value="">Select a subject</option>
                                <option value="Trial Session Enquiry">Trial Session Enquiry</option>
                                <option value="Squad & Training Question">Squad &amp; Training Question</option>
                                <option value="Club Information">General Club Information</option>
                                <option value="Other">Other</option>
                            </select>
                            <p v-if="form.errors.subject" class="mt-1.5 text-xs text-rose-400 font-medium">
                                {{ form.errors.subject }}
                            </p>
                        </div>

                        <!-- Message -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-tg-text-muted">
                                    Message <span class="text-tg-accent">*</span>
                                </label>
                                <span class="text-[11px] text-tg-text-muted">
                                    Detailed enquiries help us respond faster
                                </span>
                            </div>
                            <textarea
                                v-model="form.message"
                                required
                                rows="6"
                                :class="[
                                    form.errors.message ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500' : 'border-tg-border focus:border-tg-accent focus:ring-tg-accent',
                                    'w-full rounded-xs bg-tg-bg px-4 py-3 text-base leading-relaxed text-tg-text-strong placeholder:text-tg-text-muted/60 outline-none focus:ring-1 transition-colors resize-y'
                                ]"
                                placeholder="Please share details about player age, previous football experience, or any questions for our coaches..."
                            />
                            <p v-if="form.errors.message" class="mt-1.5 text-xs text-rose-400 font-medium">
                                {{ form.errors.message }}
                            </p>
                        </div>

                        <!-- Submit -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="tg-btn w-full justify-center py-3.5 text-xs font-bold uppercase tracking-wider disabled:opacity-50 cursor-pointer"
                        >
                            {{ form.processing ? 'Sending Message...' : 'Send Message' }}
                        </button>
                    </form>

                    <!-- Expanded Success Message Layout -->
                    <div v-else class="text-center py-10 px-4 space-y-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 mx-auto">
                            <CheckCircle2 class="w-8 h-8" />
                        </div>
                        <div class="space-y-2">
                            <h3
                                class="text-2xl sm:text-3xl font-normal uppercase text-tg-text-strong tracking-tight"
                                style="font-family: var(--tg-display);"
                            >
                                Message Sent Successfully
                            </h3>
                            <p class="text-tg-text text-sm sm:text-base max-w-xl mx-auto leading-relaxed">
                                Thank you for getting in touch with TopGrade London FC. Our coaching and club admin team will review your enquiry and respond to your email address shortly.
                            </p>
                        </div>

                        <div class="pt-4 border-t border-tg-border max-w-md mx-auto">
                            <button
                                @click="submitted = false; form.reset()"
                                class="tg-btn ghost text-xs py-2 px-6"
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
