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
      user: {}
    }
  },
  methods: {
    getUser() {
      this.axios.get('user/' + this.$route.params.id)
        .then((response) => {
          console.log(response.data)
          this.user = response.data
        })
        .catch((err) => {
          // TODO: real error message
          console.log(err)
        })
    }
  },
  mounted() {
    this.getUser()
  }
}
</script>

<template>
  <div>
    
    <q-card>
      <q-card-section>
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
    </q-card>
  </div>
</template>

<style scoped lang="scss">
</style>
