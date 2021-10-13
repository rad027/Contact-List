import Vue from 'vue'
import Router from 'vue-router'
//import { scrollBehavior } from '~/utils'

Vue.use(Router)

const page = path => () => import(`~/pages/${path}`).then(m => m.default || m)

const routes = [
  { path: '/', name: 'index', component: page('index.vue') },
]

export function createRouter () {
  return new Router({
    routes,
    //scrollBehavior,
    mode: 'history'
  })
}