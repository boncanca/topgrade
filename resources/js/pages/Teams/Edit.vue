<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Image as ImageIcon,
    Plus,
    Save,
    Trash2,
    Upload,
    Users,
} from '@lucide/vue';
import { ref } from 'vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';

interface MomentOption {
    id: number;
    title: string;
    slug: string;
}

interface Player {
    name: string;
    number?: number | string;
    position?: string;
    role?: string;
    image?: string;
}

interface TeamData {
    id: number;
    name: string;
    slug: string;
    age_group: string;
    stage: string | null;
    description: string | null;
    visual_variant: string | null;
    moment_id: number | null;
    sort_order: number;
    is_active: boolean;
    players: Player[];
    image_url: string | null;
}

const props = defineProps<{
    team: TeamData;
    moments: MomentOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Overview',
                href: '/dashboard',
            },
            {
                title: 'Teams & Squads',
                href: '/dashboard/teams',
            },
            {
                title: 'Edit Team',
                href: '#',
            },
        ],
    },
});

const form = useForm({
    _method: 'PUT',
    name: props.team.name,
    slug: props.team.slug,
    age_group: props.team.age_group,
    stage: props.team.stage || 'Foundation Phase',
    description: props.team.description || '',
    visual_variant: props.team.visual_variant || 'foundation',
    moment_id: props.team.moment_id || '',
    sort_order: props.team.sort_order ?? 1,
    is_active: Boolean(props.team.is_active),
    image: null as File | null,
    players: (props.team.players && props.team.players.length > 0)
        ? JSON.parse(JSON.stringify(props.team.players))
        : [
            { name: '', number: '', position: 'Goalkeeper', role: 'Player' },
        ] as Player[],
});

const imagePreview = ref<string | null>(props.team.image_url);

