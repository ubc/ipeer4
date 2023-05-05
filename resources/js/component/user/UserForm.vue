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
      showPassword: false,
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
        <q-input v-model="user.username" label="Username" bottom-slots
                 :error="'username' in $error.fields"
                 :error-message='$error.fields.username' />

        <q-input v-model="user.password" label="Password" class='col-grow'
                 :type="showPassword ? 'text' : 'password'"
                 autocomplete='new-password'
                 :error="'password' in $error.fields"
                 :error-message='$error.fields.password' >
         <template v-slot:after>
           <q-toggle
               size='xl'
               v-model="showPassword"
               color='warning'
               checked-icon='visibility'
               unchecked-icon='visibility_off'
               />
         </template>
        </q-input>

        <q-input v-model="user.name" label="Name"
                 :error="'name' in $error.fields"
                 :error-message='$error.fields.name' />

        <q-input v-model="user.email" label="Email" bottom-slots
                 :error="'email' in $error.fields"
                 :error-message='$error.fields.email' />

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
