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
      username: '',
      name: '',
      email: '',
      password: '',
      isPwd: true,
    }
  },
  methods: {
    onSubmit() {
      console.log("Submit")
      this.axios.post('/register', {
        username: this.username,
        name: this.name,
        email: this.email,
        password: this.password
      }).then((resp) => {
        this.$q.notify({
          type: 'positive',
          message: 'User created successfully, please log in'
        })
        this.$router.push('/login')
      }).catch((err) => {
        console.log(err)
        this.$q.notify({
          type: 'negative',
          message: 'Failed to create user: ' + err.message
        })
      })
    },
    onReset() {
      this.username = ''
      this.password = ''
      this.name = ''
      this.email = ''
    },
  }
}
</script>

<template>
  <div class='row justify-center'>
    <div class='column col-xs-12 col-sm-8 col-md-6 col-lg-4'>
      <h4 class='q-mt-none q-mb-sm'>Register User</h4>
      <q-form @submit="onSubmit" @reset="onReset"
              class='column q-col-gutter-md'>
        <q-input v-model="username" label="Username"
          :rules="[val => !!val || 'Username is required']" />

        <q-input v-model="password" label="Password"
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

        <q-input v-model="name" label="Name" />

        <q-input v-model="email" type="email" label="Email" />

        <div class='q-pt-lg row justify-between'>
          <q-btn label="Register" type="submit" color="primary" />
          <q-btn label="Reset" type="reset" color="primary" flat class="q-ml-sm" />
        </div>
      </q-form>
    </div>
  </div>
</template>

<style scoped>
</style>
