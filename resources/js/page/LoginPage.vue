<script>
import PasswordInput from '@/component/input/PasswordInput.vue'

export default {
  components: {
    PasswordInput,
  },
  computed: {
  },
  data() {
    return {
      username: '',
      password: '',
      isLoading: false,
    }
  },
  methods: {
    onReset() {
      this.username = ''
      this.password = ''
    },
    async onSubmit() {
      this.isLoading = true
      try {
        await this.$axios.post('/login', {username: this.username,
                                         password: this.password})
        this.$router.push('/admin')
      } catch (err) {
        this.isLoading = false
        this.$notify.err('Check your username and password')
        throw err
      }
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

        <PasswordInput v-model="password" label="Password"
                       :is-new-password='false'
                       :error="'password' in $error.fields"
                       :error-message='$error.fields.password' />

        <ErrorBox />

        <div class='q-pt-lg row justify-between'>
          <q-btn label="Login" type="submit" color="primary"
            :loading='isLoading' />
          <q-btn label="Reset" type="reset" color="primary" :disable='isLoading'
            flat class="q-ml-sm" />
        </div>
      </q-form>
    </div>
  </q-page>
</template>

<style scoped>
</style>
