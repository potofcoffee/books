<template>
    <Head title="Etiketten drucken"/>

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Etiketten drucken
            </h2>
        </template>

        <div class="pt-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" @submit.prevent="submitForm">
                    <h3 class="text-bold mb-3">Etikettendruck</h3>
                    <div class="text-center">
                        <table class="table-fixed border-collapse border border-gray-500" width="50%">
                            <thead>
                            </thead>
                            <tbody>
                            <tr v-for="i in 8">
                                <td v-for="j in 3" class="border border-gray-500" width="33%">
                                    <input type="radio" :value="((i-1)*3)+(j-1)" name="skipLabels" v-model="skipLabels"
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    Hier beginnen
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <BreezeButton class="my-1 mr-1" @click.prevent="submitForm">Drucken</BreezeButton>
                    <BreezeButton class="my-1" @click.prevent="setPrinted">Als gedruckt markieren</BreezeButton>
                </form>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Head} from '@inertiajs/inertia-vue3';
import BookCover from "@/Components/Books/BookCover";
import BreezeButton from '@/Components/Button.vue'

export default {
    components: {
        BookCover,
        BreezeAuthenticatedLayout,
        BreezeButton,
        Head,
    },
    name: "Form",
    props: ['books'],
    data() {
        return {
            skipLabels: 0,
        }
    },
    methods: {
        submitForm() {
            window.location.href = route('labels.print', this.skipLabels);
        },
        setPrinted() {
            this.$inertia.post(route('labels.printed', this.skipLabels));
        }
    }
}
</script>
<style scoped>
.table-fixed td {
    height: 5em;
    vertical-align: middle;
    text-align: center;
}
</style>

