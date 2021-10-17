<template>
  <div style="background : linear-gradient(100deg, rgba(217, 215, 66, 1) 0%, rgba(240, 158, 0, 1) 100%); width : 100vw;position : relative;overflow : hidden">
    <v-row justify="center" align-content="center" align="center" style="min-height : 100%">
      <v-col cols="10" lg=6 xl=6 style="max-width : 500px">
        <center><v-img src="https://suiterus.com/templates/g5_helium/custom/images/logo-suiterus.png" contain class="mx-2 my-3" max-height="60" max-width="200"></v-img></center>
        <v-card color="rgba(255,255,255,0.4)">
          <v-card-title class="elevation-1 laravel">
            WELCOME TO SUITERUS TECHNOLOGIES INC.`s <br>FRONTEND TEMPLATE
          </v-card-title>
          <v-card-text>
            <p>Features : </p>
            <ul>
              <li>SPA or SSR enabled.</li>
              <li>Vuex, Vue-Router, & Vue JS enabled.</li>
              <li>Vuetify JS enabled.</li>
              <li>Customizable Layout per page.</li>
              <li>Customizable middleware and router.</li>
              <li>
                <p>Dashboard Features</p>
                <ul>
                  <li>User management - Create, Update, Ban/Unban, Set Roles, Set Permissions</li>
                </ul>
              </li>
            </ul>
            <br>
            <p>Created by Roldhan Dasalla.</p>
            <p>Dependecy template : <a href="https://github.com/cretueusebiu/laravel-nuxt" target="_blank">https://github.com/cretueusebiu/laravel-nuxt</a></p>
          </v-card-text>
          <v-card-actions style="border-top : 2px solid #222">
            <span class="title laravel">Sample Routes : </span>
            <v-spacer></v-spacer>
            <v-btn link class="mr-2" :to="{ name : 'login' }">LOGIN</v-btn>
            <v-btn link :to="{ name : 'register' }">REGISTER</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  layout: 'simple',

  middleware : [ 'guest' ],

  data: () => ({
    title: process.env.appName
  }),

  head () {
    return { title: this.$t('home') }
  },

  computed: mapGetters({
    authenticated: 'auth/check'
  })
}
</script>

<style scoped>
.top-right {
  position: absolute;
  right: 10px;
  top: 18px;
}

.title {
  font-size: 85px;
}

.laravel {
  color: #2e495e;
}

.nuxt {
  color: #00c48d;
}
</style>
