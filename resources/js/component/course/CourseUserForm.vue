<script>
import { mapStores } from 'pinia'
import { useCourseUserStore } from '@/store/CourseUserStore'
import { useUserSearchStore } from '@/store/UserSearchStore'
import CourseRoleSelect from '@/component/input/CourseRoleSelect.vue'


export default {
  components: {
    CourseRoleSelect,
  },
  computed: {
    courseId() { return this.$route.params.courseId },
    ...mapStores(useCourseUserStore),
    ...mapStores(useUserSearchStore)
  },
  data() {
    return {
      columns: [
        {name: 'id', field: 'id', label: 'ID', sortable: true},
        {name: 'username', field: 'username', label: 'Username',sortable: true},
        {name: 'name', field: 'name', label: 'Name', sortable: true},
        {name: 'email', field: 'email', label: 'Email', sortable: true},
      ],
      visibleColumns: ['username', 'name', 'email'],
      loading: false,
      selected: [],
      courseRole: null
    }
  },
  methods: {
    async getUsers(props=null) {
      let hasFilter = false
      if (props) {
        if (props.filter) {
          hasFilter = true
          this.userSearchStore.setFilter(props.filter)
        }
        this.userSearchStore.setPagination(props.pagination)
      }
      if (hasFilter) {
        this.loading = true
        await this.userSearchStore.getPage()
        this.loading = false
      }
      else {
        // we only want to show search results, so if there's no search term,
        // clear everything
        this.userSearchStore.resetPagination()
      }
    },
    async addSelected() {
      if (this.selected.length <= 0) return
      let userIds = []
      for (const selected of this.selected) {
        userIds.push(selected.id)
      }
      this.courseUserStore.create(this.courseId, userIds, this.courseRole.id)
      this.$notify.ok("User '"+ this.selected[0].username +"' added to course")
    }
  },
  mounted() {
    this.userSearchStore.resetPagination()
  }
}
</script>

<template>
  <div>
    <h6>Add User to Course</h6>
    <ErrorBox />
    <q-table
        title="Users"
        :rows="userSearchStore.page"
        :columns="columns"
        :visible-columns='visibleColumns'
        :rows-per-page-options='[15,30,50,100]'
        :binary-state-sort='true'
        :loading='loading'
        :filter='userSearchStore.filter'
        selection='single'
        row-key="id"
        v-model:pagination="userSearchStore.pagination"
        v-model:selected="selected"
        @request='getUsers'
        no-data-label='Enter a search term'
        no-results-label='No matching users found'
    >

      <template v-slot:top-left>
        <div class='col'>
          <ErrorBox class='q-mb-md' />
          <p class='text-h6'>Add selected user with role:</p>
          <CourseRoleSelect :course-id='courseId' v-model='courseRole'
                            label='Role' class='col-4'>
            <template v-slot:after>
              <q-btn color="primary" icon='add' label="Add"
                     :disabled='selected.length <= 0'
                     @click='addSelected()' />
            </template>
          </CourseRoleSelect>
        </div>
      </template>

      <template v-slot:top-right>
        <q-input borderless dense debounce="1000" v-model="userSearchStore.filter"
          placeholder="Search" autofocus>
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </template>

    </q-table>

    <q-btn color="secondary" icon='arrow_back' label="Back" class='q-mt-md'
           @click="$router.back()" />

  </div>
</template>

<style scoped lang="scss">
</style>
