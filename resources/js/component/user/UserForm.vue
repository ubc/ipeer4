<script>
import { mapStores } from 'pinia'
import { useUserStore } from '@/store/UserStore'
import PasswordInput from '@/component/input/PasswordInput.vue'

export default {
  components: {
    PasswordInput,
  },
  computed: {
    userId() { return this.$route.params.id },
    isEdit() { if (this.userId) return true; return false },
    actionVerb() { if (this.isEdit) return 'Edit'; return 'Add' },
    ...mapStores(useUserStore)
  },
  data() {
    return {
      user: {
        username: '',
        name: '',
        password: '',
        email: '',
      },
      showPassword: false,
      isLoading: false,
    }
  },
  methods: {
    async onSubmit() {
      this.isLoading = true
      try {
        if (this.isEdit) { // edit existing user
          this.user = await this.userStore.editUser(this.user)
          this.$notify.ok("User '"+ this.user.username +"' updated!")
        }
        else { // create new user
          let resp = await this.userStore.newUser(this.user)
          this.$notify.ok("User '"+ resp.username +"' created!")
          this.$router.back()
        }
      } catch(err) {
        this.isLoading = false
        this.$notify.err('Check fields for issues.')
        throw err
      }
      this.isLoading = false
    },
    onReset() {
      this.user.username = ''
      this.user.name = ''
      this.user.password = ''
      this.user.email = ''
      this.$error.clearAll()
    },
  },
  mounted() {
  },
  async created() {
    if (this.isEdit) {
      this.user = await this.userStore.getUser(this.userId)
    }
  }
}
</script>

<template>
  <q-card>
    <q-card-section>
      {{ user.password }}
      <div class='text-h6'>{{ actionVerb }} User</div>
      <q-form @submit="onSubmit" @reset="onReset"
              class='column q-col-gutter-md'>
        <q-input v-model="user.username" label="Username" bottom-slots
                 :error="'username' in $error.fields"
                 :error-message='$error.fields.username' />

        <PasswordInput v-model="user.password" label="Password"
                 :is-new-password='false'
                 :error="'password' in $error.fields"
                 :error-message='$error.fields.password' />

        <q-input v-model="user.name" label="Name"
                 :error="'name' in $error.fields"
                 :error-message='$error.fields.name' />

        <q-input v-model="user.email" label="Email" bottom-slots
                 :error="'email' in $error.fields"
                 :error-message='$error.fields.email' />

        <ErrorBox />

        <div class='row reverse-sm justify-between'>
          <q-btn label="Reset" type="reset" color="primary" flat class="q-ml-sm"
                 :disable='isLoading' />
          <div>
            <q-btn label="Save" type="submit" color="primary q-mr-md"
                   :loading='isLoading'/>
            <q-btn color="secondary" icon='arrow_back' label="Back"
                   @click="$router.back()" />
          </div>
        </div>
      </q-form>
    </q-card-section>
  </q-card>
</template>

<style scoped>
</style>
