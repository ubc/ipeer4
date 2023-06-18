<script>
import { date } from 'quasar'

export default {
  components: {
  },
  computed: {
    humanTime() {
      return date.formatDate(this.timestamp, 'YYYY-MM-DD hh:mm:ssa')
    }
  },
  props: ['title', 'timestamp'],
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
  <q-card class="my-card" flat bordered>
    <q-item>
      <q-item-section avatar>
        <q-avatar icon='error' text-color='negative' size='xl' />
      </q-item-section>

      <q-item-section>
        <q-item-label>{{ title }}</q-item-label>
        <q-item-label caption>{{ humanTime }}</q-item-label>
      </q-item-section>
    </q-item>
    <template v-if='$slots.default'>
      <q-separator />
      <q-card-section>
        <slot></slot>
      </q-card-section>
    </template>
  </q-card>
</template>

<style scoped lang="scss">
</style>
