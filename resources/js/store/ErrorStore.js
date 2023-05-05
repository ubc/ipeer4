import { defineStore } from 'pinia'
import axios from '@/plugin/axios'
import { NavigationFailureType, isNavigationFailure } from 'vue-router'

export const useErrorStore = defineStore('error', {
  state: () => ({
    errors: [],
  }),

  getters: {
    // return a list of error messages, each error message is in the format:
    // {'title': 'Error Title', 'body': 'Error Detail'}
    msgs(state) {
      let msgs = []
      for (const error of state.errors) {
        let resp = error.response
        if (resp.status == 401) {
          msgs.push({
            'title': 'You need to log in'
          })
        }
        else if (resp.status == 404) {
          let msg = 'Requested resource not found'
          if ('message' in resp.data) msg = error.response.data.message
          msgs.push({
            'title': msg
          })
        }
        else if (resp.status == 409) {
          // rare, like if delete fails cause it's already being deleted
          let msg = 'Conflict encountered when trying to modify resource'
          if ('message' in resp.data) msg = error.response.data.message
          msgs.push({
            'title': msg
          })
        }
        else if (resp.status == 422) {
          // laravel field validation error
          for (const [field, issues] of Object.entries(resp.data.errors)) {
            for (const issue of issues) {
              msgs.push({
                'title': issue
              })
            }
          }
        }
        else {
          // general catchall 
          let msg = 'Unknown Error'
          if ('message' in resp.data)
            msg = error.response.data.message
          msgs.push({
            'title': msg
          })
        }
      }
      return msgs
    },
    // returns a map of fields that failed validation to their error messages
    fields(state) {
      let fields = {}
      for (const error of state.errors) {
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
