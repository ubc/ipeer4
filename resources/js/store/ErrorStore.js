import { defineStore } from 'pinia'
import axios from '@/plugin/axios'
import { NavigationFailureType, isNavigationFailure } from 'vue-router'

export const useErrorStore = defineStore('error', {
  state: () => ({
    errors: [],
  }),

  getters: {
    msgs(state) {
      let msgs = []
      for (const error of state.errors) {
        if (error.response.status == 401) {
          msgs.push({
            'title': 'You need to log in'
          })
        }
        else if (error.response.status == 422) {
          // laravel field validation error
          msgs.push({
            'title': 'Unprocessable Content'
          })
        }
      }
      return msgs
    }
  },

  actions: {
    handle(error) {
      if (error.response.status == 401) {
        // we don't want the 401 error to be cleared on route change (cause then
        // the error won't show up on login page), so
        // we'll store the error only after navigation is complete
        this.$router.push('/login').then((failure) => {
          this.errors.push(error)
        })
      }
      else {
        this.errors.push(error)
      }
    },
    clear() {
      this.errors = []
    }
  },
})
