import { defineStore } from 'pinia'
import axios from '@/plugin/axios'

// You can name the return value of `defineStore()` anything you want,
// but it's best to use the name of the store and surround it with `use`
// and `Store` (e.g. `useUserStore`, `useCartStore`, `useProductStore`)
// the first argument is a unique id of the store across your application
export const useVersionStore = defineStore('version', {
  state: () => ({
    full: 'none',
    debug: false,
    mode: 'none'
  }),

  getters: {
  },

  actions: {
    get() {
      axios.get('version')
        .then((response) => {
          this.full = response.data.full
          this.debug = response.data.debug
          this.mode = response.data.mode
        })
        .catch((err) => {
          // TODO: real error message
          console.log(err)
        })
    },
  },
})
