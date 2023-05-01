<script>
import notify from '@/plugin/notify'
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
      password: '',
      isPwd: true,
    }
  },
  methods: {
    onReset() {
      this.username = ''
      this.password = ''
    },
    onSubmit() {
      this.axios.post('/login', {
        username: this.username,
        password: this.password,
      }).then((resp) => {
        notify.ok('Login Successful')
        this.$router.push('/admin')
      }).catch((err) => {
        notify.err('Login Failed')
      })
    },
  },
  mounted() {
  }
}
</script>

<template>
  <q-page padding class='row justify-center'>
    <div class='column col-xs-12 col-sm-8 col-md-6 col-lg-4'>
      <h4 class='q-mt-none q-mb-sm'>Login</h4>
      <q-form @submit="onSubmit" @reset="onReset"
              class='column q-col-gutter-md'>
        <q-input v-model="username" label="Username"
          :rules="[val => !!val || 'Username is required']" />

        <q-input v-model="password" label="Password"
                 :rules="[val => !!val || 'Password is required']"
                 :type="isPwd ? 'password' : 'text'"
                 autocomplete='current-password'>
          <template v-slot:append>
            <q-icon
              :name="isPwd ? 'visibility' : 'visibility_off' "
              class="cursor-pointer"
              @click="isPwd = !isPwd"
            />
          </template>
        </q-input>

        <div class='q-pt-lg row justify-between'>
          <q-btn label="Login" type="submit" color="primary" />
          <q-btn label="Reset" type="reset" color="primary" flat class="q-ml-sm" />
        </div>
      </q-form>
    </div>
  </q-page>
</template>

<style scoped>
</style>
