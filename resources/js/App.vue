<script>
export default {
  components: {
  },
  data() {
    return {
      count: 0
    }
  }
}
</script>

<template>
  <div class='my-2'>
    <div class='max-w-5xl mx-auto'>
      <div class='border rounded-xl border-neutral-300 bg-white drop-shadow-2xl'>
        <!-- Logo Header -->
        <div class='bg-neutral-100 rounded-t-xl p-6 pb-8 -mb-4'>
          <a href='/nuxt' class='text-black visited:text-black '>
            <img src="@img/ipeer_logo.png" alt='iPeer logo'
                 class='inline-block mr-1 align-top' />
            <span class='text-3xl mr-1 align-top'>iPeer</span >
            <span class='align-bottom text-sm italic'>4.0.0</span>
          </a>
        </div>
        <!-- Nav -->
        <div class='border mx-6 bg-white drop-shadow-xl text-neutral-600
                    visited:text-neutral-600 flex justify-between'>
          <a href='/nuxt' class='visited:text-neutral-600 border-b-4 py-2 
                                 px-4 inline-block border-yellow-300
                                 font-semibold text-sm hover:bg-yellow-100'>

            <i-mdi-view-dashboard-variant class='text-xl inline-block' />
            Dashboard
          </a>
        </div>
        <!-- Content -->
        <div class='p-6'></div>
      </div>
      <!-- Footer -->
      <div class='my-5 text-center text-sm text-neutral-800'>
        <p>Powered by iPeer - Created by UBC</p>
        <p>Icons designed by <a href="https://openmoji.org/" target="_blank" rel="nofollow">OpenMoji</a> and used under a <a href="https://creativecommons.org/licenses/by-sa/4.0/#" target="_blank" rel="nofollow">CC BY-SA 4.0 License</a></p>
      </div>
    </div>
  </div>
</template>

<style scoped>
</style>
