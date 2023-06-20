<script>

export default {
  components: {
  },
  computed: {
  },
  props: {
    modelValue: '',
    label: 'Password',
    isNewPassword: false,
    error: false,
    errorMessage: ''
  },
  emits: ['update:modelValue'],
  data() {
    return {
      showPassword: false
    }
  },
  methods: {
  },
  mounted() {
  }
}
</script>

<template>
  <q-input :label="label"
         :type="showPassword ? 'text' : 'password'"
         :autocomplete="isNewPassword ? 'new-password' : 'current-password'"
         :model-value="modelValue"
         @update:model-value="(value) => $emit('update:modelValue', value)"
         :error="error"
         :error-message='errorMessage' >
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
</template>

<style scoped lang="scss">
</style>
