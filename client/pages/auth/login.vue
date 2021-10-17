<template>
  <div class="px-5" style="background : linear-gradient(100deg, rgba(217, 215, 66, 1) 0%, rgba(240, 158, 0, 1) 100%); width : 100vw;position : relative;overflow : hidden">
    <v-row justify="center" align="center" style="height : 100vh">
        <v-card max-width="400px">
          <v-card-title class="elevation-1">
            LOGIN
          </v-card-title>
          <v-form ref="loginForm" lazy-validation v-model="form.valid" @submit.prevent="login">
            <v-card-text>
              <v-row>
                <v-col cols="12">
                  <v-text-field class="rounded-0" v-model="form.data.email" label="E-Mail" outlined dense :rules="form.rules.email"></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field class="rounded-0" v-model="form.data.password" label="Password" type="password" outlined dense :rules="form.rules.password"></v-text-field>
                </v-col>
              </v-row>
            </v-card-text>
            <v-card-actions>
              <v-btn small text exact :to="{ name : 'welcome' }">
                CANCEL
              </v-btn>
              <v-spacer></v-spacer>
              <v-btn small type="submit" :loading="form.loading">
                LOGIN
              </v-btn>
            </v-card-actions>
          </v-form>
        </v-card>
    </v-row>
  </div>
</template>

<script>
export default {
  
  middleware : [ 'guest' ],

  layout : 'simple',

  data(){
    return {
      form : {
        valid : true,
        loading : false,
        data : {},
        rules : {
          email : [
            v => !!v || 'E-Mail is a required field.',
            v => /.+@.+/.test(v) || 'Invalid E-Mail address'
          ],
          password : [
            v => !!v || 'Password is a required field.'
          ]
        }
      }
    }
  },

  methods : {

    async login(){
      if(this.$refs.loginForm.validate()){
        this.form.loading = true
        await this.$axios.post('/login', this.form.data)
          .then(async res => {
            var data = res.data
            // Save the token.
            this.$store.dispatch('auth/saveToken', {
              token: data.token,
              remember: this.remember
            })
            // Fetch the user.
            await this.$store.dispatch('auth/fetchUser')
            // Redirect home.
            this.$router.push({ name: 'home' })
          })
          .catch(err => {
            if(err.response.status == '500'){
              this.$swal.fire({
                title : '500 Internal Server Error!',
                html : 'Please contact us to fix it.',
                icon : 'error'
              })
            }else if(err.response.status == '403'){
              var x = ''
              this.$jquery.each(err.response.data.errors,(i,v) => {
                x += v + "<br>"
              })
              this.$swal.fire({
                title : 'ACCESS DENIED!',
                html : x,
                icon : 'error'
              })
            }else{
              var x = ''
              this.$jquery.each(err.response.data.errors,(i,v) => {
                x += v + "<br>"
              })
              this.$swal.fire({
                title : 'Input errors!',
                html : x,
                icon : 'error'
              })
            }
          })
        this.form.loading = false
      }
    }

  }

}
</script>