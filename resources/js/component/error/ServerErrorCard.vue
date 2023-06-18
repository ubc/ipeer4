<script>
import ErrorCard from '@/component/error/ErrorCard.vue'

export default {
  components: {
    ErrorCard,
  },
  computed: {
    title() {
      let resp = this.error.response
      if (resp.status == 401) {
        let msg = 'You need to log in'
        if ('message' in resp.data) msg = resp.data.message
        return msg
      }
      else if (resp.status == 404) {
        let msg = 'Requested resource not found' // default message
        if ('message' in resp.data) msg = resp.data.message
        return msg
      }
      else if (resp.status == 409) {
        // rare, like if delete fails cause it's already being deleted
        let msg = 'Conflict encountered when trying to modify resource'
        if ('message' in resp.data) msg = resp.data.message
        return msg
      }
      else if (resp.status == 422) {
        // laravel field validation error
        return 'Check fields'
      }
      else {
        // general catchall 
        let msg = 'Unknown Server Error'
        if ('message' in resp.data) msg = resp.data.message
        return msg
      }
    },
    fieldErrors() {
      let resp = this.error.response
      let msgs = []
      if (resp.status == 422) {
        // laravel field validation error
        for (const [field, issues] of Object.entries(resp.data.errors)) {
          for (const issue of issues) {
            msgs.push({'field': field, 'issue': issue})
          }
        }
      }
      return msgs
    }
  },
  props: ['error'],
  data() {
    return {
    }
  },
  methods: {
  },
  mounted() {
  }
}
</script>

<template>
  <ErrorCard :title='title' :timestamp='error.timestamp'>
    <q-list v-if='fieldErrors' bordered separator>
      <q-item v-for='msg in fieldErrors'>
        <q-item-section avatar>
          <q-avatar text-color="negative" icon="error_outline" />
        </q-item-section>
        <q-item-section>{{ msg.issue }}</q-item-section>
      </q-item>
    </q-list>
  </ErrorCard>
</template>

<style scoped lang="scss">
</style>
