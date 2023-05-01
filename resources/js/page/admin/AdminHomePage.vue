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
  <q-page padding>
    Admin Home Page
    <UserList />
  </q-page>
</template>

<style scoped>
</style>
