import axios from '@/plugin/axios'
import { defineStore, acceptHMRUpdate } from 'pinia'

const urlBase = 'course'

export const useCourseStore = defineStore('course', {
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
    async get(courseId) {
      const url = urlBase + '/' + courseId
      const resp = await axios.get(url)
      return resp.data
    },
    async create(courseInfo) {
      const resp = await axios.post(urlBase, courseInfo)
      return resp.data
    },
    async edit(courseInfo) {
      const url = urlBase + '/' + courseInfo.id
      const resp = await axios.put(url, courseInfo)
      return resp.data
    },
    async remove(courseId) {
      const url = urlBase + '/' + courseId
      const resp = await axios.delete(url)
      // remove deleted course from cached page
      this.page = this.page.filter(course => course.id != courseId)
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
  import.meta.hot.accept(acceptHMRUpdate(useCourseStore, import.meta.hot))
}
