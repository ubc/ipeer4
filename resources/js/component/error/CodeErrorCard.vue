<script>
import ErrorCard from '@/component/error/ErrorCard.vue'

export default {
  components: {
    ErrorCard,
  },
  computed: {
    //...mapStores(useErrorStore)
  },
  props: ['error'],
  data() {
    return {
    }
  },
  methods: {
  },
  mounted() {
  }
}
</script>

<template>
  <ErrorCard :title='"Buggy Code: " + error.name + "-" + error.message'
             :timestamp='error.timestamp'>
    <q-scroll-area class='stacktrace'>{{ error.stack }}</q-scroll-area>
  </ErrorCard>
</template>

<style scoped lang="scss">
.stacktrace {
  white-space: pre;
  height: 12em;
}
</style>
