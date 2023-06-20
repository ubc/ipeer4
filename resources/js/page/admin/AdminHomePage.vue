<script>
import { mapStores } from 'pinia'

import CourseList from '@/component/course/CourseList.vue'
import UserList from '@/component/user/UserList.vue'

export default {
  components: {
    CourseList,
    UserList
  },
  computed: {
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
    <CourseList />
    <q-separator spaced inset />
    <UserList />
  </q-page>
</template>

<style scoped>
</style>
