<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
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
    <Head title="Contact the Club — TopGrade London FC" />

    <div class="min-h-screen bg-slate-950 text-white">
        <!-- Hero Section -->
        <section class="relative min-h-[40vh] flex items-end overflow-hidden pb-12 pt-28 border-b border-slate-800">
            <div
                class="absolute inset-0 bg-cover bg-center opacity-50"
                style="background-image: url('/images/club/club-training-london.jpg')"
            />
            <div class="absolute inset-0 bg-black/70" />

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-3">
                <span class="inline-block px-3 py-1 rounded bg-purple-900/60 border border-purple-700/60 text-purple-200 text-xs font-bold tracking-widest uppercase">
                    Contact Club
                </span>

                <h1 class="text-3xl sm:text-5xl font-extrabold text-white uppercase tracking-tight">
                    Contact TopGrade London FC
                </h1>
                <p class="text-slate-300 text-base max-w-2xl">
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
                        <h2 class="text-2xl font-extrabold text-white uppercase tracking-tight mb-2">Club Office & Enquiries</h2>
                        <p class="text-slate-400 text-sm">We are here to help parents, players, and supporters with club information.</p>
                    </div>

                    <div class="space-y-4">
                        <!-- Email -->
                        <div class="flex items-start gap-4 p-5 rounded bg-slate-900 border border-slate-800">
                            <Mail class="w-5 h-5 text-purple-400 shrink-0 mt-0.5" />
                            <div>
                                <h3 class="font-bold text-white text-sm uppercase tracking-wider">Email</h3>
                                <a href="mailto:topgradelondonfc@hotmail.com" class="text-slate-300 hover:text-white text-sm transition-colors mt-0.5 block">
                                    topgradelondonfc@hotmail.com
                                </a>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="flex items-start gap-4 p-5 rounded bg-slate-900 border border-slate-800">
                            <MapPin class="w-5 h-5 text-purple-400 shrink-0 mt-0.5" />
                            <div>
                                <h3 class="font-bold text-white text-sm uppercase tracking-wider">Club Bases</h3>
                                <p class="text-slate-300 text-sm mt-0.5">
                                    Tottenham (N17) & Hackney (E9), London
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6 sm:p-8">
                    <form v-if="!submitted" class="space-y-5" @submit.prevent="submit">
                        <!-- Name -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">
                                Full Name <span class="text-destructive">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full rounded-lg border border-slate-800 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none focus:border-purple-500 transition-colors"
                                placeholder="Your name"
                            />
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">
                                Email Address <span class="text-destructive">*</span>
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                required
                                class="w-full rounded-lg border border-slate-800 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none focus:border-purple-500 transition-colors"
                                placeholder="you@example.com"
                            />
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">
                                Phone Number (optional)
                            </label>
                            <input
                                v-model="form.phone"
                                type="tel"
                                class="w-full rounded-lg border border-slate-800 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none focus:border-purple-500 transition-colors"
                                placeholder="+44 7123 456789"
                            />
                        </div>

                        <!-- Subject -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">
                                Subject <span class="text-destructive">*</span>
                            </label>
                            <select
                                v-model="form.subject"
                                required
                                class="w-full rounded-lg border border-slate-800 bg-slate-950 px-4 py-2.5 text-sm text-white outline-none focus:border-purple-500 transition-colors"
                            >
                                <option value="">Select a subject</option>
                                <option value="Trial Session Enquiry">Trial Session Enquiry</option>
                                <option value="Squad & Training Question">Squad & Training Question</option>
                                <option value="Club Information">General Club Information</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <!-- Message -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">
                                Message <span class="text-destructive">*</span>
                            </label>
                            <textarea
                                v-model="form.message"
                                required
                                rows="4"
                                class="w-full rounded-lg border border-slate-800 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none focus:border-purple-500 transition-colors"
                                placeholder="Your message here..."
                            />
                        </div>

                        <!-- Submit -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full rounded bg-purple-800 hover:bg-purple-900 px-6 py-3.5 font-bold uppercase tracking-wider text-xs text-white transition-colors disabled:opacity-50 cursor-pointer"
                        >
                            {{ form.processing ? 'Sending Message...' : 'Send Message' }}
                        </button>
                    </form>

                    <!-- Success Message -->
                    <div v-else class="text-center py-8 space-y-4">
                        <CheckCircle2 class="w-12 h-12 text-emerald-400 mx-auto" />
                        <h3 class="text-2xl font-bold text-white uppercase tracking-tight">Message Sent</h3>
                        <p class="text-slate-300 text-sm max-w-sm mx-auto">
                            Thank you for contacting TopGrade London FC. A member of our club team will respond to your enquiry shortly.
                        </p>
                        <button
                            @click="submitted = false; form.reset()"
                            class="rounded bg-purple-800 hover:bg-purple-900 px-6 py-2.5 text-xs font-bold uppercase tracking-wider text-white transition-colors cursor-pointer"
                        >
                            Send Another Message
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
