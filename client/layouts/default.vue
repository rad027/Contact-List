<template>
  <v-app>
    <div class="layout">
      <navbar />

      <div class="container mt-4">
        <nuxt />
      </div>
    </div>
  </v-app>
</template>

<script>
import Navbar from '~/components/Navbar'

export default {
  components: {
    Navbar
  }
}
</script>
