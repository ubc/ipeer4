<script>
import { mapStores } from 'pinia'
import { useErrorStore } from '@/store/ErrorStore'
import ErrorBox from '@/component/ErrorBox.vue'


export default {
  components: {
    ErrorBox,
  },
  computed: {
    ...mapStores(useErrorStore)
  },
  data() {
    return {
      columns: [
        {name: 'id', field: 'id', label: 'ID', sortable: true},
        {name: 'username', field: 'username', label: 'Username',sortable: true},
        {name: 'name', field: 'name', label: 'Name', sortable: true},
        {name: 'email', field: 'email', label: 'Email', sortable: true},
        {name: 'created_at', field: 'created_at', label: 'Created',
         sortable: true},
        {name: 'updated_at', field: 'updated_at', label: 'Updated',
         sortable: true},
      ],
      pagination: {
        sortBy: 'id',
        descending: false,
        page: 1,
        rowsPerPage: 10,
        rowsNumber: 0, // total entries
      },
      users: {},
    }
  },
  methods: {
    getUsers(url='user') {
      this.axios.get(url)
        .then((response) => {
          this.users = response.data
          this.pagination.page = response.data.current_page
          this.pagination.rowsPerPage = response.data.per_page
          this.pagination.rowsNumber = response.data.total
          this.pagination.sortBy = response.data.sort_by
          this.pagination.descending = response.data.descending
        })
        .catch((err) => {
          this.errorStore.handle(err)
        })
    },
    // qtable event handler for clicking on a user row
    showUser(ev, row, index) {
      this.$router.push({name: 'userInfo', params: { id: row.id}})
    },
    // qtable event handler for paging/sorting 
    updateUsers(props) {
      const { page, rowsPerPage, sortBy, descending } = props.pagination
      const params = new URLSearchParams({
        page: page,
        per_page: rowsPerPage,
        sort_by: sortBy,
        descending: descending,
      });
      const url = 'user?' + params.toString(); 
      this.getUsers(url);
    },
  },
  mounted() {
    this.getUsers()
  }
}
</script>

<template>
  <div>
    <q-table
        title="Users"
        :rows="users.data"
        :columns="columns"
        :rows-per-page-options='[15,30,50,100]'
        :binary-state-sort='true'
        row-key="id"
        v-model:pagination="pagination"
        @request='updateUsers'
        @row-click='showUser'
    >
      <template v-slot:top-left>
        <div class='col'>
          <ErrorBox class='q-mb-md' />
          <q-btn color="primary" icon='add' label="Add User" @click="addUser" 
            to='/user/new' />
        </div>
      </template>
    </q-table>
  </div>
</template>

<style scoped lang="scss">
</style>
