<script>
import { mapStores } from 'pinia'
//import { useVersionStore } from '@/store/VersionStore'

export default {
  components: {
  },
  computed: {
    //...mapStores(useVersionStore)
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
      loading: false,
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
      this.loading = true
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
          // TODO: real error message
          console.log(err)
        })
        .finally(() => {
          this.loading = false
        })
    },
    // called by qtable to load other pages
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
    <pre>
    {{ pagination }}
    </pre>
    <q-table
        title="Users"
        :rows="users.data"
        :columns="columns"
        :rows-per-page-options='[15,30,50,100]'
        :loading='loading'
        v-model:pagination="pagination"
        @request='updateUsers'
        :binary-state-sort='true'
        row-key="id"
    />
  </div>
</template>

<style scoped>
</style>
