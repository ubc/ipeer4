<script>
import { mapStores } from 'pinia'
import { useCourseUserStore } from '@/store/CourseUserStore'


export default {
  components: {
  },
  computed: {
    courseId() { return this.$route.params.courseId },
    ...mapStores(useCourseUserStore)
  },
  data() {
    return {
      columns: [
        {name: 'id', field: 'id', label: 'ID', sortable: true},
        {name: 'username', field: 'username', label: 'Username',sortable: true},
        {name: 'name', field: 'name', label: 'Name',sortable: true},
        {name: 'email', field: 'email', label: 'Email',sortable: true},
        {name: 'role_name', field: 'role_name', label: 'Role',sortable: true},
      ],
      loading: false,
      selected: [],
    }
  },
  methods: {
    // qtable event handler for paging/sorting 
    async getPage(props=null) {
      if (props) {
        this.courseUserStore.setFilter(props.filter)
        this.courseUserStore.setPagination(props.pagination)
      }
      this.loading = true
      await this.courseUserStore.getPage(this.courseId)
      this.loading = false
    },
    async removeSelected() {
      if (this.selected.length <= 0) return
      this.courseUserStore.remove(this.courseId, this.selected[0].id)
      this.$notify.ok("User '"+ this.selected[0].username +"' removed from course")
      this.selected = []
    }
  },
  mounted() {
  },
  async created() {
    await this.getPage()
  }
}
</script>

<template>
  <div>
    <q-table
        title="Enrolment"
        :rows="courseUserStore.page"
        :columns="columns"
        :rows-per-page-options='[15,30,50,100]'
        :binary-state-sort='true'
        :loading='loading'
        :filter='courseUserStore.filter'
        row-key="id"
        v-model:pagination="courseUserStore.pagination"
        selection='single'
        v-model:selected='selected'
        @request='getPage'
        no-data-label='No one enroled yet'
    >

      <template v-slot:top-left>
        <div class='col'>
          <ErrorBox class='q-mb-md' />
          <q-btn color="primary" icon='add' label="Add User"
                 :to="{ name: 'courseUserNew', params: {'courseId': courseId}}" />
          <q-btn color="negative" icon='remove' label="Remove Selected"
                 class='q-ml-md' :disabled='selected.length <= 0'
                 @click='removeSelected()' />
        </div>
      </template>

      <template v-slot:top-right>
        <q-input borderless dense debounce="1000" v-model="courseUserStore.filter"
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
