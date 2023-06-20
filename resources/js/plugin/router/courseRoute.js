import CoursePage from '@/page/course/CoursePage.vue'
import CourseInfo from '@/component/course/CourseInfo.vue'
import CourseForm from '@/component/course/CourseForm.vue'

export default {
  path: '/course',
  component: CoursePage,
  children: [
    {
      path: ':courseId',
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
  ]
}
