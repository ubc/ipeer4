<script>
import { mapStores } from 'pinia'
import { useUserStore } from '@/store/UserStore'


export default {
  components: {
  },
  computed: {
    ...mapStores(useUserStore)
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
      filter: '',
      loading: false,
      pagination: {},
    }
  },
  methods: {
    // qtable event handler for paging/sorting 
    async getUsers(props=null) {
      if (props) {
        this.userStore.setFilter(props.filter)
        this.userStore.setPagination(props.pagination)
      }
      this.loading = true
      this.$error.clear()
      try {
        await this.userStore.getPage()
      } catch(err) {
        this.$error.handle(err)
      }
      this.loading = false
    },
    // qtable event handler for clicking on a user row
    showUser(ev, row, index) {
      this.$router.push({name: 'userInfo', params: { id: row.id}})
    },
  },
  mounted() {
  },
  async created() {
    await this.getUsers()
  }
}
</script>

<template>
  <div>
    <q-table
        title="Users"
        :rows="userStore.users"
        :columns="columns"
        :rows-per-page-options='[15,30,50,100]'
        :binary-state-sort='true'
        :loading='loading'
        :filter='filter'
        row-key="id"
        v-model:pagination="userStore.pagination"
        @request='getUsers'
        @row-click='showUser'
    >

      <template v-slot:top-left>
        <div class='col'>
          <ErrorBox class='q-mb-md' />
          <q-btn color="primary" icon='add' label="Add User" to='/user/new' />
        </div>
      </template>

      <template v-slot:top-right>
        <q-input borderless dense debounce="1000" v-model="filter"
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
