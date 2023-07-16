import axios from '@/plugin/axios'
import { defineStore, acceptHMRUpdate } from 'pinia'
import storeBase from '@/store/AbstractPaginatedStore'

const urlBase = 'user'

const userSearchStoreExt = {
  state: {},
  getters: {},
  actions: {
    async getPage() {
      return await this.index(urlBase)
    },
  }
}

export const useUserSearchStore = defineStore('userSearch',
                                              storeBase(userSearchStoreExt))

// enable HMR for this store
if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useUserSearchStore, import.meta.hot))
}
