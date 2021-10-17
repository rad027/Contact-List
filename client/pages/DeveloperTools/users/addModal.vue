<template>
    <v-dialog v-model="modal" persistent max-width="500px">
        <v-card>
            <v-card-title class="elevation-1">
                Create a User
            </v-card-title>
            <v-form v-model="form.valid" ref="createForm" lazy-validation @submit.prevent="createUser">
                <v-card-text>
                    <v-row>
                        <v-col cols=12>
                            <v-text-field v-model="form.data.name" auto-complete="off" label="Full Name" :rules="form.rules.name" dense outlined class="rounded-0"></v-text-field>
                        </v-col>
                        <v-col cols=12>
                            <v-text-field v-model="form.data.email" auto-complete="off" label="E-Mail" :rules="form.rules.email" dense outlined class="rounded-0"></v-text-field>
                        </v-col>
                        <v-col cols=12>
                            <v-text-field v-model="form.data.password" label="Password" :type="form.showPass ? 'text' : 'password'" :rules="form.rules.password" dense outlined class="rounded-0" >
                                <template v-slot:[`append`]>
                                    <v-icon class="mr-1" @click="showPassword">
                                        {{ form.showPass ? 'mdi-eye-outline' : 'mdi-eye' }}
                                    </v-icon>
                                    <v-icon @click="generatePass">
                                        mdi-lock-reset
                                    </v-icon>
                                </template>
                            </v-text-field>
                        </v-col>
                        <v-col cols=12>
                            <v-combobox :loading="loading" :dense="form.data.roles.length < 1" outlined v-model="form.data.roles" :items="data.roles" label="Roles" item-text="name" multiple chips>
                                <template v-slot:[`prepend-item`]>
                                    <v-btn block class="rounded-0" @click="tooglePerms">
                                        {{ form.data.roles.length === data.roles.length ? 'UNSELECT ALL' : 'SELECT ALL' }}
                                    </v-btn>
                                </template>
                                <template v-slot:selection="{ item, index }">
                                    <v-chip v-if="index <= 2">
                                        <span>{{ item.name }}</span>
                                    </v-chip>
                                    <span v-if="index === 3" class="grey--text text-caption" >
                                        (+{{ form.data.roles.length - 3 }} others)
                                    </span>
                                </template>      
                            </v-combobox>
                        </v-col>
                        <v-col cols="12">
                            <v-combobox :loading="loading" :dense="form.data.permissions.length < 1" outlined v-model="form.data.permissions" :items="data.permissions" label="Permissions" item-text="name" multiple chips>
                                <template v-slot:[`prepend-item`]>
                                    <v-btn block class="rounded-0" @click="tooglePerms">
                                        {{ form.data.permissions.length === data.permissions.length ? 'UNSELECT ALL' : 'SELECT ALL' }}
                                    </v-btn>
                                </template>
                                <template v-slot:selection="{ item, index }">
                                    <v-chip v-if="index <= 2">
                                        <span>{{ item.name }}</span>
                                    </v-chip>
                                    <span v-if="index === 3" class="grey--text text-caption" >
                                        (+{{ form.data.permissions.length - 3 }} others)
                                    </span>
                                </template>      
                            </v-combobox>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <v-btn color="red" text @click="modal = false">
                        Cancel
                    </v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="green" text :loading="form.loading" type="submit">
                        Create
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    
    data(){
        return {
            modal : true,
            loading : true,
            data : {
                roles : [],
                permissions : []
            },
            form : {
                valid : true,
                loading : false,
                data : {
                    roles : [],
                    permissions : []
                },
                showPass : false,
                rules : {
                    name : [
                        v => !!v || 'Name is a required field.'
                    ],
                    email : [
                        v => !!v || 'E-Mail is a required field.'
                    ],
                    password : [
                        v => !!v || 'Password is a required field.'
                    ],
                    roles : [
                        v => v.length > 0 || 'Roles is a required field.'
                    ],
                }
            }
        }
    },

    async created(){
        //fetch roles and permissions
        await this.$axios.post('/functions/roles/all')
            .then(res => {
                var x = res.data
                this.data.roles = x.data
            })
        await this.$axios.post('/functions/permissions/all')
            .then(res => {
                var x = res.data
                this.data.permissions = x.data
            })
        this.loading = false
    },

    watch :{
        modal(v){
            if(!v){
                this.$emit('updateUser', { update : false })
            }
        }
    },

    methods : {

        async tooglePerms(){
            if(this.form.data.permissions.length === this.data.permissions.length){
                this.form.data.permissions = []
            }else{
                this.form.data.permissions = this.data.permissions
            }
        },

        async generatePass(){
            this.form.data.password = null
            this.form.data.password = this.$randompass(12)
            this.form.showPass = false
            this.form.showPass = true
        },

        async showPassword(){
            this.form.showPass = !this.form.showPass
        },

        async createUser(){
            if(this.$refs.createForm.validate()){
                this.form.loading = true
                await this.$axios.post('/functions/users/create', this.form.data)
                    .then(res => {
                        var x = res.data
                        this.$swal.fire({
                            title : 'User has been created',
                            icon : 'success'
                        })
                        this.$emit('updateUser', { update : true })
                    })
                    .catch(err => {
                        if(err.response.status === '500'){
                            this.$swal.fire({
                                title : '500 Internal server error!',
                                html : 'There is a problem with our server. Please contact us to fix it. Error-code : dev-user-creation-0x01',
                                icon : 'error'
                            })
                        }else{
                            var x = ''
                            this.$jquery.each(err.response.data.errors,(i,v) => {
                                x += v + '<br>'
                            })
                            this.$swal.fire({
                                title : 'Request Denied!',
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