import axios from '@/plugin/axios'
import { defineStore, acceptHMRUpdate } from 'pinia'
import storeBase from '@/store/AbstractPaginatedStore'

const courseUserStoreExt = {
  state: {},
  getters: {},
  actions: {
    getUrl(courseId, userId=null) {
      if (userId) return 'course/' + courseId + '/user/' + userId
      return 'course/' + courseId + '/user'
    },
    async getPage(courseId) {
      return await this.index(this.getUrl(courseId))
    },
    async create(courseId, userIds) {
      const resp = await axios.post(this.getUrl(courseId), {'userIds': userIds})
      return resp.data
    },
    async remove(courseId, userId) {
      const resp = await axios.delete(this.getUrl(courseId, userId))
      // remove deleted user from cache
      this.page = this.page.filter(user => user.id != userId)
    }
  }
}

export const useCourseUserStore = defineStore('courseUser',
                                              storeBase(courseUserStoreExt))

// enable HMR for this store
if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useCourseUserStore, import.meta.hot))
}
