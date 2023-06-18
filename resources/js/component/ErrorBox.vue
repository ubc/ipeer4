<script>
import { mapStores } from 'pinia'
import CodeErrorCard from '@/component/error/CodeErrorCard.vue'
import NoResponseErrorCard from '@/component/error/NoResponseErrorCard.vue'
import ServerErrorCard from '@/component/error/ServerErrorCard.vue'

export default {
  components: {
    CodeErrorCard,
    NoResponseErrorCard,
    ServerErrorCard,
  },
  computed: {
  },
  data() {
    return {
    }
  },
  methods: {
    clearAll() {
      this.$error.clearAll()
    }
  },
  mounted() {
  }
}
</script>

<template>
  <div v-if='$error.latest.length'>
    <q-banner class="errorBorder">
      <template v-for='error in $error.latest'>
        <ServerErrorCard v-if='error.response' :error='error' />
        <NoResponseErrorCard v-else-if='error.request' :error='error' />
        <CodeErrorCard v-else :error='error' />
      </template>

      <q-expansion-item v-if='$error.old.length'
        label='Previous Errors' switch-toggle-side>
        <q-card bordered>
          <q-card-section v-for='errorGroup in $error.old'>
            <template v-for='error in errorGroup'>
              <ServerErrorCard v-if='error.response' :error='error' />
              <NoResponseErrorCard v-else-if='error.request' :error='error' />
              <CodeErrorCard v-else :error='error' />
            </template>
          </q-card-section>
        </q-card>
      </q-expansion-item>

      <template v-slot:action>
        <q-btn flat color="primary" icon='clear_all' label="Clear"
               @click='clearAll()' />
      </template>
    </q-banner>
  </div>
</template>

<style scoped lang="scss">
.errorBorder {
  border: 1px solid $negative;
  border-left: 5px solid $negative;
}
</style>
