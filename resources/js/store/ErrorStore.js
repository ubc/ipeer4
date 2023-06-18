import { defineStore, acceptHMRUpdate } from 'pinia'

import axios from '@/plugin/axios'
import { NavigationFailureType, isNavigationFailure } from 'vue-router'

// errors that occur within this timespan in miliseconds will be grouped
// together in the latest error
const ERROR_FRAME_TIMESPAN = 1000;

export const useErrorStore = defineStore('error', {
  state: () => ({
    errorFrameStart: 0, // timestamp of the current error frame
    latest: [], // current error frame, most recent errors
    old: [], // when new error come in, the previous error gets moved here
    errors: [],
  }),

  getters: {
    // returns a map of fields that failed validation to their error messages
    fields(state) {
      let fields = {}
      // we only want to highlight the latest errors
      for (const error of state.latest) {
        if (!error.response) continue
        let resp = error.response
        if (resp.status == 422) {
          for (const [field, issues] of Object.entries(resp.data.errors)) {
            let msg = ''
            for (const issue of issues) {
              msg += issue + ' '
            }
            fields[field] = msg
          }
        }
      }
      return fields
    }
  },

  actions: {
    add(error) {
      let errorFrameEnd = this.errorFrameStart + ERROR_FRAME_TIMESPAN
      if (error.timestamp > errorFrameEnd) {
        // error is newer than the current error frame, create a new frame
        if (this.latest.length) this.old.unshift(this.latest)
        this.errorFrameStart = error.timestamp
        this.latest = [error]
      }
      else {
        // error is in the current error frame, add to current frame
        this.latest.push(error)
      }
    },
    handle(error) {
      error.timestamp = Date.now()
      console.error(error)
      if (error.response && error.response.status == 401) {
        // we don't want the 401 error to be cleared on route change (cause then
        // the error won't show up on login page), so
        // we'll store the error only after navigation is complete
        this.$router.push('/login').then((failure) => {
          this.add(error)
        })
      }
      else {
        this.add(error)
      }
    },
    clearAll() {
      this.errorFrameStart = 0
      this.latest = []
      this.old = []
    }
  },
})

// enable HMR for this store
if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useErrorStore, import.meta.hot))
}
