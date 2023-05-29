import axios from '@/plugin/axios'
import { defineStore } from 'pinia'

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
    users: [],
  }),

  getters: {
  },

  actions: {
    async getPage() {
      const params = new URLSearchParams({
        page:       this.pagination.page,
        per_page:   this.pagination.rowsPerPage,
        sort_by:    this.pagination.sortBy,
        descending: this.pagination.descending,
        filter:     this.filter,
      })
      const url = urlBase + '?' + params.toString()
      try {
        const resp = await axios.get(url)
        this.users = resp.data.data
        this.setPagination({
          page:        resp.data.current_page,
          rowsPerPage: resp.data.per_page,
          sortBy:      resp.data.sort_by,
          descending:  resp.data.descending,
          rowsNumber:  resp.data.total
        })
      } catch (err) {
        throw err
      }
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
