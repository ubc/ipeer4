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
  <div class='row justify-evenly'>
    <q-btn to="/login" label="Login" color='primary' />
    <q-btn to="/register" label="Register" color='primary' />
  </div>
</template>

<style scoped>
</style>
