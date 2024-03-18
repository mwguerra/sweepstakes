<script setup>
import axios from 'axios'
import { router, Head } from '@inertiajs/vue3'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps(['sweepstakes'])

const handleDelete = async (id) => {
    try {
        await axios.post(route('sweepstakes.destroy', { sweepstakes: slug }))
    } catch (error) {
        console.error('An error occurred while trying to delete the sweepstake.', error)
    }
}

const handleView = (slug) => {
    router.visit(route('sweepstakes.show', { sweepstakes: slug }))
}

const handleEdit = (slug) => {
    router.visit(route('sweepstakes.edit', { sweepstakes: slug }))
}

const handleCreate = () => {
    router.visit(route('sweepstakes.create'))
}
</script>

<template>
    <Head title="Sweepstakes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Sweepstakes</h2>
                <button type="button" class="rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600" @click="handleCreate">Add Sweepstakes</button>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="mt-8 flow-root">
                        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead>
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Title</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Draw Time</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Result</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                    <tr v-for="event in props.sweepstakes" :key="event.id">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">{{ event.title }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ new Date(event.draw_time_raw).toLocaleString() }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ event.is_over }}</td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0 inline-flex gap-2">
                                            <a href="#" class="text-indigo-600 hover:text-indigo-900" @click.prevent="handleView(event.slug)">
                                                View<span class="sr-only">, {{ event.name }}</span>
                                            </a>
                                            <a href="#" class="text-indigo-600 hover:text-indigo-900" @click.prevent="handleEdit(event.slug)">
                                                Edit<span class="sr-only">, {{ event.name }}</span>
                                            </a>
                                            <a href="#" class="text-indigo-600 hover:text-indigo-900" @click.prevent="handleDelete(event.slug)">
                                                Delete<span class="sr-only">, {{ event.name }}</span>
                                            </a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>

</template>
