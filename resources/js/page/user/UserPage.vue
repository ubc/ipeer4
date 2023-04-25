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
    <router-view></router-view>
  </div>
</template>

<style scoped>
</style>
