<script setup lang="ts">
import PricingPlans from '@/components/billing/PricingPlans.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { register } from '@/routes';
import { show } from '@/routes/billing';
import { router, usePage } from '@inertiajs/vue3';
import { motion } from 'motion-v';
import { ref } from 'vue';
import ILucideAppWindow from '~icons/lucide/app-window';
import ILucideArrowRight from '~icons/lucide/arrow-right';
import ILucideCreditCard from '~icons/lucide/credit-card';
import ILucideGithub from '~icons/lucide/github';
import ILucideLayers3 from '~icons/lucide/layers3';
import ILucideLock from '~icons/lucide/lock';
import ILucidePalette from '~icons/lucide/palette';
import ILucideRocket from '~icons/lucide/rocket';
import ILucideServerCog from '~icons/lucide/server-cog';
import ILucideShieldCheck from '~icons/lucide/shield-check';
import ILucideUsers from '~icons/lucide/users';

const page = usePage();

defineProps<{ plans: App.Data.Billing.PlanData[] }>();

useSeoMeta({
    title: 'Home',
    description: 'Another Saas Starter Kit',
});

const isProcessing = ref(false);

const handleSubscribeFromLanding = ({
    stripePriceId,
}: {
    stripePriceId: string;
}) => {
    isProcessing.value = true;
    if (page.props.user?.currentTeam) {
        router.get(
            show(page.props.user.currentTeam.id).url,
            {},
            { onFinish: () => (isProcessing.value = false) },
        );
    } else {
        router.get(
            register().url,
            { plan: stripePriceId },
            { onFinish: () => (isProcessing.value = false) },
        );
    }
};

const architecturalPillars = [
    {
        title: 'Robust & Modern Backend',
        description:
            'Built on the latest Laravel 12 with PHP 8.4. We enforce strict typing and high code quality standards for a robust and reliable foundation.',
        icon: ILucideServerCog,
    },
    {
        title: 'Clean & Scalable Architecture',
        description:
            'Logic is neatly organized for scalability. Data integrity is guaranteed by strongly-typed DTOs via Spatie/Laravel-Data, ensuring predictable and maintainable code.',
        icon: ILucideLayers3,
    },
    {
        title: 'SEO-Ready with SSR',
        description:
            'Server-Side Rendering (SSR) is ready out-of-the-box, ensuring optimal performance and excellent SEO for your public-facing pages.',
        icon: ILucideRocket,
    },
];

const features = [
    {
        title: 'Complete Authentication',
        description:
            'Powered by Laravel Fortify, the kit includes a secure, ready-to-use authentication system with Two-Factor Authentication, registration, login, password reset, and email verification.',
        icon: ILucideLock,
    },
    {
        title: 'Team Management',
        description:
            'The application is team-centric. Users can create teams, invite members, and even transfer team ownership seamlessly.',
        icon: ILucideUsers,
    },
    {
        title: 'Advanced Permissions',
        description:
            'Fine-grained permission management based on roles and plans. A Vue composable makes it easy to check user permissions directly on the frontend.',
        icon: ILucideShieldCheck,
    },
    {
        title: 'Subscription Billing',
        description:
            'Stripe Cashier integration out-of-the-box. Manage plans, subscriptions, and view invoices with ease.',
        icon: ILucideCreditCard,
    },
    {
        title: 'Socialite Logins',
        description:
            'Allow users to register and log in effortlessly using their Google and GitHub accounts. Adding more social providers is simple.',
        icon: ILucideGithub,
    },
    {
        title: 'Admin Panel',
        description:
            'A dedicated admin panel provides an overview of all user subscriptions, making management straightforward.',
        icon: ILucideServerCog,
    },
    {
        title: 'Static Markdown Pages',
        description:
            'Easily create and manage static content like Privacy Policy or Terms of Service pages using simple Markdown files, powered by Spatie/laravel-markdown.',
        icon: ILucideLayers3,
    },
    {
        title: 'Dynamic Theming',
        description:
            'Users can personalize their experience by choosing primary, secondary, and neutral colors for the UI.',
        icon: ILucidePalette,
    },
    {
        title: 'Flash Message Toasts',
        description:
            'Provide clear user feedback with beautifully rendered toast notifications for success, error, and info messages.',
        icon: ILucideAppWindow,
    },
];
</script>

