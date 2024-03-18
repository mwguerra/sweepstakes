<script setup>
import {computed, onBeforeUnmount, onMounted, ref} from 'vue'
import {useForm, usePage, router, Head} from '@inertiajs/vue3'

const props = defineProps(['sweepstakes', 'totalParticipants'])

const page = usePage();
const message = computed(() => page.props.flash?.message);

const form = useForm({
    email: ''
})

const timeLeft = ref('');
const targetDate = new Date(props.sweepstakes.draw_time_raw).getTime();
let interval;
const updateCountdown = () => {
    const now = new Date().getTime();
    const distance = targetDate - now;

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    if (distance < 0) {
        clearInterval(interval)
        timeLeft.value = 'Expired'
        router.reload()
    }

    timeLeft.value = `${days}d ${hours}h ${minutes}m ${seconds}s`;
}

const handleEdit = () => {
    router.visit(route('sweepstakes.edit', { slug: props.sweepstakes.slug }))
}

const handleSubmit = async () => {
    await form.post(route('sweepstakes.participate', { slug: props.sweepstakes.slug }), {
        preserveScroll: true,
        onSuccess: () => form.reset('email'),
    })
}

onMounted(() => {
    updateCountdown();
    interval = setInterval(updateCountdown, 1000);
});

onBeforeUnmount(() => {
    clearInterval(interval);
});
</script>

<template>
    <Head :title="`Sweepstakes: ${props.sweepstakes.title}`" />

    <div class="relative">
        <nav class="fixed top-0 right-0 z-10 p-4" v-if="$page.props.auth.user">
            <button class="px-6 py-2 rounded-full bg-white/80 text-gray-900" @click="handleEdit">Edit</button>
        </nav>
        <div class="relative isolate overflow-hidden bg-gray-900 px-6 py-24 shadow-2xl sm:px-24 xl:py-32 min-h-screen flex flex-col justify-center items-center">
            <div class="min-w-full">
                <h2 class="mx-auto max-w-2xl text-center text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ props.sweepstakes.title }}</h2>
                <p class="mx-auto mt-6 max-w-3xl text-center text-lg leading-7 text-gray-300">{{ props.sweepstakes.description }}</p>
                <form class="mx-auto mt-10 max-w-md flex flex-col gap-4" @submit.prevent="handleSubmit">
                    <div class="flex flex-col text-gray-200">
                        <p>Draw Time: {{ new Date(props.sweepstakes.draw_time_raw).toLocaleString() }}</p>
                        <p>Time Left: {{ timeLeft }}</p>
                        <p>Total Participants: {{ props.totalParticipants }}</p>
                        <p v-if="props.sweepstakes.winner">Winner: {{ props.sweepstakes.winner.email }}</p>
                    </div>
                    <div class="flex gap-x-4 flex-wrap">
                        <label for="email-address" class="sr-only">Email address</label>
                        <input id="email-address" name="email" type="email" autocomplete="email" required="" class="min-w-0 flex-auto rounded-md border-0 bg-white/5 px-3.5 py-2 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-white sm:text-sm sm:leading-6" placeholder="Enter your email" />
                        <button type="submit" class="flex-none rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Participate</button>
                    </div>
                    <div class="flex flex-col">
                        <p v-if="form.errors.email" class="mt-2 text-sm text-red-500">{{ form.errors.email }}</p>
                        <p v-if="form.error" class="mt-2 text-sm text-red-500">{{ form.error }}</p>
                        <p v-if="form.success" class="mt-2 text-sm text-green-400">{{ form.success }}</p>
                        <p v-if="message" class="mt-2 text-sm text-gray-500">{{ message }}</p>
                    </div>
                </form>

                <svg viewBox="0 0 1024 1024" class="absolute left-1/2 top-1/2 -z-10 h-[64rem] w-[64rem] -translate-x-1/2" aria-hidden="true">
                    <circle cx="512" cy="512" r="512" fill="url(#759c1415-0410-454c-8f7c-9a820de03641)" fill-opacity="0.7" />
                    <defs>
                        <radialGradient id="759c1415-0410-454c-8f7c-9a820de03641" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(512 512) rotate(90) scale(512)">
                            <stop stop-color="#7775D6" />
                            <stop offset="1" stop-color="#E935C1" stop-opacity="0" />
                        </radialGradient>
                    </defs>
                </svg>
            </div>
        </div>
    </div>
</template>
