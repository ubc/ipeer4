<script>
import { mapStores } from 'pinia'
import { useCourseStore } from '@/store/CourseStore'

export default {
  components: {
  },
  computed: {
    ...mapStores(useCourseStore)
  },
  data() {
    return {
      course: {},
      courseId: this.$route.params.courseId,
      confirmDelete: false,
    }
  },
  methods: {
    back() {
      this.$router.back()
    },
    async getCourse() {
      this.course = await this.courseStore.get(this.courseId)
    },
    async deleteCourse() {
      await this.courseStore.remove(this.courseId)
      this.$notify.ok('Course "' + this.course.name + '" deleted.')
      this.$router.back()
    },
    editCourse() {
      this.$router.push({name: 'courseEdit', params: {courseId: this.courseId}})
    }
  },
  mounted() {
  },
  async created() {
    await this.getCourse()
  }
}
</script>

<template>
  <div>
    <q-card>
      <q-card-section>
        <ErrorBox />
        <div class="text-h6">{{ course.name }}</div>
      </q-card-section>

      <q-separator />

      <q-card-section>
          <table>
            <tr>
              <td class='text-right'>ID:</td>
              <td class='text-body1 q-pl-md'>{{ course.id }}</td>
            </tr>
            <tr>
              <td class='text-right'>Name:</td>
              <td class='text-body1 q-pl-md'>{{ course.name }}</td>
            </tr>
            <tr>
              <td class='text-right'>Updated:</td>
              <td class='text-body1 q-pl-md'>{{ course.updated_at }}</td>
            </tr>
            <tr>
              <td class='text-right'>Created:</td>
              <td class='text-body1 q-pl-md'>{{ course.created_at }}</td>
            </tr>
          </table>
      </q-card-section>

      <q-card-section class='row reverse-sm justify-between'>
        <q-btn flat color="negative" label="Delete" @click="confirmDelete=true" />
        <div>
          <q-btn color="primary" icon='edit' label="Edit" @click="editCourse"
            class='q-mr-md' />
          <q-btn color="secondary" icon='arrow_back' label="Back" @click="back" />
        </div>
      </q-card-section>
    </q-card>

    <q-dialog v-model="confirmDelete">
      <q-card>
        <q-card-section class="row items-center">
          <span class="text-negative">Delete course '{{ course.coursename }}'?</span>
        </q-card-section>

        <q-card-actions class='row justify-between'>
          <q-btn label="Yes" icon='delete' color="negative" v-close-popup @click='deleteCourse' />
          <q-btn label="No" icon='cancel' color="secondary" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<style scoped lang="scss">
</style>
