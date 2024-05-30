<template>
    <Head title="Buch hinzufügen" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Buch hinzufügen
            </h2>
        </template>

        <div v-if="added" class="bg-green-400 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Buch gespeichert:</strong>
            <span class="block sm:inline">
                "{{ added.title }}" wurde mit der ID {{ added.id }} hinzugefügt.
                <a :href="route('book.edit', added.id)">Buchdetails ansehen</a>
            </span>
        </div>

        <div class="pt-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <BreezeValidationErrors class="mb-4" />
                <div class="grid grid-cols-12">
                    <div class="col-span-9">
                        <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" @submit.prevent="submitForm">
                            <div class="mb-3">
                                <BreezeLabel for="isbn" value="Suchen nach ISBN" />
                                <BreezeInput type="text" class="mt-1 block w-full" v-model="addForm.isbn" autofocus autocomplete="isbn" ref="isbn"/>
                            </div>

                            <div v-if="isbnQueryResults" class="py-3">
                                <div v-for="sourceResult in isbnQueryResults">
                                    <div class="text-bold text-underline mb-1 text-lg"><span class="fa fa-chevron-circle-right mr-1"></span>{{ sourceResult.source }}</div>
                                    <ul class="pl-3 fa-ul">
                                        <li v-for="result in sourceResult.data" @click="setResult(result)">
                                            <span class="fa-li"><span class="fa fa-book"></span></span>
                                            <div class="partial-result" title="Als Autor(en) setzen" @click.stop="setPartial('author', result.authors.join('; '))">{{ result.authors.join('; ') }}</div>
                                            <div class="text-bold" ><span class="partial-result" title="Als Titel setzen" @click.stop="setPartial('title', result.book.title)">{{ result.book.title }}</span><span v-if="result.book.subtitle">: <span class="partial-result" title="Als Untertitel setzen" @click.stop="setPartial('subtitle', result.book.subtitle)">{{ result.book.subtitle }}</span></span></div>
                                            <div class="text-small"><span class="partial-result" title="Als Ort setzen" @click.stop="setPartial('place', result.book.place)">{{ result.book.place ? result.book.place+': ' : ''}}</span>
                                                <span class="partial-result" title="Als Verlag setzen" @click.stop="setPartial('publisher', result.book.publisher)">{{ result.book.publisher}}</span>
                                                <span class="partial-result" title="Als Jahr setzen" @click.stop="setPartial('year', result.book.year)">{{ result.book.year ? '('+result.book.year+')' : ''}}</span></div>
                                            <div class="text-small" v-if="result.book.ddc">
                                                <span class="partial-result" title="Als DDC setzen" @click.stop="setPartial('ddc', result.book.ddc)">{{ result.book.ddc }}</span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="mb-3">
                                <BreezeLabel for="author" value="Autor(en)" />
                                <BreezeInput type="text" class="mt-1 block w-full" v-model="addForm.author" autocomplete="author" placeholder="Nachname, Vorname; Nachname, Vorname; ..."/>
                            </div>
                            <div class="mb-3">
                                <BreezeLabel for="title" value="Titel" />
                                <BreezeInput type="text" class="mt-1 block w-full" v-model="addForm.title" autocomplete="title" />
                            </div>
                            <div class="mb-3">
                                <BreezeLabel for="subtitle" value="Untertitel" />
                                <BreezeInput type="text" class="mt-1 block w-full" v-model="addForm.subtitle" autocomplete="subtitle" />
                            </div>
                            <div class="mb-3">
                                <BreezeLabel for="place" value="Ort" />
                                <BreezeInput type="text" class="mt-1 block w-full" v-model="addForm.place" autocomplete="place" />
                            </div>
                            <div class="mb-3">
                                <BreezeLabel for="publisher" value="Verlag" />
                                <BreezeInput type="text" class="mt-1 block w-full" v-model="addForm.publisher" autocomplete="publisher" />
                            </div>
                            <div class="mb-3">
                                <BreezeLabel for="year" value="Jahr" />
                                <BreezeInput type="text" class="mt-1 block w-full" v-model="addForm.year" autocomplete="year" />
                            </div>
                            <div class="mb-3">
                                <DDCSearch v-model="addForm.ddc" />
                            </div>

                            <BreezeButton class="my-1" :class="{ 'opacity-25': addForm.processing }" :disabled="addForm.processing">
                                {{ searched ? 'Suchen' : 'Speichern' }}
                            </BreezeButton>
                        </form>

                    </div>
                    <div class="col-span-3 ml-3 bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                        <h3 class="font-bold">Auf der Druckliste
                            <span class="ml-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">{{ booksToPrint.length }}</span>
                        </h3>
                        <BreezeButton class="mb-3" @click="$inertia.get(route('labels.form'))">
                            Drucken
                        </BreezeButton>
                        <ol>
                            <li v-for="book in booksToPrint" class="printable-book mb-2 border-0 border-b-2" @click="editBook(book)">
                                <BookCallNumber :book="book" />
                                <div>
                                    <span class="mr-1" v-for="(author) in book.authors">{{ author.name }}</span>
                                </div>
                                <div class="font-bold">{{ book.title }}</div>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-1 pt-3">
            <form @submit.prevent="searchDDC">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-3">
                    <BreezeLabel for="ddcSearch" value="DDC durchsuchen"/>
                    <BreezeInput type="text" class="mt-1 block w-full" v-model="ddcSearch" autocomplete="ddcSearch" />
                </div>
                <BreezeButton class="my-1" :class="{ 'opacity-25': addForm.processing }" :disabled="addForm.processing" @click="searchDDC">
                    Suchen
                </BreezeButton>

            </div>
            </form>
        </div>

        <div v-if="ddcSearchResults" v-for="result in ddcSearchResults" class="py-1">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <h4>{{ result.ddc}}</h4>
                    <div class="text-bold">{{ result.title}}</div>
                    <div>{{ result.description }}</div>
                    <div class="text-small">{{ result.see_also}}</div>
                    <BreezeButton class="my-1" :class="{ 'opacity-25': addForm.processing }" :disabled="addForm.processing" @click="setDDC(result)">
                        Übernehmen
                    </BreezeButton>
                </div>
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
import vSelect from "vue-select";

