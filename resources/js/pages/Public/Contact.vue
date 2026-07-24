<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Mail, Phone, MapPin, Clock, CheckCircle2 } from '@lucide/vue';

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
    <Head title="Contact TopGrade London FC" />

    <div class="min-h-screen bg-slate-950 text-white">
        <!-- Hero Section -->
        <section class="relative min-h-[35vh] flex items-end overflow-hidden pb-12 pt-28 border-b border-slate-800">
            <div
                class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-30"
                style="background-image: url('/images/coaching_facility.png')"
            />
            <div class="absolute inset-0 bg-slate-950/80" />

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-4">
                <div class="inline-block px-3 py-1 rounded bg-slate-800 border border-slate-700 text-slate-300 text-xs font-semibold tracking-wider uppercase">
                    Contact Us
                </div>

                <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight">
                    Get In Touch With TGLFC
                </h1>
                <p class="text-slate-300 text-base max-w-2xl">
                    Have questions about trial sessions, team enrollment, or coaching? Send us a message.
                </p>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="grid gap-12 lg:grid-cols-2 items-start">
                <!-- Contact Info -->
                <div class="space-y-8">
                    <div>
                        <h2 class="text-2xl font-bold text-white mb-2">Academy Office & Support</h2>
                        <p class="text-slate-400 text-sm">Our team is available Monday through Saturday to answer inquiries.</p>
                    </div>

                    <div class="space-y-6">
                        <!-- Email -->
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-slate-900 border border-slate-800">
                            <Mail class="w-5 h-5 text-purple-400 shrink-0 mt-0.5" />
                            <div>
                                <h3 class="font-bold text-white text-sm">Email Address</h3>
                                <a href="mailto:info@topgradefc.com" class="text-slate-300 hover:text-white text-sm transition-colors mt-0.5 block">
                                    info@topgradefc.com
                                </a>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-slate-900 border border-slate-800">
                            <Phone class="w-5 h-5 text-purple-400 shrink-0 mt-0.5" />
                            <div>
                                <h3 class="font-bold text-white text-sm">Telephone</h3>
                                <a href="tel:+442012345678" class="text-slate-300 hover:text-white text-sm transition-colors mt-0.5 block">
                                    +44 (0) 20 1234 5678
                                </a>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-slate-900 border border-slate-800">
                            <MapPin class="w-5 h-5 text-purple-400 shrink-0 mt-0.5" />
                            <div>
                                <h3 class="font-bold text-white text-sm">Location</h3>
                                <p class="text-slate-300 text-sm mt-0.5">
                                    London, United Kingdom<br />
                                    Topgrade Football Academy Grounds
                                </p>
                            </div>
                        </div>

                        <!-- Hours -->
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-slate-900 border border-slate-800">
                            <Clock class="w-5 h-5 text-purple-400 shrink-0 mt-0.5" />
                            <div>
                                <h3 class="font-bold text-white text-sm">Office Hours</h3>
                                <p class="text-slate-300 text-sm mt-0.5">
                                    Monday - Friday: 9:00 AM - 6:00 PM<br />
                                    Saturday: 9:00 AM - 5:00 PM
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
                                placeholder="+44 (0) 20 XXXX XXXX"
                            />
                        </div>

                        <!-- Company & Position (Optional Grid) -->
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">
                                    Company / Organization
                                </label>
                                <input
                                    v-model="form.company"
                                    type="text"
                                    class="w-full rounded-lg border border-slate-800 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none focus:border-purple-500 transition-colors"
                                    placeholder="Company name"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">
                                    Position / Role
                                </label>
                                <input
                                    v-model="form.position"
                                    type="text"
                                    class="w-full rounded-lg border border-slate-800 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none focus:border-purple-500 transition-colors"
                                    placeholder="Position title"
                                />
                            </div>
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
                                <option value="general">General Inquiry</option>
                                <option value="trial">Trial Session</option>
                                <option value="coaching">Coaching Inquiry</option>
                                <option value="other">Other</option>
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
                            class="w-full rounded-lg bg-purple-600 px-6 py-3.5 font-semibold text-white transition-colors hover:bg-purple-700 disabled:opacity-50 shadow-md"
                        >
                            {{ form.processing ? 'Sending Message...' : 'Send Message' }}
                        </button>
                    </form>

                    <!-- Success Message -->
                    <div v-else class="text-center py-6 space-y-4">
                        <CheckCircle2 class="w-12 h-12 text-emerald-400 mx-auto" />
                        <h3 class="text-2xl font-bold text-white">Message Sent!</h3>
                        <p class="text-slate-300 text-sm">
                            Thank you for reaching out. We will get back to you as soon as possible.
                        </p>
                        <button
                            @click="submitted = false; form.reset()"
                            class="rounded-lg bg-purple-600 px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-purple-700"
                        >
                            Send Another Message
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

