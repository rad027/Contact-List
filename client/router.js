import Vue from 'vue'
import Router from 'vue-router'
import { scrollBehavior } from '~/utils'
import axios from 'axios'
import Swal from 'sweetalert2'
import jquery from 'jquery'


Vue.prototype.$axios = axios
Vue.prototype.$swal = Swal
Vue.prototype.$jquery = jquery

Vue.prototype.$role_colors = {
  Developer : {
    color : '#2ecc71',
    dark : true
  }
}

Vue.prototype.$randompass = (length) => {
  var result           = '';
  var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
  var charactersLength = characters.length;
  for ( var i = 0; i < length; i++ ) {
    result += characters.charAt(Math.floor(Math.random() * charactersLength));
  }
  return result;
}

Vue.use(Router)

const page = path => () => import(`~/pages/${path}`).then(m => m.default || m)

const routes = [
  { path: '/', name: 'welcome', component: page('welcome.vue') },

  { path: '/login', name: 'login', component: page('auth/login.vue') },
  { path: '/register', name: 'register', component: page('auth/register.vue') },
  { path: '/password/reset', name: 'password.request', component: page('auth/password/email.vue') },
  { path: '/password/reset/:token', name: 'password.reset', component: page('auth/password/reset.vue') },
  { path: '/email/verify/:id', name: 'verification.verify', component: page('auth/verification/verify.vue') },
  { path: '/email/resend', name: 'verification.resend', component: page('auth/verification/resend.vue') },

  { path: '/home', name: 'home', component: page('home.vue') },
    //developer tools
      //user
        //index
        { path: '/developer-tools/users', name: 'developer-tools.users.index', component: page('DeveloperTools/users/index.vue') },
      //role
        //index
        { path: '/developer-tools/roles', name: 'developer-tools.roles.index', component: page('DeveloperTools/roles/index.vue') },
      //permission
        //index
        { path: '/developer-tools/permissions', name: 'developer-tools.permissions.index', component: page('DeveloperTools/permissions/index.vue') },
  {
    path: '/settings',
    component: page('settings/index.vue'),
    children: [
      { path: '', redirect: { name: 'settings.profile' } },
      { path: 'profile', name: 'settings.profile', component: page('settings/profile.vue') },
      { path: 'password', name: 'settings.password', component: page('settings/password.vue') }
    ]
  }
]

export function createRouter () {
  return new Router({
    routes,
    scrollBehavior,
    mode: 'history'
  })
}
