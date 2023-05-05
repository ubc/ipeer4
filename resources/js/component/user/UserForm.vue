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
      user: {
        id: 0,
        username: '',
        name: '',
        password: '',
        email: '',
      },
      isPwd: true
    }
  },
  methods: {
    onSubmit() {
      this.axios.post('/user', this.user).then((resp) => {
        this.$error.clear() // clear previous errors if any
        this.$notify.ok("User '"+ this.username +"' created!")
        this.$router.push('/admin')
      }).catch((err) => {
        this.$error.clear()
        this.$notify.err('Check user info for errors.')
        this.$error.handle(err)
      })
    },
    onReset() {
      this.user.username = ''
      this.user.name = ''
      this.user.password = ''
      this.user.email = ''
    },
  },
  mounted() {
  }
}
</script>

<template>
  <q-card>
    <q-card-section>
      <div class='text-h6'>Add User</div>
      <q-form @submit="onSubmit" @reset="onReset"
              class='column q-col-gutter-md'>
        <q-input v-model="user.username" label="Username"
          :rules="[val => !!val || 'Username is required']" />

        <q-input v-model="user.password" label="Password"
                 :rules="[val => !!val || 'Password is required']"
                 :type="isPwd ? 'password' : 'text'"
                 autocomplete='new-password'>
          <template v-slot:append>
            <q-icon
              :name="isPwd ? 'visibility' : 'visibility_off'"
              class="cursor-pointer"
              @click="isPwd = !isPwd"
            />
          </template>
        </q-input>

        <q-input v-model="user.name" label="Name" />

        <q-input v-model="user.email" type="email" label="Email" />
        <ErrorBox />

        <div class='q-pt-lg row justify-between'>
          <q-btn label="Add" type="submit" color="primary" />
          <q-btn label="Reset" type="reset" color="primary" flat class="q-ml-sm" />
        </div>
      </q-form>
    </q-card-section>
  </q-card>
</template>

<style scoped>
</style>
