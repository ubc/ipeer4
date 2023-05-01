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
  <q-page padding>
    <div class='row justify-evenly'>
      <q-btn to="/login" label="Login" color='primary' />
      <q-btn to="/register" label="Register" color='primary' />
    </div>
  </q-page>
</template>

<style scoped>
</style>
