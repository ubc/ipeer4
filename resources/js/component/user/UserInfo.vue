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
      user: {},
      userId: this.$route.params.id,
      confirmDelete: false,
    }
  },
  methods: {
    back() {
      this.$router.back()
    },
    async getUser() {
      this.user = await this.userStore.getUser(this.userId)
    },
    async deleteUser() {
      await this.userStore.deleteUser(this.userId)
      this.$notify.ok('User "' + this.user.username + '" deleted.')
    },
    editUser() {
      this.$router.push({ name: 'userEdit', params: { id: this.userId } })
    }
  },
  mounted() {
  },
  async created() {
    await this.getUser()
  }
}
</script>

<template>
  <div>
    
    <q-card>
      <q-card-section>
        <ErrorBox />
        <div class="text-h6">{{ user.username }}</div>
      </q-card-section>

      <q-separator />

      <q-card-section>
          <table>
            <tr>
              <td class='text-right'>ID:</td>
              <td class='text-body1 q-pl-md'>{{ user.id }}</td>
            </tr>
            <tr>
              <td class='text-right'>Username:</td>
              <td class='text-body1 q-pl-md'>{{ user.username }}</td>
            </tr>
            <tr>
              <td class='text-right'>Name:</td>
              <td class='text-body1 q-pl-md'>{{ user.name }}</td>
            </tr>
            <tr>
              <td class='text-right'>Email:</td>
              <td class='text-body1 q-pl-md'>{{ user.email }}</td>
            </tr>
            <tr>
              <td class='text-right'>Updated:</td>
              <td class='text-body1 q-pl-md'>{{ user.updated_at }}</td>
            </tr>
            <tr>
              <td class='text-right'>Created:</td>
              <td class='text-body1 q-pl-md'>{{ user.created_at }}</td>
            </tr>
          </table>
      </q-card-section>

      <q-card-section class='row reverse-sm justify-between'>
        <q-btn flat color="negative" label="Delete" @click="confirmDelete=true" />
        <div>
          <q-btn color="primary" icon='edit' label="Edit" @click="editUser" 
            class='q-mr-md' />
          <q-btn color="secondary" icon='arrow_back' label="Back" @click="back" />
        </div>
      </q-card-section>
    </q-card>

    <q-dialog v-model="confirmDelete">
      <q-card>
        <q-card-section class="row items-center">
          <span class="text-negative">Delete user '{{ user.username }}'?</span>
        </q-card-section>

        <q-card-actions class='row justify-between'>
          <q-btn label="Yes" icon='delete' color="negative" v-close-popup @click='deleteUser' />
          <q-btn label="No" icon='cancel' color="secondary" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<style scoped lang="scss">
</style>
