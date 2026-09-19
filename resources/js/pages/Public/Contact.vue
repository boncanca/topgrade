<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import { Mail, Phone, MapPin, CheckCircle2 } from '@lucide/vue';

defineOptions({
    layout: PublicLayout,
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
                                <a href="mailto:topgradelondonfc@hotmail.com" class="text-tg-accent hover:underline text-sm transition-colors mt-0.5 block">
                                    topgradelondonfc@hotmail.com
                                </a>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="flex items-start gap-4 p-5 rounded-sm bg-tg-bg-deep/80 border border-tg-border">
                            <MapPin class="w-5 h-5 text-tg-accent shrink-0 mt-0.5" />
                            <div>
                                <h3 class="font-bold text-tg-text-strong text-xs uppercase tracking-wider">Club Bases</h3>
                                <p class="text-tg-text text-sm mt-0.5">
                                    Tottenham (N17) &amp; Hackney (E9), London
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="rounded-sm border border-tg-border bg-tg-bg-deep p-6 sm:p-8">
                    <form v-if="!submitted" class="space-y-5" @submit.prevent="submit">
                        <!-- Name -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-tg-text-muted mb-1">
                                Full Name <span class="text-tg-accent">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full rounded-xs border border-tg-border bg-tg-bg px-3.5 py-2.5 text-sm text-tg-text-strong placeholder:text-tg-text-muted/60 outline-none focus:border-tg-accent focus:ring-1 focus:ring-tg-accent transition-colors"
                                placeholder="Your name"
                            />
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-tg-text-muted mb-1">
                                Email Address <span class="text-tg-accent">*</span>
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                required
                                class="w-full rounded-xs border border-tg-border bg-tg-bg px-3.5 py-2.5 text-sm text-tg-text-strong placeholder:text-tg-text-muted/60 outline-none focus:border-tg-accent focus:ring-1 focus:ring-tg-accent transition-colors"
                                placeholder="you@example.com"
                            />
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-tg-text-muted mb-1">
                                Phone Number (optional)
                            </label>
                            <input
                                v-model="form.phone"
                                type="tel"
                                class="w-full rounded-xs border border-tg-border bg-tg-bg px-3.5 py-2.5 text-sm text-tg-text-strong placeholder:text-tg-text-muted/60 outline-none focus:border-tg-accent focus:ring-1 focus:ring-tg-accent transition-colors"
                                placeholder="+44 7123 456789"
                            />
                        </div>

                        <!-- Subject -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-tg-text-muted mb-1">
                                Subject <span class="text-tg-accent">*</span>
                            </label>
                            <select
                                v-model="form.subject"
                                required
                                class="w-full rounded-xs border border-tg-border bg-tg-bg px-3.5 py-2.5 text-sm text-tg-text-strong outline-none focus:border-tg-accent focus:ring-1 focus:ring-tg-accent transition-colors"
                            >
                                <option value="">Select a subject</option>
                                <option value="Trial Session Enquiry">Trial Session Enquiry</option>
                                <option value="Squad & Training Question">Squad &amp; Training Question</option>
                                <option value="Club Information">General Club Information</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <!-- Message -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-tg-text-muted mb-1">
                                Message <span class="text-tg-accent">*</span>
                            </label>
                            <textarea
                                v-model="form.message"
                                required
                                rows="4"
                                class="w-full rounded-xs border border-tg-border bg-tg-bg px-3.5 py-2.5 text-sm text-tg-text-strong placeholder:text-tg-text-muted/60 outline-none focus:border-tg-accent focus:ring-1 focus:ring-tg-accent transition-colors"
                                placeholder="Your message here..."
                            />
                        </div>

                        <!-- Submit -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="tg-btn w-full justify-center py-3 text-xs disabled:opacity-50 cursor-pointer"
                        >
                            {{ form.processing ? 'Sending Message...' : 'Send Message' }}
                        </button>
                    </form>

                    <!-- Success Message -->
                    <div v-else class="text-center py-8 space-y-4">
                        <CheckCircle2 class="w-12 h-12 text-tg-accent mx-auto" />
                        <h3
                            class="text-2xl font-normal uppercase text-tg-text-strong tracking-tight"
                            style="font-family: var(--tg-display);"
                        >
                            Message Sent
                        </h3>
                        <p class="text-tg-text-muted text-sm max-w-sm mx-auto">
                            Thank you for contacting TopGrade London FC. A member of our club team will respond to your enquiry shortly.
                        </p>
                        <button
                            @click="submitted = false; form.reset()"
                            class="tg-btn ghost text-xs"
                        >
                            Send Another Message
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
