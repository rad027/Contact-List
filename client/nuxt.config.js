require('dotenv').config()
const { join, resolve } = require('path')
const { copySync, removeSync } = require('fs-extra')
import fs from 'fs'

module.exports = {

  server: {
    //change `<hotename>` with your generated domain by valet
    hostname : process.env.HOSTNAME || 'localhost',
    port: process.env.APP_PORT, // default: 3000,
    https: {
      //change `<username>` with your machine username
      key: fs.readFileSync(resolve(process.env.KEY_PATH, process.env.HOSTNAME + '.key')),
      cert: fs.readFileSync(resolve(process.env.CERT_PATH, process.env.HOSTNAME + '.crt'))
    }
  },

  ssr: false,

  srcDir: __dirname,

  env: {
    apiUrl: process.env.API_URL || process.env.APP_URL + '/api',
    appName: process.env.APP_NAME || 'STI - Project Frontend Template',
    appLocale: process.env.APP_LOCALE || 'en',
    githubAuth: !!process.env.GITHUB_CLIENT_ID
  },

  head: {
    title: process.env.APP_NAME,
    titleTemplate: '%s | ' + process.env.APP_NAME,
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: 'STI - Project Frontend Template' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
    ]
  },

  loading: { color: '#007bff' },

  router: {
    middleware: ['locale', 'check-auth']
  },

  css: [
    { src: '~assets/sass/app.scss', lang: 'scss' }
  ],

  plugins: [
    '~components/global',
    '~plugins/i18n',
    '~plugins/vform',
    '~plugins/axios',
    '~plugins/fontawesome',
    '~plugins/nuxt-client-init'
  ],

  modules: [
    '@nuxtjs/router',
    '@nuxtjs/vuetify'
  ],

  build: {
    extractCSS: true
  },

  hooks: {
    generate: {
      done (generator) {
        // Copy dist files to public/_nuxt
        if (generator.nuxt.options.dev === false && generator.nuxt.options.mode === 'spa') {
          const publicDir = join(generator.nuxt.options.rootDir, 'public', '/')
          removeSync(publicDir)
          copySync(join(generator.nuxt.options.generate.dir, '/'), publicDir)
          copySync(join(generator.nuxt.options.generate.dir, '200.html'), join(publicDir, 'index.html'))
          removeSync(generator.nuxt.options.generate.dir)
        }
      }
    }
  }
}
