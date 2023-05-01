<script>
import { mapStores } from 'pinia'
import { useErrorStore } from '@/store/ErrorStore'

export default {
  components: {
  },
  computed: {
    ...mapStores(useErrorStore)
  },
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
  <div v-if='errorStore.msgs.length'>
    <q-banner class="errorBorder">
      <q-list separator>
        <q-item v-for='msg in errorStore.msgs'>
          <q-item-section avatar>
            <q-icon name="error" color='negative' />
          </q-item-section>
          <q-item-section>
            <q-item-label text-color='negative' v-if='msg.title'>{{ msg.title }}</q-item-label>
            <q-item-label caption v-if='msg.body'>{{ msg.body }}</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-banner>
  </div>
</template>

<style scoped lang="scss">
.errorBorder {
  border: 1px solid $negative;
  border-left: 5px solid $negative;
}
</style>
