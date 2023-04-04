<script>
export default {
  data() {
    return {
      count: 0
    }
  }
}
</script>

<template>
  <button class='text-4xl' @click="count++">Count is: {{ count }}</button>
</template>

<style scoped>
</style>
