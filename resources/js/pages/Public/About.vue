<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { CheckCircle2, ArrowRight } from '@lucide/vue';

defineOptions({
    layout: PublicLayout,
});
</script>

<template>
    <Head title="About TopGrade London FC" />

    <div class="min-h-screen bg-slate-950 text-white">
        <!-- Hero Section -->
        <section class="relative min-h-[40vh] flex items-end overflow-hidden pb-12 pt-28 border-b border-slate-800">
            <div
                class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-30"
                style="background-image: url('/images/coaching_facility.png')"
            />
            <div class="absolute inset-0 bg-slate-950/80" />

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-4">
                <div class="inline-block px-3 py-1 rounded bg-slate-800 border border-slate-700 text-slate-300 text-xs font-semibold tracking-wider uppercase">
                    About The Club
                </div>

                <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight">
                    TopGrade London FC
                </h1>
                <p class="text-slate-300 text-base max-w-2xl">
                    Developing the next generation of football talent with professional UEFA coaching and modern facilities.
                </p>
            </div>
        </section>

        <!-- Mission Section -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 bg-slate-900">
            <div class="max-w-7xl mx-auto grid gap-12 lg:grid-cols-2 items-center">
                <div class="space-y-4">
                    <h2 class="text-3xl font-bold text-white">Our Mission</h2>
                    <p class="text-slate-300 text-base leading-relaxed">
                        TopGrade London FC is dedicated to providing exceptional football training that develops both skill and character in young players. We believe in nurturing talent from grassroots level through elite development programs.
                    </p>
                    <p class="text-slate-300 text-base leading-relaxed">
                        Our coaches combine technical expertise with a passion for developing confident, skilled footballers who understand the values of teamwork, discipline, and respect.
                    </p>
                </div>
                <div class="rounded-xl overflow-hidden border border-slate-800 shadow-lg">
                    <img src="/images/hero_action.png" alt="Training" class="w-full h-80 object-cover" />
                </div>
            </div>
        </section>

        <!-- Values Section -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 bg-slate-950">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-3xl font-bold text-white text-center mb-12">Our Core Values</h2>
                <div class="grid gap-8 md:grid-cols-3">
                    <div class="rounded-xl border border-slate-800 bg-slate-900 p-8 space-y-3">
                        <h3 class="text-xl font-bold text-white">Excellence</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            We pursue excellence in every aspect of our coaching and player development, setting high standards and continuously improving.
                        </p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-900 p-8 space-y-3">
                        <h3 class="text-xl font-bold text-white">Integrity</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            We operate with honesty and integrity, building trust through consistent values and transparent communication with players and families.
                        </p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-900 p-8 space-y-3">
                        <h3 class="text-xl font-bold text-white">Passion</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            We are passionate about football and dedicated to inspiring young players to love the game while developing their talents.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Facilities Section -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 bg-slate-900">
            <div class="max-w-7xl mx-auto grid gap-12 lg:grid-cols-2 items-center">
                <div class="rounded-xl overflow-hidden border border-slate-800 shadow-lg">
                    <img src="/images/coaching_facility.png" alt="Facility" class="w-full h-80 object-cover" />
                </div>
                <div class="space-y-6">
                    <h2 class="text-3xl font-bold text-white">Training Facilities & Setup</h2>
                    <p class="text-slate-300 text-base">
                        TopGrade London FC operates from high-grade London venues equipped with:
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3 text-slate-300 text-sm">
                            <CheckCircle2 class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" />
                            <span>Regulation synthetic pitches and training equipment</span>
                        </li>
                        <li class="flex items-start gap-3 text-slate-300 text-sm">
                            <CheckCircle2 class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" />
                            <span>Modern training equipment and technology setup</span>
                        </li>
                        <li class="flex items-start gap-3 text-slate-300 text-sm">
                            <CheckCircle2 class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" />
                            <span>UEFA licensed coaching staff with pro experience</span>
                        </li>
                        <li class="flex items-start gap-3 text-slate-300 text-sm">
                            <CheckCircle2 class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" />
                            <span>Safe, supportive, and inclusive player environment</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 bg-slate-950 border-t border-slate-800 text-center">
            <div class="max-w-3xl mx-auto space-y-6">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white">Ready to Join TopGrade FC?</h2>
                <p class="text-slate-300 text-base">
                    Explore our training programs and start your football journey with us today.
                </p>
                <div class="pt-2">
                    <Link
                        href="/bookings"
                        class="inline-flex items-center gap-2 px-8 py-4 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors shadow-md"
                    >
                        <span>View Training Programs</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>