function handleImageChange(event: Event): void {
    const target = event.target as HTMLInputElement;

    if (target.files && target.files[0]) {
        const file = target.files[0];
        form.image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
}

function addPlayer(): void {
    form.players.push({
        name: '',
        number: '',
        position: 'Midfielder',
        role: 'Player',
    });
}

function removePlayer(index: number): void {
    form.players.splice(index, 1);
}

function submit(): void {
    form.post(`/dashboard/teams/${props.team.id}`, {
        forceFormData: true,
    });
}
</script>

<template>
    <Head :title="`Edit ${team.name}`" />

    <div class="space-y-6">
        <PageHeader
            :title="`Edit Team: ${team.name}`"
            description="Update age group details, change squad photo, adjust roster players, and link gallery albums."
        >
            <template #actions>
                <Button variant="outline" as-child>
                    <Link href="/dashboard/teams">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Back to Teams
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <form class="space-y-8" @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Left 2 Cols: Main Info & Roster -->
                <div class="space-y-8 lg:col-span-2">
                    <!-- General Details Card -->
                    <div class="bg-card border-border rounded-xl border p-6 space-y-6">
                        <h2 class="text-base font-semibold text-foreground">
                            Team & Age Category Information
                        </h2>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label class="text-foreground block text-xs font-semibold">
                                    Team Name *
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    placeholder="e.g. U9 – U10"
                                    class="border-input bg-background placeholder:text-muted-foreground focus-visible:ring-ring mt-2 w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2"
                                />
                                <span v-if="form.errors.name" class="text-destructive mt-1 block text-xs">
                                    {{ form.errors.name }}
                                </span>
                            </div>

                            <div>
                                <label class="text-foreground block text-xs font-semibold">
                                    Age Group Badge *
                                </label>
                                <input
                                    v-model="form.age_group"
                                    type="text"
                                    required
                                    placeholder="e.g. U9–U10"
                                    class="border-input bg-background placeholder:text-muted-foreground focus-visible:ring-ring mt-2 w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2"
                                />
                                <span v-if="form.errors.age_group" class="text-destructive mt-1 block text-xs">
                                    {{ form.errors.age_group }}
                                </span>
                            </div>

                            <div>
                                <label class="text-foreground block text-xs font-semibold">
                                    Development Stage
                                </label>
                                <select
                                    v-model="form.stage"
                                    class="border-input bg-background text-foreground focus-visible:ring-ring mt-2 w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2"
                                >
                                    <option value="Foundation Phase">Foundation Phase (U7–U8)</option>
                                    <option value="Skill Acquisition">Skill Acquisition (U9–U10)</option>
                                    <option value="Game Development">Game Development (U11–U12)</option>
                                    <option value="Youth Progression">Youth Progression (U13–U14)</option>
                                    <option value="Youth Competition">Youth Competition (U15–U16)</option>
                                    <option value="Senior Pathway">Senior Pathway</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-foreground block text-xs font-semibold">
                                    Sort Order
                                </label>
                                <input
                                    v-model.number="form.sort_order"
                                    type="number"
                                    min="0"
                                    class="border-input bg-background placeholder:text-muted-foreground focus-visible:ring-ring mt-2 w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="text-foreground block text-xs font-semibold">
                                Coaching Focus & Philosophy Description
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                placeholder="Details about technical drills, matchday focus, and tactical expectations for this squad..."
                                class="border-input bg-background placeholder:text-muted-foreground focus-visible:ring-ring mt-2 w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2"
                            />
                        </div>
                    </div>

                    <!-- Players Roster Manager -->
                    <div class="bg-card border-border rounded-xl border p-6 space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-base font-semibold text-foreground flex items-center gap-2">
                                    <Users class="h-4 w-4 text-amber-500" />
                                    <span>Squad Players Roster ({{ form.players.length }})</span>
                                </h2>
                                <p class="text-muted-foreground text-xs mt-1">
                                    Add or edit registered players with their squad numbers, positions, and leadership roles.
                                </p>
                            </div>

                            <Button type="button" variant="outline" size="sm" @click="addPlayer">
                                <Plus class="mr-1 h-3.5 w-3.5" />
                                Add Player
                            </Button>
                        </div>

                        <div class="space-y-3">
                            <div
                                v-for="(player, idx) in form.players"
                                :key="idx"
                                class="bg-muted/30 border-border flex flex-col sm:flex-row items-center gap-3 rounded-lg border p-3"
                            >
                                <div class="h-8 w-8 rounded-full overflow-hidden border border-neutral-700 bg-neutral-800 shrink-0 mt-3 sm:mt-4">
                                    <img v-if="player.image" :src="player.image" :alt="player.name" class="h-full w-full object-cover" />
                                    <div v-else class="flex h-full w-full items-center justify-center font-bold text-[10px] text-muted-foreground">
                                        {{ player.name ? player.name.charAt(0) : '#' }}
                                    </div>
                                </div>

                                <div class="w-full sm:w-16">
                                    <label class="text-muted-foreground block text-[10px] font-semibold uppercase">No.</label>
                                    <input
                                        v-model="player.number"
                                        type="number"
                                        placeholder="10"
                                        class="border-input bg-background focus-visible:ring-ring mt-1 w-full rounded border px-2 py-1 text-xs"
                                    />
                                </div>

                                <div class="w-full flex-1 min-w-[140px]">
                                    <label class="text-muted-foreground block text-[10px] font-semibold uppercase">Player Full Name *</label>
                                    <input
                                        v-model="player.name"
                                        type="text"
                                        required
                                        placeholder="e.g. Kai Robinson"
                                        class="border-input bg-background focus-visible:ring-ring mt-1 w-full rounded border px-2 py-1 text-xs"
                                    />
                                </div>

                                <div class="w-full sm:w-28">
                                    <label class="text-muted-foreground block text-[10px] font-semibold uppercase">Position</label>
                                    <input
                                        v-model="player.position"
                                        type="text"
                                        placeholder="Midfielder"
                                        class="border-input bg-background focus-visible:ring-ring mt-1 w-full rounded border px-2 py-1 text-xs"
                                    />
                                </div>

                                <div class="w-full sm:w-28">
                                    <label class="text-muted-foreground block text-[10px] font-semibold uppercase">Role</label>
                                    <select
                                        v-model="player.role"
                                        class="border-input bg-background text-foreground focus-visible:ring-ring mt-1 w-full rounded border px-2 py-1 text-xs"
                                    >
                                        <option value="Player">Player</option>
                                        <option value="Captain">Captain</option>
                                        <option value="Vice-Captain">Vice-Captain</option>
                                    </select>
                                </div>

                                <div class="w-full sm:w-44">
                                    <label class="text-muted-foreground block text-[10px] font-semibold uppercase">Player Photo URL</label>
                                    <input
                                        v-model="player.image"
                                        type="text"
                                        placeholder="/images/players/...jpg"
                                        class="border-input bg-background focus-visible:ring-ring mt-1 w-full rounded border px-2 py-1 text-xs"
                                    />
                                </div>

                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    class="text-destructive hover:bg-destructive/10 hover:text-destructive shrink-0 mt-3 sm:mt-4"
                                    @click="removePlayer(idx)"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Col: Squad Photo & Gallery Link -->
                <div class="space-y-8">
                    <!-- Squad Photo Card -->
                    <div class="bg-card border-border rounded-xl border p-6 space-y-4">
                        <h2 class="text-base font-semibold text-foreground flex items-center gap-2">
                            <Upload class="h-4 w-4" />
                            <span>Squad Photo</span>
                        </h2>

                        <div class="relative aspect-[4/3] overflow-hidden rounded-lg border border-neutral-800 bg-neutral-900">
                            <img
                                v-if="imagePreview"
                                :src="imagePreview"
                                alt="Squad preview"
                                class="h-full w-full object-cover"
                            />
                            <div v-else class="flex h-full w-full flex-col items-center justify-center p-4 text-center text-xs text-neutral-500">
                                <ImageIcon class="mb-2 h-8 w-8 text-neutral-600" />
                                <span>Upload official squad matchday or training team photo</span>
                            </div>
                        </div>

                        <div>
                            <input
                                type="file"
                                accept="image/*"
                                class="border-input bg-background text-foreground file:bg-primary file:text-primary-foreground focus-visible:ring-ring w-full rounded-md border text-xs file:mr-2 file:border-0 file:px-2 file:py-1 focus-visible:outline-none"
                                @change="handleImageChange"
                            />
                            <span v-if="form.errors.image" class="text-destructive mt-1 block text-xs">
                                {{ form.errors.image }}
                            </span>
                        </div>
                    </div>

                    <!-- Linked Gallery Album Card -->
                    <div class="bg-card border-border rounded-xl border p-6 space-y-4">
                        <h2 class="text-base font-semibold text-foreground flex items-center gap-2">
                            <ImageIcon class="h-4 w-4 text-amber-500" />
                            <span>Linked Gallery Album</span>
                        </h2>
                        <p class="text-muted-foreground text-xs">
                            Select an existing published moment to show highlights and photo albums on this squad's card.
                        </p>

                        <select
                            v-model="form.moment_id"
                            class="border-input bg-background text-foreground focus-visible:ring-ring w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2"
                        >
                            <option value="">No Gallery Linked</option>
                            <option v-for="m in moments" :key="m.id" :value="m.id">
                                {{ m.title }}
                            </option>
                        </select>
                    </div>

                    <!-- Publishing Status -->
                    <div class="bg-card border-border rounded-xl border p-6 space-y-4">
                        <h2 class="text-base font-semibold text-foreground">
                            Status & Visibility
                        </h2>

                        <label class="flex items-center gap-2 text-sm text-foreground cursor-pointer">
                            <input
                                v-model="form.is_active"
                                type="checkbox"
                                class="rounded border-input text-primary focus:ring-primary"
                            />
                            <span>Active (Visible on public website)</span>
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        <Button
                            type="submit"
                            class="w-full"
                            :disabled="form.processing"
                        >
                            <Save class="mr-2 h-4 w-4" />
                            {{ form.processing ? 'Saving Changes...' : 'Update Team' }}
                        </Button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
