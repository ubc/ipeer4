import CoursePage from '@/page/course/CoursePage.vue'
import CourseInfo from '@/component/course/CourseInfo.vue'
import CourseForm from '@/component/course/CourseForm.vue'
import CourseUserForm from '@/component/course/CourseUserForm.vue'

export default {
  path: '/course',
  component: CoursePage,
  children: [
    {
      path: ':courseId/info',
      name: 'courseInfo',
      component: CourseInfo
    },
    {
      path: 'new',
      name: 'courseNew',
      component: CourseForm
    },
    {
      path: ':courseId/edit',
      name: 'courseEdit',
      component: CourseForm
    },
    {
      path: ':courseId/user/new',
      name: 'courseUserNew',
      component: CourseUserForm
    },
  ]
}
