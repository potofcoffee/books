<template>
    <Head :title="'Buchdetails zu '+book.title" />

    <BreezeAuthenticatedLayout>
            <template #header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ book.title }}
                </h2>
            </template>

            <div class="mb-1">
                <span class="mr-1" v-for="(author) in book.authors">{{ author.name }}</span>
            </div>
            <div class="mb-4" v-if="book.publisher">
                {{ book.publisher }} <span v-if="book.year">({{ book.year }})</span>
            </div>
            <div class="mb-4">
                {{ book.ddc }}
            </div>
            <div class="mb-4" v-if="book.ISBN">
                <BookCover :book="book"/>
            </div>
            <div class="mb-4">
                <BreezeButton @click="deleteBook">Löschen</BreezeButton>
            </div>


    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';
import BookCover from "@/Components/Books/BookCover";
import BreezeButton from '@/Components/Button.vue'


export default {
    components: {
        BookCover,
        BreezeAuthenticatedLayout,
        BreezeButton,
        Head,
    },
    name: "Show",
    props: ['book'],
    methods: {
        deleteBook() {
            this.$inertia.delete('/book/'+this.book.id);
        }
    }
}
</script>

