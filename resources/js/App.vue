<script>
import { mapStores } from 'pinia'
import { useVersionStore } from '@/store/VersionStore'
import { useErrorStore } from '@/store/ErrorStore'


export default {
  components: {
  },
  computed: {
    ...mapStores(useVersionStore),
    ...mapStores(useErrorStore)
  },
  data() {
    return {
      count: 0
    }
  },
  mounted() {
    this.versionStore.get()
    this.$router.beforeEach((to, from) => {
      // clear existing errors on route change
      this.errorStore.clear()
    })
  }
}
</script>

<template>
  <q-layout view="hhh lpr fff">

    <q-header bordered>
      <q-toolbar>
        <q-toolbar-title class='q-pt-md'>
          <q-btn stretch flat no-caps to='/'>
            <div class='row items-baseline'>
              <img src="@img/ipeer_logo.png" alt='iPeer logo' />
              <span class='text-h4 q-ml-xs'>iPeer</span>
            </div>
          </q-btn>
        </q-toolbar-title>
      </q-toolbar>

      <q-tabs align="left">
        <q-route-tab to="/admin" label="Admin" />
      </q-tabs>

    </q-header>

    <q-footer bordered class="bg-grey-2 text-grey-8">
      <div class='text-center q-my-md'>
        <p>
          Powered by iPeer {{ versionStore.full }} - Created by UBC <br />
          Icons designed by <a href="https://openmoji.org/" target="_blank" rel="nofollow">OpenMoji</a> and used under a <a href="https://creativecommons.org/licenses/by-sa/4.0/#" target="_blank" rel="nofollow">CC BY-SA 4.0 License</a> <br />
          <span v-if='versionStore.debug'>Debug Mode On</span>
        </p>
      </div>
    </q-footer>

    <q-page-container>
      <router-view />
    </q-page-container>

    <!-- shows a load bar for all ajax actions -->
    <q-ajax-bar size='5px' />

  </q-layout>
</template>

<style scoped>
</style>
