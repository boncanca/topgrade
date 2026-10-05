<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { AlertCircle } from '@lucide/vue';
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
import PasskeyVerify from '@/components/PasskeyVerify.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Log in to your account',
        description: 'Enter your email and password below to log in',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const page = usePage();

const authAlert = computed(() => {
    const errs = (page.props.errors as Record<string, string>) || {};

    if (errs.auth_notice_title && errs.auth_notice) {
        return {
            title: errs.auth_notice_title,
            message: errs.auth_notice,
        };
    }

    if (errs.auth_notice) {
        return {
            title: 'Authentication notice',
            message: errs.auth_notice,
        };
    }

    if (errs.session) {
        return {
            title: 'Session expired',
            message: errs.session,
        };
    }

    if (errs.password) {
        return {
            title: 'Incorrect password',
            message: errs.password,
        };
    }

    if (errs.email) {
        const text = errs.email.toLowerCase();

        if (text.includes('verified') || text.includes('verification')) {
            return {
                title: 'Account verification required',
                message: errs.email,
            };
        }

        if (
            text.includes('administrator') ||
            text.includes('permission') ||
            text.includes('admin')
        ) {
            return {
                title: 'Administrator access required',
                message: errs.email,
            };
        }

        if (
            text.includes('not found') ||
            text.includes('no account') ||
            text.includes('no administrator account')
        ) {
            return {
                title: 'Account not found',
                message: errs.email,
            };
        }

        if (
            text.includes('credentials') ||
            text.includes('match') ||
            text.includes('failed') ||
            text.includes('incorrect')
        ) {
            return {
                title: 'Authentication failed',
                message: 'Your email or password is incorrect.',
            };
        }
    }

    return null;
});
</script>

<template>
    <Head title="Log in" />

    <div
        v-if="status"
        class="mb-4 rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-center text-sm font-medium text-emerald-400"
    >
        {{ status }}
    </div>

    <!-- Prominent Red Authentication Notice -->
    <Alert
        v-if="authAlert"
        variant="destructive"
        class="mb-4 border-red-500/30 bg-red-500/10 text-red-300 shadow-sm"
        data-test="auth-notice"
    >
        <AlertCircle class="size-4 shrink-0 text-red-400" />
        <div class="grid gap-1">
            <AlertTitle
                class="text-sm font-semibold tracking-tight text-red-300"
            >
                {{ authAlert.title }}
            </AlertTitle>
            <AlertDescription class="text-xs leading-relaxed text-red-400/90">
                {{ authAlert.message }}
            </AlertDescription>
        </div>
    </Alert>

    <PasskeyVerify />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="email">Email address</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="email@example.com"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label for="password">Password</Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-sm"
                        :tabindex="5"
                    >
                        Forgot your password?
                    </TextLink>
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Password"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <Label for="remember" class="flex items-center space-x-3">
                    <Checkbox id="remember" name="remember" :tabindex="3" />
                    <span>Remember me</span>
                </Label>
            </div>

            <Button
                type="submit"
                class="mt-4 w-full"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                Log in
            </Button>
        </div>

        <div class="text-muted-foreground text-center text-sm">
            Don't have an account?
            <TextLink :href="register()" :tabindex="5">Sign up</TextLink>
        </div>
    </Form>
</template>
