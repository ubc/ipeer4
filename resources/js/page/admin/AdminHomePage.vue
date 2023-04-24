<script>
import { mapStores } from 'pinia'
//import { useVersionStore } from '@/store/VersionStore'
import UserList from '@/component/user/UserList.vue'

export default {
  components: {
    UserList
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
    Admin Home Page
    <UserList />
  </div>
</template>

<style scoped>
</style>
