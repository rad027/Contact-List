<template>
  <v-app v-if="auth">
    <div class="layout">
      <v-app-bar app clipped-left color="white">
        <v-img src="https://i.imgur.com/ZCqZNke.png" contain class="mx-2 my-3" max-height="60" max-width="200"></v-img>
        <v-spacer></v-spacer>
        
        <template v-slot:extension>
          <v-card tile color="transparent" class="elevation-0" width="100%">
            <v-card-actions class="px-0">
              <v-app-bar-nav-icon @click.stop="drawer = !drawer">
                <v-icon v-if="drawer" color="red">
                  mdi-close
                </v-icon>
                <v-icon v-else color="#2d3270">
                  mdi-menu
                </v-icon>
              </v-app-bar-nav-icon>
              <v-spacer></v-spacer>
              <v-btn text :to="{ name : 'home' }" exact exact-active-class="nav-active-class">
                Home
              </v-btn>
            </v-card-actions>
          </v-card>
        </template>
      </v-app-bar>
      <v-navigation-drawer v-model="drawer" app clipped color="white">
        <v-list nav dense >
          <v-list-item two-line>
              <v-list-item-avatar class="my-0">
                  <img :src="'https://ui-avatars.com/api/?name='+user.name" />
              </v-list-item-avatar>

              <v-list-item-content v-if="auth">
                  <v-list-item-title>{{ user.name }}</v-list-item-title>
                  <v-list-item-subtitle>
                    <span v-if="user.roles.length > 0">{{ user.roles[0].name }}</span>
                    <span v-else>User</span>
                  </v-list-item-subtitle>
              </v-list-item-content>
          </v-list-item>
          <v-divider class="my-1"></v-divider>
          <template v-for="(nav,navi) in sideNavs">
            <v-list-item v-if="nav.exact" :key="navi" :exact="nav.exact" :to="{ name : nav.path }">
                <v-list-item-icon>
                    <v-icon>{{ nav.icon }}</v-icon>
                </v-list-item-icon>

                <v-list-item-title>{{ nav.title }}</v-list-item-title>
            </v-list-item>
            <template v-if="!nav.exact">
              <v-list-group v-if="checkSubmenu(nav.allowed)" :value="nav.submenu.filter(o => o.path === $route.name).length > 0" prepend-icon="mdi-account-circle" no-action :key="navi">
                  <template v-slot:activator>
                      <v-list-item-title>{{ nav.title }}</v-list-item-title>
                  </template>
                  <template v-for="(sub,subi) in nav.submenu">
                    <v-list-item link :key="subi" :to="{ name : sub.path }" exact v-if="checkSubmenu(sub.allowed)">
                      <v-list-item-content>
                        <v-list-item-title>{{ sub.title }}</v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>
                  </template>
              </v-list-group>
            </template>
          </template>
        </v-list>
        <template v-slot:append>
            <v-divider class="my-1"></v-divider>
            <div class="pa-2">
                <v-btn block color="#2d3270" dark @click="logout">
                    Logout
                </v-btn>
            </div>
        </template>        
      </v-navigation-drawer>
      <v-main>
        <v-container fluid class="">
          <nuxt keep-alive />
        </v-container>
      </v-main>
    </div>
  </v-app>
</template>

<script>
import Navbar from '~/components/Navbar'

export default {

  middleware : [ 'auth' ],

  loading : {
    color : 'green',
    height : '5px'
  },

  computed : {
    user(){
      return this.$store.getters['auth/user']
    },
    auth(){
      return this.$store.getters['auth/check']
    }
  },

  components: {
    Navbar
  },

  data(){
    return {
      drawer : true,
      sideNavs : [
        {
          title : 'Home',
          icon : 'mdi-home',
          path : 'home',
          exact : true,
          allowed : [],
          submenu : []
        }
      ]
    }
  },

  methods : {

    checkSubmenu(allowed){
      if(allowed.length === 0){
        return true
      }else{
        if(allowed.some((o) => {
          var x = this.user.roles
          var y = this.user.permissions
          var all = x.concat(y)
          return all.find(u => {
            return u.name === o
          })
        })){
          return true
        }else{
          return false
        }
      }
    },

    async logout(){
      this.$swal.fire({
        title: 'Are you sure?',
        text: "You are about to be logged out!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Logged me out!'
      }).then(async(result) => {
        if (result.isConfirmed) {
          var swalala = this.$swal.fire({
            title : 'Processing Your Request...',
            icon : 'warning',
            showCloseButton : false,
            showConfirmButton : false,
            showCancelButton: false,
            allowOutsideClick: false,
          })
          // Log out the user.
          await this.$store.dispatch('auth/logout')
          swalala.close()
          this.$swal.fire(
            'Logged Out!',
            'Your session has been successfully destroyed.',
            'success'
          ).then((v)=>{
            if(v.isConfirmed){
              // Redirect to login.
              this.$router.push({ name: 'login' })
            }
          })
        }
      })      
    }

  }

}
</script>
