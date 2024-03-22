<script setup>
import {computed, onBeforeUnmount, onMounted, ref} from 'vue'
import {useForm, usePage, router, Head} from '@inertiajs/vue3'

const props = defineProps(['sweepstakes', 'totalParticipants'])

const page = usePage();
const message = computed(() => page.props.flash?.message);

const emailSubmittedKey = `emailSubmitted-${props.sweepstakes.slug}`;
const emailConfirmedKey = `emailConfirmed-${props.sweepstakes.slug}`;

const showForm = ref(true);

const form = useForm({
    email: ''
})

const timeLeft = ref('');
const targetDate = new Date(props.sweepstakes.draw_time_raw).getTime();
let interval;
const updateCountdown = () => {
    const now = new Date().getTime();
    const distance = targetDate - now;

    if (distance <= 0) {
        clearInterval(interval);
        timeLeft.value = 'Time\'s up!';
        showForm.value = false;
    } else {
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        timeLeft.value = `${days}d ${hours}h ${minutes}m ${seconds}s`;
    }
};

const handleEdit = () => {
    router.visit(route('sweepstakes.edit', { slug: props.sweepstakes.slug }))
}

const handleSubmit = async () => {
    localStorage.setItem(emailSubmittedKey, 'true');

    await form.post(route('sweepstakes.participate', { sweepstakes: props.sweepstakes.slug }), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('email');
            localStorage.setItem(emailConfirmedKey, 'true');
            showForm.value = false;
        }
    })
}

const maskEmail = email => {
    const [localPart, domain] = email.split('@');
    if (!localPart || !domain) {
        return email;
    }

    if (localPart.length <= 5) {
        const firstChar = localPart.substring(0, 1);
        const lastChar = localPart.substring(localPart.length - 1);
        const maskedMiddle = '*'.repeat(localPart.length - 2);
        return `${firstChar}${maskedMiddle}${lastChar}@${domain}`;
    }

    const firstTwoChars = localPart.substring(0, 2);
    const lastTwoChars = localPart.substring(localPart.length - 2);
    const maskedMiddle = '*'.repeat(localPart.length - 4);

    return `${firstTwoChars}${maskedMiddle}${lastTwoChars}@${domain}`;
}

onMounted(() => {
    const emailSubmitted = localStorage.getItem(emailSubmittedKey) === 'true';
    const emailConfirmed = localStorage.getItem(emailConfirmedKey) === 'true';

    if (emailSubmitted && emailConfirmed) {
        showForm.value = false;
    }

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

                <div class="mx-auto mt-10 max-w-md flex flex-col gap-4" >
                    <div class="flex flex-col text-gray-200">
                        <p>Draw Time: {{ new Date(props.sweepstakes.draw_time_raw).toLocaleString() }}</p>
                        <p>Time Left: {{ timeLeft }}</p>
                        <p>Total Participants: {{ props.totalParticipants }}</p>
                        <p v-if="props.sweepstakes.winner">Winner: {{ maskEmail(props.sweepstakes.winner.email) }}</p>
                    </div>
                </div>

                <form v-if="showForm" class="mx-auto mt-10 max-w-md flex flex-col gap-4" @submit.prevent="handleSubmit">
                    <div v-if="!message" class="flex gap-x-4 flex-wrap">
                        <label class="sr-only">Email address</label>
                        <input name="email" type="email" required class="min-w-0 flex-auto rounded-md border-0 bg-white/5 px-3.5 py-2 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-white sm:text-sm sm:leading-6" placeholder="Enter your email" v-model="form.email" />
                        <button type="submit" class="flex-none rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Participate</button>
                    </div>
                    <div class="flex flex-col">
                        <p v-if="form.errors.email" class="mt-2 text-sm text-red-500">{{ form.errors.email }}</p>
                        <p v-if="message" class="mt-2 text-md text-green-500">{{ message }}</p>
                    </div>
                </form>
                <div v-if="!showForm" class="mx-auto mt-10 max-w-md text-center text-lg font-semibold text-green-500">
                    Your email is already registered for this sweepstakes. Good luck!
                </div>

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
