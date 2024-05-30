<template>
  <img :src="'https://covers.openlibrary.org/b/isbn/'+book.ISBN+'-'+size+'.jpg'"/>
</template>
<script>
export default {
  name: 'BookCover',
  props: {
    book: {},
    size: {
        type: String,
        default: 'M',
    }
  }
}
</script>
