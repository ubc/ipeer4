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
    <h4>Admin Home</h4>
    <h5>Courses</h5>
    <CourseList />
    <h5>Users</h5>
    <UserList />
  </q-page>
</template>

<style scoped>
</style>
