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
      columns: [
        {name: 'id', field: 'id', label: 'ID', sortable: true},
        {name: 'name', field: 'name', label: 'Name',sortable: true},
        {name: 'created_at', field: 'created_at', label: 'Created',
         sortable: true},
        {name: 'updated_at', field: 'updated_at', label: 'Updated',
         sortable: true},
      ],
      loading: false,
    }
  },
  methods: {
    // qtable event handler for paging/sorting 
    async getCourses(props=null) {
      if (props) {
        this.courseStore.setFilter(props.filter)
        this.courseStore.setPagination(props.pagination)
      }
      this.loading = true
      await this.courseStore.getPage()
      this.loading = false
    },
    // qtable event handler for clicking on a course row
    showCourse(ev, row, index) {
      this.$router.push({name: 'courseInfo', params: { courseId: row.id}})
    },
  },
  mounted() {
  },
  async created() {
    await this.getCourses()
  }
}
</script>

<template>
  <div>
    <q-table
        title="Courses"
        :rows="courseStore.page"
        :columns="columns"
        :rows-per-page-options='[15,30,50,100]'
        :binary-state-sort='true'
        :loading='loading'
        :filter='courseStore.filter'
        row-key="id"
        v-model:pagination="courseStore.pagination"
        @request='getCourses'
        @row-click='showCourse'
    >

      <template v-slot:top-left>
        <div class='col'>
          <ErrorBox class='q-mb-md' />
          <q-btn color="primary" icon='add' label="Add Course" to='/course/new' />
        </div>
      </template>

      <template v-slot:top-right>
        <q-input borderless dense debounce="1000" v-model="courseStore.filter"
          placeholder="Search">
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </template>

    </q-table>
  </div>
</template>

<style scoped lang="scss">
</style>
