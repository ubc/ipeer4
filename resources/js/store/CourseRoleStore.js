import axios from '@/plugin/axios'
import { defineStore, acceptHMRUpdate } from 'pinia'


export const useCourseRoleStore = defineStore('courseRole',{
  state: () => ({
    roles:    [],
    courseId: -1 
  }),
  getters: {},
  actions: {
    getUrl(courseId, roleId=null) {
      if (roleId) return 'course/' + courseId + '/role/' + roleId
      return 'course/' + courseId + '/role'
    },
    async getRoles(courseId) {
      if (courseId == this.courseId) return // use cached
      this.roles = []
      const resp = await axios.get(this.getUrl(courseId))
      this.roles = resp.data
      this.courseId = courseId
      return
    },
  }
})

// enable HMR for this store
if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useCourseRoleStore, import.meta.hot))
}
