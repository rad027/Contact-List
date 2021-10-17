<template>
    <v-dialog v-model="modal" persistent max-width="500px">
        <v-card>
            <v-card-title class="elevation-1">
                Update a User&nbsp;<b>[{{ old.name }}]</b>
            </v-card-title>
            <v-form v-model="form.valid" ref="editForm" lazy-validation @submit.prevent="updateUser">
                <v-card-text>
                    <v-row>
                        <v-col cols=12>
                            <v-text-field v-model="form.data.name" auto-complete="off" label="Name" :rules="form.rules.name" dense outlined class="rounded-0"></v-text-field>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <v-btn color="red" text @click="modal = false">
                        Cancel
                    </v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="green" text :loading="form.loading || loading" type="submit">
                        Update
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
    </v-dialog>
</template>

<script>
export default {

    props : [ 'old' ],

    computed : {
        user(){
            return this.$store.getters['auth/user']
        }
    },
    
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
                    ]
                }
            }
        }
    },

    async created(){
        this.form.data = Object.assign({}, this.old)
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

        async updateUser(){
            if(this.$refs.editForm.validate()){
                this.form.loading = true
                await this.$axios.post('/functions/roles/update', this.form.data)
                    .then(async res => {
                        var x = res.data
                        this.$swal.fire({
                            title : 'Role has been updated',
                            icon : 'success'
                        })
                        if(this.user.id === this.form.data.id){
                            await this.$store.dispatch('auth/fetchUser')
                        }
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