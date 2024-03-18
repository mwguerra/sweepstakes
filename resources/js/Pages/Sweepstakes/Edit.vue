<script setup>
import {useForm, usePage, Head, router} from '@inertiajs/vue3'
import {computed, ref} from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import BaseSelect from "@/Components/BaseSelect.vue";
import { ArrowUpOnSquareStackIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    sweepstakes: {
        type: Object,
        default: null
    },
    timezones: Array
})

const fileInput = ref(null);

const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

const form = useForm({
    title: props.sweepstakes ? props.sweepstakes.title : '',
    slug: props.sweepstakes ? props.sweepstakes.slug : '',
    description: props.sweepstakes ? props.sweepstakes.description : '',
    draw_time: props.sweepstakes ? stripSecondsAndTimezone(props.sweepstakes.draw_time_raw) : '',
    timezone: props.sweepstakes ? (props.sweepstakes.timezone ?? userTimezone) : userTimezone,
    winner_email_message: props.sweepstakes ? props.sweepstakes.winner_email_message : '',
    files: props.sweepstakes ? props.sweepstakes.winner_email_files : [],
    newFiles: [],
})

function stripSecondsAndTimezone(isoDateString) {
    return isoDateString.replace(/(:\d\d):\d\d.*$/, '$1');
}

const errors = computed(() => usePage().props.errors)

const keptFiles = computed(() => form.files.map(file => file.id));
const removedFiles = computed(() => {
    const currentFileIds = new Set(form.files.map(file => file.id));
    return props.sweepstakes
        ? props.sweepstakes.winner_email_files
            .filter(file => !currentFileIds.has(file.id))
            .map(file => file.id)
        : [];
});

const submit = () => {
    const method = props.sweepstakes ? 'put' : 'post';
    const url = props.sweepstakes
        ? route('sweepstakes.update', { sweepstakes: props.sweepstakes.slug })
        : route('sweepstakes.store')

    form
        .transform((data) => {
            const payload = {...data}

            payload._method = method;
            payload.keptFiles = keptFiles.value;
            payload.removedFiles = removedFiles.value;
            delete payload.files;

            // console.log(':: form.transform: payload', payload);

            return payload
        })
        .post(url, {
            onSuccess: (result) => {
                console.log(':: ON SUCCESS', result);
            },
            onError: (error) => {
                console.log(':: ON ERROR', error);
            },
            onProgress: (progress) => {
                console.log(`:: Upload status: ${progress.percentage}%`, progress);
            },
            onStart: () => {
                console.log(':: Upload started');
            }
        });
};

const handleFileUpload = (event) => {
    form.newFiles = event.target.files;
};

const removeNewFile = (index) => {
    form.newFiles.splice(index, 1);
};

const removeExistingFile = (fileId) => {
    form.files = form.files.filter(f => f.id !== fileId);
};

const triggerFileInput = () => {
    fileInput.value.click(); // Programmatically trigger the file input click
};

const handleCancel = () => {
    router.visit(route('sweepstakes.index'));
}

const handleView = () => {
    window.open(route('sweepstakes.show', { sweepstakes: props.sweepstakes.slug }));
}
</script>

