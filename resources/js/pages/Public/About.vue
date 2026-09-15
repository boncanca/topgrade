<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { ArrowRight, CheckCircle2 } from '@lucide/vue';

defineOptions({
    layout: PublicLayout,
});
</script>

<template>
    <Head title="About the Club — TopGrade London FC" />

    <div class="min-h-screen bg-slate-950 text-white">
        <!-- Hero Section -->
        <section class="relative min-h-[45vh] flex items-end overflow-hidden pb-16 pt-32 border-b border-slate-800">
            <div
                class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-35"
                style="background-image: url('/images/club/club-training-london.jpg')"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/80 to-slate-950/60" />

            <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-4">
                <span class="inline-block px-3.5 py-1 rounded-full bg-slate-800 border border-slate-700 text-purple-300 text-xs font-semibold tracking-wider uppercase">
                    About the Club
                </span>

                <h1 class="text-4xl sm:text-6xl font-extrabold text-white tracking-tight">
                    A club to grow with.
                </h1>
                <p class="text-slate-300 text-lg sm:text-xl max-w-2xl leading-relaxed">
                    TopGrade London FC is a youth football club helping young players develop through training, teamwork and playing experience.
                </p>
            </div>
        </section>

        <!-- Club Environment -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-900">
            <div class="max-w-6xl mx-auto grid gap-12 lg:grid-cols-2 items-center">
                <div class="space-y-6">
                    <span class="text-xs font-bold uppercase tracking-widest text-[var(--brand-secondary)] block">
                        Our Football Environment
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white">
                        Train. Play. Progress.
                    </h2>
                    <p class="text-slate-300 text-base leading-relaxed">
                        At TopGrade London FC, we believe player development happens when young footballers are given consistent coaching, clear guidance, and a positive team atmosphere.
                    </p>
                    <p class="text-slate-300 text-base leading-relaxed">
                        Every session is designed to build technical ball skill, decision-making, and understanding of the game in a supportive club culture.
                    </p>

                    <div class="pt-2">
                        <Link
                            href="/bookings"
                            class="inline-flex items-center gap-2 px-6 py-3.5 bg-[var(--brand-primary)] hover:opacity-90 text-white font-semibold rounded-lg transition-opacity shadow-md"
                        >
                            <span>Book a Trial</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>

                <div class="rounded-2xl overflow-hidden border border-slate-800 shadow-xl">
                    <img
                        src="/images/club/club-coach-mentoring.jpg"
                        alt="TopGrade London FC Coach and Players"
                        class="w-full h-80 sm:h-96 object-cover"
                    />
                </div>
            </div>
        </section>

        <!-- Development Pillars -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-950">
            <div class="max-w-6xl mx-auto">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <span class="text-xs font-bold uppercase tracking-widest text-slate-500 block mb-2">
                        Club Approach
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white">
                        What We Focus On
                    </h2>
                </div>

                <div class="grid gap-8 md:grid-cols-3">
                    <div class="rounded-xl border border-slate-800 bg-slate-900/80 p-8 space-y-3">
                        <h3 class="text-xl font-bold text-white">Technical Ability</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            Ball mastery, control, passing rhythm, and 1v1 confidence are the foundation of everything we do on the training pitch.
                        </p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-900/80 p-8 space-y-3">
                        <h3 class="text-xl font-bold text-white">Teamwork & Respect</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            Learning to communicate, support teammates, accept coaching feedback, and respect opponents and officials.
                        </p>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-900/80 p-8 space-y-3">
                        <h3 class="text-xl font-bold text-white">Playing Experience</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            Putting training into practice through small-sided games, structured drills, and matchday opportunities.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Opportunity CTA -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-900 text-center border-t border-slate-800">
            <div class="max-w-3xl mx-auto space-y-6">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white">
                    Experience TopGrade London FC
                </h2>
                <p class="text-slate-300 text-base max-w-xl mx-auto">
                    Bring your child to a trial session and discover our training environment first-hand.
                </p>
                <div class="pt-4">
                    <Link
                        href="/bookings"
                        class="inline-flex items-center gap-2 px-8 py-4 bg-[var(--brand-primary)] hover:opacity-90 text-white font-semibold rounded-lg transition-opacity shadow-lg"
                    >
                        <span>Book a Trial</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
