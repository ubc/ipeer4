import axios from '@/plugin/axios'
import { defineStore, acceptHMRUpdate } from 'pinia'
import storeBase from '@/store/AbstractPaginatedStore'

const urlBase = 'course'

const courseStoreExt = {
  state: {
  },

  getters: {
  },

  actions: {
    async getPage() {
      return await this.index(urlBase)
    },
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
  }
}

export const useCourseStore = defineStore('course', storeBase(courseStoreExt))

// enable HMR for this store
if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useCourseStore, import.meta.hot))
}
