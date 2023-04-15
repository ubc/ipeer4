<script>
import { mapStores } from 'pinia'
//import { useVersionStore } from '@/store/VersionStore'

export default {
  components: {
  },
  computed: {
    //...mapStores(useVersionStore)
  },
  data() {
    return {
    }
  },
  methods: {
  }
}
</script>

<template>
  <div>
    Home page
    <q-btn to="/login" label="Login" color='primary' />
  </div>
</template>

<style scoped>
</style>
