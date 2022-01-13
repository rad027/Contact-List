<template>
  <div class="px-5" style="background : url('https://cccwstl.com/wp-content/uploads/2017/09/striped-background.png') top left repeat; width : 100vw;position : relative;overflow-x : hidden; overflow-y : auto">
    <v-row justify="center" align="center" style="height : 100vh">
        <v-card max-width="400px">
          <v-card-title class="elevation-1">
            REGISTER
          </v-card-title>
          <v-form ref="loginForm" lazy-validation v-model="form.valid" @submit.prevent="register">
            <v-card-text>
              <v-row>
                <v-col cols="12">
                  <v-text-field class="rounded-0" v-model="form.data.name" label="Name" outlined dense :rules="form.rules.name"></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field class="rounded-0" v-model="form.data.email" label="E-Mail" outlined dense :rules="form.rules.email"></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field class="rounded-0" v-model="form.data.password" label="Password" type="password" outlined dense :rules="form.rules.password"></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field class="rounded-0" v-model="form.data.password_confirmation" label="Confirm Password" type="password" outlined dense :rules="form.rules.password_confirmation"></v-text-field>
                </v-col>
              </v-row>
            </v-card-text>
            <v-card-actions>
              <v-btn small text exact :to="{ name : 'login' }">
                CANCEL
              </v-btn>
              <v-spacer></v-spacer>
              <v-btn small type="submit" :loading="form.loading">
                REGISTER
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
          name : [
            v => !!v || 'Name is a required field.'
          ],
          email : [
            v => !!v || 'E-Mail is a required field.',
            v => /.+@.+/.test(v) || 'Invalid E-Mail address'
          ],
          password : [
            v => !!v || 'Password is a required field.'
          ],
          password_confirmation : [
            v => !!v || 'Confirm Password is a required field.',
            v => this.form.data.password === v || 'Password not matched.'
          ]
        }
      },
      mustVerifyEmail: false
    }
  },

  computed : {
    /*passwordConfirmationRule(){
      return () => this.form.pa
    }*/
  },

  methods : {

    async register(){
      if(this.$refs.loginForm.validate()){
        this.form.loading = true
        await this.$axios.post('/register', this.form.data)
          .then(res => {
            var data = res.data
            if (data.status) {
              //this.mustVerifyEmail = true
              this.$swal.fire({
                title : 'Registration Successful!',
                html : 'We`ve sent you a email confirmation. Please check your inbox or spam folder before proceeding on login page.',
                icon : 'warning'
              })
            }else{
              this.$swal.fire({
                title : 'Registration Successful!',
                html : 'You may now login.',
                icon : 'success'
              }).then((r) => {
                if(r.isConfirmed){
                  this.$router.push({ name : 'login' })
                }
              })
            }
          })
          .catch(err => {
            if(err.response.status === '500'){
              this.$swal.fire({
                title : '500 Internal Server Error!',
                html : 'Please contact us to fix it.',
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