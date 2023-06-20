<script>
import { mapStores } from 'pinia'
import { useCourseStore } from '@/store/CourseStore'

export default {
  components: {
  },
  computed: {
    courseId() { return this.$route.params.courseId },
    isEdit() { if (this.courseId) return true; return false },
    actionVerb() { if (this.isEdit) return 'Edit'; return 'Add' },
    ...mapStores(useCourseStore)
  },
  data() {
    return {
      course: {
        coursename: '',
        name: '',
        password: '',
        email: '',
      },
      showPassword: false,
      isLoading: false,
    }
  },
  methods: {
    async onSubmit() {
      this.isLoading = true
      try {
        if (this.isEdit) { // edit existing course
          this.course = await this.courseStore.edit(this.course)
          this.$notify.ok("Course '"+ this.course.name +"' updated!")
        }
        else { // create new course
          let resp = await this.courseStore.create(this.course)
          this.$notify.ok("Course '"+ resp.name +"' created!")
          this.$router.back()
        }
      } catch(err) {
        this.isLoading = false
        this.$notify.err('Check fields for issues.')
        throw err
      }
      this.isLoading = false
    },
    onReset() {
      this.course.coursename = ''
      this.course.name = ''
      this.course.password = ''
      this.course.email = ''
      this.$error.clearAll()
    },
  },
  mounted() {
  },
  async created() {
    if (this.isEdit) {
      this.course = await this.courseStore.get(this.courseId)
    }
  }
}
</script>

<template>
  <q-card>
    <q-card-section>
      <div class='text-h6'>{{ actionVerb }} Course</div>
      <q-form @submit="onSubmit" @reset="onReset"
              class='column q-col-gutter-md'>

        <q-input v-model="course.name" label="Name"
                 :error="'name' in $error.fields"
                 :error-message='$error.fields.name' />

        <ErrorBox />

        <div class='row reverse-sm justify-between'>
          <q-btn label="Reset" type="reset" color="primary" flat class="q-ml-sm"
                 :disable='isLoading' />
          <div>
            <q-btn label="Save" type="submit" color="primary q-mr-md"
                   :loading='isLoading'/>
            <q-btn color="secondary" icon='arrow_back' label="Back"
                   @click="$router.back()" />
          </div>
        </div>
      </q-form>
    </q-card-section>
  </q-card>
</template>

<style scoped>
</style>
