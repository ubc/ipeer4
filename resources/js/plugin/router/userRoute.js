import UserPage from '@/page/user/UserPage.vue'
import UserInfo from '@/component/user/UserInfo.vue'
import UserForm from '@/component/user/UserForm.vue'

export default {
  path: '/user',
  component: UserPage,
  children: [
    {
      path: ':id',
      name: 'userInfo',
      component: UserInfo
    },
    {
      path: 'new',
      name: 'userNew',
      component: UserForm
    },
    {
      path: ':id/edit',
      name: 'userEdit',
      component: UserForm
    },
  ]
}