<template>
    <Head title="Sweepstakes Edit" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ props.sweepstakes ? `Sweepstakes: ${props.sweepstakes.title}` : 'New Sweepstakes' }}</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
                            <div class="col-span-full">
                                <label class="block text-sm font-medium leading-6 text-gray-900">Title</label>
                                <div class="mt-2">
                                    <input type="text" name="title" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" v-model="form.title" />
                                </div>
                                <p v-if="errors.title" class="mt-2 text-red-500">{{ errors.title }}</p>
                            </div>

                            <div class="col-span-full">
                                <label class="block text-sm font-medium leading-6 text-gray-900">Slug</label>
                                <div class="mt-2">
                                    <input type="text" name="slug" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" v-model="form.slug" />
                                </div>
                                <p v-if="errors.slug" class="mt-2 text-red-500">{{ errors.slug }}</p>
                            </div>

                            <div class="col-span-full">
                                <label class="block text-sm font-medium leading-6 text-gray-900">Description</label>
                                <div class="mt-2">
                                    <textarea name="description" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" v-model="form.description" />
                                </div>
                                <p class="mt-3 text-sm leading-6 text-gray-600">Tell potential participants about the prize and why is this sweepstakes running.</p>
                                <p v-if="errors.description" class="mt-2 text-red-500">{{ errors.description }}</p>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium leading-6 text-gray-900">Draw Time</label>
                                <div class="mt-2">
                                    <input type="datetime-local" name="draw-time" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" v-model="form.draw_time" />
                                </div>
                                <p v-if="errors.draw_time" class="mt-2 text-red-500">{{ errors.draw_time }}</p>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium leading-6 text-gray-900">Timezone</label>
                                <div class="mt-2">
                                    <BaseSelect :items="props.timezones" v-model="form.timezone" />
                                </div>
                                <p v-if="errors.timezone" class="mt-2 text-red-500">{{ errors.timezone }}</p>
                            </div>

                            <div class="sm:col-span-full">
                                <label for="last-name" class="block text-sm font-medium leading-6 text-gray-900">Winner's Prize</label>
                                <div class="mt-2 p-6 border border-gray-500 rounded-md grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">


                                    <div class="col-span-full">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Email Message</label>
                                        <div class="mt-2">
                                            <textarea name="winner_email_message" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" v-model="form.winner_email_message" />
                                        </div>
                                        <p class="mt-3 text-sm leading-6 text-gray-600">This is the text if the email message to be sent to the winner at draw time. If you need to send attachments, add them below.</p>
                                        <p v-if="errors.winner_email_message" class="mt-2 text-red-500">{{ errors.winner_email_message }}</p>
                                    </div>

                                    <div class="col-span-full">
                                        <label for="photo" class="block text-sm font-medium leading-6 text-gray-900">Add Email Attachment</label>
                                        <div class="mt-2 sm:col-span-2 sm:mt-0">
                                            <div class="mt-2 flex items-center gap-x-3">
                                                <ArrowUpOnSquareStackIcon class="h-6 w-6 text-gray-500" aria-hidden="true" />
                                                <button type="button" class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" @click="triggerFileInput">Choose Files</button>
                                                <input type="file" id="files" multiple @change="handleFileUpload" class="hidden"
                                                       accept=".jpg,.jpeg,.png,.pdf,.xls,.xlsx,.xlsm,.ppt,.pptx,.pptm,.txt,.md"
                                                       ref="fileInput">
                                            </div>
                                            <p v-if="errors.files" class="mt-2 text-red-500">{{ errors.files }}</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-row flex-wrap col-span-full gap-2">
                                        <!-- Existing files -->
                                        <div v-for="(file, index) in form.files" :key="file.id" class="bg-indigo-200 py-2 px-4 rounded-md flex gap-4 w-full">
                                            <div class="flex-1 flex flex-col">
                                                <a :href="file.path" class="flex-1">{{ file.original_name }}</a>
                                                <span class="text-xs uppercase font-semibold">Already Uploaded</span>
                                            </div>
                                            <button type="button" @click="removeExistingFile(file.id)" class="flex-shrink-0 text-red-600 border rounded-full px-4 py-1 border-red-500">Delete</button>
                                        </div>

                                        <!-- New files -->
                                        <div v-for="(file, index) in form.newFiles" :key="index" class="bg-cyan-50 border-gray-300 border py-2 px-4 rounded-md flex gap-4 w-full">
                                            <div class="flex-1 flex flex-col">
                                                <span class="flex-1">{{ file.name }}</span>
                                                <span class="text-xs uppercase font-semibold">New file to upload</span>
                                            </div>
                                            <button type="button" @click="removeNewFile(index)" class="flex-shrink-0 text-red-600 border rounded-full px-4 py-1 border-red-500">Remove</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-span-full pt-4 mt-8 flex justify-end gap-4">
                            <button type="button" class="rounded-md bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" @click="handleCancel">Cancel</button>

                            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