<template>
    <PublicLayout>
        <motion.div
            :initial="{ opacity: 0, y: 50 }"
            :animate="{
                opacity: 1,
                y: 0,
                transition: { duration: 0.5, ease: 'easeOut' },
            }"
        >
            <UPageHero
                title="Another Saas Starter Kit"
                description="Launch your next SaaS faster than ever. A production-ready, feature-rich boilerplate built on a solid architectural foundation."
            >
                <template #links>
                    <motion.div
                        :initial="{ opacity: 0, y: 50 }"
                        :animate="{
                            opacity: 1,
                            y: 0,
                            transition: {
                                delay: 0.2,
                                duration: 0.5,
                                ease: 'easeOut',
                            },
                        }"
                        class="flex flex-wrap justify-center gap-4"
                    >
                        <UButton
                            :to="register().url"
                            size="xl"
                            label="Get Started - It's Free"
                            :icon="ILucideArrowRight"
                            trailing
                        />
                        <UButton
                            to="https://github.com/y-l-g/saasterkit"
                            target="_blank"
                            size="xl"
                            label="Star on GitHub"
                            color="neutral"
                            variant="ghost"
                            :icon="ILucideGithub"
                        />
                    </motion.div>
                </template>
            </UPageHero>
        </motion.div>

        <motion.div
            :initial="{ opacity: 0, y: 50 }"
            :whileInView="{
                opacity: 1,
                y: 0,
                transition: { duration: 0.7, ease: 'easeOut' },
            }"
            :inViewOptions="{ once: true }"
        >
            <UPageSection
                title="Built for Professionals"
                description="This isn't just a starter kit. It's a professional foundation designed for scalability, maintainability, and an outstanding developer experience."
                :ui="{ title: 'text-center', description: 'text-center' }"
            >
                <UPageGrid>
                    <motion.div
                        v-for="(pillar, index) in architecturalPillars"
                        :key="pillar.title"
                        :initial="{ opacity: 0, y: 50 }"
                        :whileInView="{
                            opacity: 1,
                            y: 0,
                            transition: {
                                delay: index * 0.15,
                                duration: 0.5,
                                ease: 'easeOut',
                            },
                        }"
                        :inViewOptions="{ once: true }"
                    >
                        <UPageCard
                            :title="pillar.title"
                            :description="pillar.description"
                            :icon="pillar.icon"
                            variant="ghost"
                        />
                    </motion.div>
                </UPageGrid>
            </UPageSection>
        </motion.div>

        <motion.div
            :initial="{ opacity: 0, y: 50 }"
            :whileInView="{
                opacity: 1,
                y: 0,
                transition: { duration: 0.7, ease: 'easeOut' },
            }"
            :inViewOptions="{ once: true }"
        >
            <UPageSection
                title="Features Included, Right Out-of-the-Box"
                description="Save weeks of development time with essential SaaS features already built, tested, and beautifully integrated."
            >
                <UPageGrid>
                    <motion.div
                        v-for="(feature, index) in features"
                        :key="feature.title"
                        :initial="{ opacity: 0, y: 50 }"
                        :whileInView="{
                            opacity: 1,
                            y: 0,
                            transition: {
                                delay: index * 0.1,
                                duration: 0.5,
                                ease: 'easeOut',
                            },
                        }"
                        :inViewOptions="{ once: true }"
                    >
                        <UPageCard
                            :title="feature.title"
                            :description="feature.description"
                            :icon="feature.icon"
                            variant="ghost"
                        />
                    </motion.div>
                </UPageGrid>
            </UPageSection>
        </motion.div>

        <motion.div
            :initial="{ opacity: 0, y: 50 }"
            :whileInView="{
                opacity: 1,
                y: 0,
                transition: { duration: 0.7, ease: 'easeOut' },
            }"
            :inViewOptions="{ once: true }"
        >
            <UPageSection
                title="Get Started Today"
                description="The pricings below are here for demonstration, and stripe is in test mode, so you can use a 4242 4242 4242 4242 credit card"
            >
                <PricingPlans
                    :on-subscribe="handleSubscribeFromLanding"
                    :loading="isProcessing"
                />
            </UPageSection>
        </motion.div>

        <motion.div
            :initial="{ opacity: 0, y: 50 }"
            :whileInView="{
                opacity: 1,
                y: 0,
                transition: { duration: 0.7, ease: 'easeOut' },
            }"
            :inViewOptions="{ once: true }"
        >
            <UPageSection
                :ui="{
                    wrapper: 'py-24 sm:py-32',
                }"
            >
                <UPageCTA
                    title="Ready to Build Your Next Big Idea?"
                    description="Stop rebuilding the same features. Start with a solid foundation and focus on what makes your application unique."
                    card
                    variant="soft"
                >
                    <template #links>
                        <UButton
                            :to="register().url"
                            size="xl"
                            label="Start Building Now"
                        />
                    </template>
                </UPageCTA>
            </UPageSection>
        </motion.div>
    </PublicLayout>
</template>
