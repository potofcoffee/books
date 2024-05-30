<template>
    <Head :title="Suchergebnisse" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Suchergebnisse
            </h2>
        </template>

        <div v-for="book in books" class="grid grid-cols-12 gap-4 border-b-2">
            <div class="col-span-3 px-4">
                <BookCover :book="book" class="p-1"/>
            </div>
            <div class="col-span-7">
                <div class="mb-1">
                    <span class="mr-1" v-for="(author) in book.authors">{{ author.name }}</span>
                </div>
                <div class="mb-4">
                    <h3 class="text-bold text-lg">{{ book.title }}</h3>
                </div>
                <div class="mb-4" v-if="book.publisher">
                    {{ book.publisher }} <span v-if="book.year">({{ book.year }})</span>
                </div>
                <div class="mb-4">
                    {{ book.ddc }}
                </div>
            </div>
            <div class="col-span-2 text-right">
                <BreezeButton class="mr-4 mb-1" @click="editBook(book)">Bearbeiten</BreezeButton>
                <BreezeButton class="mr-4" @click="deleteBook(book)">Löschen</BreezeButton>
            </div>
        </div>


    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import BookCover from '@/Components/Books/BookCover.vue';
import { Head } from '@inertiajs/inertia-vue3';
import BreezeButton from '@/Components/Button.vue'

export default {
    components: {
        BreezeAuthenticatedLayout,
        BookCover,
        BreezeButton,
        Head,
    },
    name: "Search",
    props: ['books'],
    methods: {
        deleteBook(book) {
            this.$inertia.delete('/book/'+book.id);
        },
        editBook(book) {
            this.$inertia.get('/book/'+book.id);
        }
    }
}
</script>

<style scoped>

</style>
