<template>
  <v-app>
    <div class="layout">
      <v-app-bar app clipped-left color="white">
        <v-img src="https://i.postimg.cc/3rBJmXYt/nkti-logo.png" contain class="mx-2 my-3" max-height="60" max-width="200"></v-img>
        <v-spacer></v-spacer>
        <v-btn text>
          Administrative
        </v-btn>
        <v-btn text class="mx-1" color="purple darken-4">
          Finance
        </v-btn>
        <v-btn text>
          Procurement
        </v-btn>
        <v-btn text>
          Human Resource
        </v-btn>
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
              <v-btn icon depressed color="#2d3270">
                <v-badge content="9" color="green" overlap>
                  <v-icon>
                    mdi-bell
                  </v-icon>
                </v-badge>
              </v-btn>
              <v-spacer></v-spacer>
              <v-btn text @click.stop="drawer = !drawer" :color="!drawer ? '#2d3270' : 'red'" class="mr-1">
                {{ drawer ? 'Close Side Bar' : 'Open Side bar' }}
              </v-btn>
              <v-btn text :to="{ name : 'home' }" exact exact-active-class="nav-active-class">
                Home
              </v-btn>
              <v-btn text>
                Web Setting
              </v-btn>
              <v-btn text>
                User Management
              </v-btn>
              <v-btn text>
                Profile Settings
              </v-btn>
            </v-card-actions>
          </v-card>
        </template>
      </v-app-bar>
      <v-navigation-drawer v-model="drawer" app clipped color="white">
        <v-list nav dense >
          <v-list-item two-line>
              <v-list-item-avatar class="my-0">
                  <img src="https://randomuser.me/api/portraits/women/81.jpg" />
              </v-list-item-avatar>

              <v-list-item-content v-if="auth">
                  <v-list-item-title>{{ user.name }}</v-list-item-title>
                  <v-list-item-subtitle>
                    <span v-if="user.acctype === 12">Admin</span>
                    <span v-else>Member</span>
                  </v-list-item-subtitle>
              </v-list-item-content>
          </v-list-item>
          <v-divider class="my-1"></v-divider>

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
          <nuxt />
        </v-container>
      </v-main>
    </div>
  </v-app>
</template>

<script>
import Navbar from '~/components/Navbar'

export default {

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
      drawer : true
    }
  },

  methods : {

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
