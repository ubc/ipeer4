import axios from '@/plugin/axios'
import { defineStore, acceptHMRUpdate } from 'pinia'
import storeBase from '@/store/AbstractPaginatedStore'

const urlBase = 'user'

const userStoreExt = {
  state: {},
  getters: {},
  actions: {
    async getPage() {
      return await this.index(urlBase)
    },
    async get(userId) {
      const url = urlBase + '/' + userId
      const resp = await axios.get(url)
      return resp.data
    },
    async create(userInfo) {
      const resp = await axios.post(urlBase, userInfo)
      return resp.data
    },
    async edit(userInfo) {
      const url = urlBase + '/' + userInfo.id
      const resp = await axios.put(url, userInfo)
      return resp.data
    },
    async remove(userId) {
      const url = urlBase + '/' + userId
      const resp = await axios.delete(url)
      // remove deleted user from cached page
      this.page = this.page.filter(user => user.id != userId)
    },
  }
}

export const useUserStore = defineStore('user', storeBase(userStoreExt))

// enable HMR for this store
if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useUserStore, import.meta.hot))
}