import Multiselect from 'vue-multiselect';
import BookCallNumber from "@/Components/Books/BookCallNumber.vue";
import DDCSearch from "@/Components/DDC/DDCSearch.vue";
require('vue-multiselect/dist/vue-multiselect.css');
require('@fortawesome/fontawesome-free/css/all.css');

export default {
    props: ['added', 'isbn', 'booksToPrint'],
    components: {
        DDCSearch,
        BookCallNumber,
        BreezeAuthenticatedLayout,
        BreezeValidationErrors,
        BreezeLabel,
        BreezeInput,
        BreezeButton,
        Head,
        Multiselect,
    },
    created() {
        axios.get(route('api.dewey.flat')).then(response => {
            this.options = response.data;
            this.ddcLoaded = true;
        });
    },
    mounted() {
        this.$refs.isbn.$el.focus();
        if (this.isbn) this.submitForm();
    },
    watch: {
        ddcSelected: function(newDDC, oldDDC) {
                this.addForm.ddc = newDDC.ddc;
            }
        },
    data() {
        return {
            addForm: this.$inertia.form({
                isbn: this.isbn ?? '',
                author: '',
                title: '',
                subtitle: '',
                place: '',
                publisher: '',
                year: '',
                ddc: null,
            }),
            isbnQueryResults: [],
            ddcSelected: null,
            ddcSearch: '',
            ddcSearchResults: [],
            searched: false,
            ddcLoaded: false,
            tree: [],
            options: []
        }
    },
    methods: {
        editBook(book) {
            this.$inertia.get(route('book.edit', book.id));
        },
        submitForm() {
            if (this.addForm.isbn && (!this.addForm.author) && (!this.addForm.title)) {
                // isbn search!
                axios.post(route('query', this.addForm.isbn)).then(response => {
                    if (response.data) {
                        this.isbnQueryResults = response.data;
                        if ((this.isbnQueryResults[0] != undefined) && (this.isbnQueryResults[0].data[0] != undefined)) {
                            this.setResult(this.isbnQueryResults[0].data[0]);
                        } else {
                            alert('Es wurden keine Resultate für ISBN '+this.addForm.isbn+' gefunden.');
                        }
                    } else {
                        alert('Es wurden keine Resultate für ISBN '+this.addForm.isbn+' gefunden.');
                    }
                });
            } else {
                /**
                this.addForm.post(route('book.store'), {
                    onFinish: () => this.addForm.reset(),
                });
                 */
                this.$inertia.post(route('book.store'), this.addForm, { preserveState: false});
            }
        },
        setResult(result) {
            this.addForm.author = result.authors.join('; ');
            this.addForm.title = result.book.title;
            this.addForm.subtitle = result.book.subtitle;
            this.addForm.place = result.book.place;
            this.addForm.publisher = result.book.publisher;
            this.addForm.year = result.book.year;
            this.addForm.ddc = result.book.ddc;
        },
        customLabel({ddc, title}) {
            return `${ddc} ${title}`.trim();
        },
        searchDDC() {
            axios.get(route('api.dewey.search', this.ddcSearch)).then(result => {
                this.ddcSearchResults = result.data;
            })
        },
        setDDC(result) {
            this.ddcSelected = result;
            this.addForm.ddc = result.ddc;
        },
        setPartial(key, value) {
            this.addForm[key] = value;
        }
    }
}
</script>
<style scoped>
    ul.fa-ul li:hover {
        background-color: rgb(129, 140, 248);
        cursor: pointer;
    }

    ul.fa-ul li:hover .partial-result:hover {
        color: white;
        cursor: pointer;
    }

    .printable-book:hover {
        cursor: pointer;
        background-color: lightblue;
    }
</style>
