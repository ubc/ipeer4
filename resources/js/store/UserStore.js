import axios from '@/plugin/axios'
import { defineStore, acceptHMRUpdate } from 'pinia'

const urlBase = 'user'

export const useUserStore = defineStore('user', {
  state: () => ({
    filter: '',
    pagination: {
      sortBy: 'id',
      descending: false,
      page: 1,
      rowsPerPage: 10,
      rowsNumber: 0, // total entries
    },
    page: [],
  }),

  getters: {
  },

  actions: {
    async getUser(userId) {
      const url = urlBase + '/' + userId
      const resp = await axios.get(url)
      return resp.data
    },
    async deleteUser(userId) {
      const url = urlBase + '/' + userId
      const resp = await axios.delete(url)
      // remove deleted user from cached page
      this.page = this.page.filter(user => user.id != userId)
    },
    async getPage() {
      const params = new URLSearchParams({
        page:       this.pagination.page,
        per_page:   this.pagination.rowsPerPage,
        sort_by:    this.pagination.sortBy,
        descending: this.pagination.descending,
        filter:     this.filter,
      })
      const url = urlBase + '?' + params.toString()
      const resp = await axios.get(url)
      this.page = resp.data.data
      this.setPagination({
        page:        resp.data.current_page,
        rowsPerPage: resp.data.per_page,
        sortBy:      resp.data.sort_by,
        descending:  resp.data.descending,
        rowsNumber:  resp.data.total
      })
    },
    setFilter(filter) {
      this.filter = filter
    },
    setPagination(pagination) {
      this.pagination.page        = pagination.page
      this.pagination.rowsPerPage = pagination.rowsPerPage
      this.pagination.sortBy      = pagination.sortBy
      this.pagination.descending  = pagination.descending
      if (pagination.rowsNumber)
        this.pagination.rowsNumber = pagination.rowsNumber
    },
  },
})

// enable HMR for this store
if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useUserStore, import.meta.hot))
}
