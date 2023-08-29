<script>
import { mapStores } from 'pinia'
import { useCourseRoleStore } from '@/store/CourseRoleStore'

export default {
  components: {
  },
  computed: {
    ...mapStores(useCourseRoleStore),
  },
  props: {
    label: 'Course Role',
    error: false,
    errorMessage: '',
    courseId: null,
    dense: false,
    stackLabel: false,
  },
  data() {
    return {
      courseRole: null
    }
  },
  methods: {
  },
  mounted() {
  },
  async created() {
    await this.courseRoleStore.getRoles(this.courseId)
    console.log(this.courseRoleStore.roles)
  }
}
</script>

<template>
  <q-select
      v-model='courseRole'
      :error='error'
      :error-message='errorMessage'
      :options='courseRoleStore.roles'
      option-label='display_name'
      option-value='id'
      :label='label'
      >
    <!-- pass through all slots -->
    <template v-for="(_, slot) in $slots" v-slot:[slot]="scope">
      <slot :name="slot" v-bind="scope || {}" />
    </template>
  </q-select>
</template>

<style scoped lang="scss">
</style>
