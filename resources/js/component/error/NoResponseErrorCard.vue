<script>
import ErrorCard from '@/component/error/ErrorCard.vue'

export default {
  components: {
    ErrorCard,
  },
  computed: {
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
  <ErrorCard title='Site is Not Responding' :timestamp='error.timestamp' />
</template>

<style scoped lang="scss">
</style>
