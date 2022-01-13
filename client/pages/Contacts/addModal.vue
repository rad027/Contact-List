<template>
    <v-dialog v-model="modal" persistent max-width="600px">
        <v-card>
            <v-card-title class="elevation-1">
                Create a Contact
            </v-card-title>
            <v-form v-model="form.valid" ref="createForm" lazy-validation @submit.prevent="createContact">
                <v-card-text>
                    <v-row>
                        <v-col cols=12 xl="6" lg="6">
                            <v-text-field v-model="form.data.name" auto-complete="off" label="Name" :rules="form.rules.name" dense outlined class="rounded-0"></v-text-field>
                        </v-col>
                        <v-col cols=12 xl="6" lg="6">
                            <v-text-field v-model="form.data.facebook" :rules="form.rules.facebook" auto-complete="off" label="Facebook (Optional)" dense outlined class="rounded-0"></v-text-field>
                        </v-col>
                        <v-col cols=12 xl="12" lg="12">
                            <v-textarea v-model="form.data.address" auto-complete="off" label="Address" :rules="form.rules.address" dense outlined class="rounded-0" auto-grow rows="3"></v-textarea>
                        </v-col>
                        <v-col cols=12 xl="12" lg="12">
                            <template v-for="(vv,vi) in form.data.phone_number">
                                <v-text-field :key="vi" v-model="form.data.phone_number[vi].value" auto-complete="off" label="Phone Number" :rules="form.rules.phone_number" dense outlined class="rounded-0">
                                    <template v-slot:append-outer>
                                        <v-icon v-if="vi > 0" @click="form.data.phone_number.splice(vi,1)">
                                            mdi-delete
                                        </v-icon>
                                        <v-icon color="green" v-else @click="form.data.phone_number.push({value : '' })">
                                            mdi-plus
                                        </v-icon>
                                    </template>
                                </v-text-field>
                            </template>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-divider></v-divider>
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
                    phone_number : [{
                        value : ''
                    }]
                },
                showPass : false,
                rules : {
                    name : [
                        v => !!v || 'Name is a required field.'
                    ],
                    address : [
                        v => !!v || 'Address is a required field.'
                    ],
                    phone_number : [
                        v => !!v || 'Phone Number is a required field.'
                    ],
                    facebook : [
                        v => !this.isURL(v) || 'Facebook must be username only.'
                    ]
                }
            }
        }
    },

    async created(){
        this.loading = false
    },

    watch :{
        modal(v){
            if(!v){
                this.$emit('updateContact', { update : false })
            }
        }
    },

    methods : {
        
        isURL(str) {
            let url;

            try {
                url = new URL(str);
            } catch (_) {
                return false;
            }

            return url.protocol === "http:" || url.protocol === "https:";
        },

        async createContact(){
            if(this.$refs.createForm.validate()){
                this.form.loading = true
                /* console.log(this.form.data) */
                await this.$axios.post('/functions/contacts/create', this.form.data)
                    .then(res => {
                        var x = res.data
                        this.$swal.fire({
                            title : 'Contact has been created',
                            icon : 'success'
                        })
                        this.$emit('updateContact', { update : true })
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