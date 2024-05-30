<template>
    <Head title="Büchertheke" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Theke
            </h2>
        </template>


        <div class="py-12">
            <counter :count="bookCount" label="Bücher" />
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <BreezeValidationErrors class="mb-4" />
                <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" @submit.prevent="submitISBN">
                    <div class="mb-3">
                        <BreezeLabel for="isbn" value="Suchen nach ISBN" />
                        <BreezeInput type="text" class="mt-1 block w-full" v-model="isbnForm.isbn" autofocus autocomplete="isbn" />
                    </div>
                    <div class="mb-3">
                        <BreezeLabel for="author" value="Suchen nach Autor" />
                        <BreezeInput type="text" class="mt-1 block w-full" v-model="isbnForm.author" autocomplete="author" />
                    </div>
                    <div class="mb-3">
                        <BreezeLabel for="title" value="Suchen nach Titel" />
                        <BreezeInput type="text" class="mt-1 block w-full" v-model="isbnForm.title" autocomplete="title" />
                    </div>
                    <BreezeButton class="my-1" :class="{ 'opacity-25': isbnForm.processing }" :disabled="isbnForm.processing">
                        Suchen
                    </BreezeButton>
                </form>
            </div>
        </div>

    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue'
import BreezeValidationErrors from '@/Components/ValidationErrors.vue'
import BreezeInput from '@/Components/Input.vue'
import BreezeLabel from '@/Components/Label.vue'
import BreezeButton from '@/Components/Button.vue'

import { Head } from '@inertiajs/inertia-vue3';
import Counter from "@/Components/Counter.vue";

export default {
    props: ['bookCount'],
    components: {
        Counter,
        BreezeAuthenticatedLayout,
        BreezeValidationErrors,
        BreezeLabel,
        BreezeInput,
        BreezeButton,
        Head,
    },
    data() {
        return {
            isbnForm: this.$inertia.form({
                isbn: '',
                author: '',
                title: '',
            }),
        }
    },
    methods: {
        submitISBN() {
            this.isbnForm.post(route('search'));
        },
    }
}
</script>
