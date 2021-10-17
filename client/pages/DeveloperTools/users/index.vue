<template>
    <div class="px-2">
        <v-card class="elevation-1" style="border-radius : 3px 3px">
            <v-card-title class="elevation-1">
                User List
            </v-card-title>
            <v-card-text class="px-1 mt-5">
                <addModal v-if="createModal" v-on:updateUser="updateUser" />
                <editModal v-if="editModal" :old="edit.data" v-on:updateUser="updateUser" />
                <v-data-table hide-default-footer :page.sync="page" :loading="loading" :headers="headers" :items="list" item-key="id" class="elevation-0">
                    <template v-slot:top>
                        <v-toolbar class="elevation-0 px-1">
                            <v-text-field v-model="search.text" label="Search" class="mx-4" @keydown="typing"></v-text-field>
                            <v-spacer></v-spacer>
                            <v-btn text color="green" @click="createModal = true" v-if="user.permissions.filter(o => o.name === 'can create user').length > 0">
                                <v-icon left>
                                    mdi-plus
                                </v-icon>
                                Create
                            </v-btn>
                        </v-toolbar>
                    </template>
                    <template v-slot:[`item.roles`]="{ item }">
                        <span v-for="(r,ri) in item.roles" :key="ri">
                            <v-chip class="ma-1" small >{{ r.name }}</v-chip>
                            <br v-if="ri%2===1" />
                        </span>
                    </template>
                    <template v-slot:[`item.status`]="{ item }">
                        <v-chip v-if="item.permissions.filter(o => o.name === 'can login').length > 0" small color="green accent-2" text-color="white">ACTIVE</v-chip>
                        <v-chip v-else color="red accent-2" text-color="white" small>BANNED</v-chip>
                    </template>
                    <template v-slot:[`item.actions`]="props">
                        <v-icon class="mr-1" :id="props.item.id" @click="openEditModal(props.item)" v-if="user.permissions.filter(o => o.name === 'can update user').length > 0">
                            mdi-pencil
                        </v-icon>
                        <v-icon :id="props.item.id" @click="processUser(props.item)">
                            {{ props.item.permissions.filter(o => o.name === 'can login').length > 0 ? 'mdi-account-cancel' : 'mdi-account-check' }}
                        </v-icon>
                    </template>
                </v-data-table>
                <div class="text-center pt-2">
                    <v-pagination v-model="page" :length="pageCount" color="#2d3270" :total-visible="itemsPerPage > 2 ? itemsPerPage : 5" @input="onPageChange"></v-pagination>
                </div>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import axios from 'axios'
import addModal from './addModal.vue'
import editModal from './editModal.vue'
export default {

    middleware : [ 'check-access' ],

    meta : {
        allowed : [ 'can list user' ]
    },

    computed :{
        user(){
            return this.$store.getters['auth/user']
        }
    },

    components : {
        addModal,
        editModal
    },

    async asyncData({ to, from }){
        var data
        await axios.post('/functions/users/list?page=1')
            .then(res => {
                var x = res.data
                data = x.data
            })
        return { 
            list : data.data, 
            loading : false,
            itemsPerPage : data.per_page,
            page : data.current_page,
            pageCount : data.last_page
        }
    },
    
    head(){
        return {
            title : 'User List'
        }
    },

    data(){
        return {
            createModal : false,
            editModal : false,
            loading : true,
            search: {
                text : null,
                timer : null,
                loading : false
            },
            headers : [
                { text: 'ID', sortable: false, align: 'center', value: 'id' },
                { text: 'NAME', sortable: false, align: 'center', value: 'name' },
                { text: 'E-MAIL', sortable: false, align: 'center', value: 'email' },
                { text: 'STATUS', sortable: false, align: 'center', value: 'status' },
                { text: 'ROLES', sortable: false, align: 'center', value: 'roles' },
                { text: 'ACTIONS', sortable: false, align: 'center', value: 'actions' },
            ],
            list : [],
            page : 1,
            pageCount: 0,
            itemsPerPage: 1,
            edit : {
                data : {}
            }
        }
    },

    methods : {
        
        async onPageChange(){
            if(this.search.text == null || this.search.text == ''){
                this.init()
            }else{
                this.search_user()
            }
        },

        async processUser(item){
            var status
            if(item.permissions.filter(o => o.name === 'can login').length > 0){
                status = 'ban'
            }else{
                status = 'unban'
            }
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You are about to "+status+" this user!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, '+status+' it!'
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
                    await this.$axios.post('/functions/users/ban/process', { id : item.id, status : status })
                        .then(()=>{
                            this.init()
                            swalala.close()
                            this.$swal.fire(
                                'User has been '+status+'ned!',
                                'User '+item.name+' is now '+status+'ned.',
                                'success'
                            )
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
                }
            })
        },

        async openEditModal(item){
            this.edit.data = item
            this.editModal = true
        },

        async init(){
            this.loading = true
            var data
            await axios.post('/functions/users/list?page='+this.page)
                .then(res => {
                    var x = res.data
                    data = x.data
                })
            this.itemsPerPage = data.per_page,
            this.page = data.current_page,
            this.pageCount = data.last_page
            this.list = data.data
            this.loading = false
        },

        async search_user(){
            if(this.search.text != null || this.search.text != ''){
                this.loading = true
                var data
                await axios.post('/functions/users/search?page='+this.page, { keyword : this.search.text })
                    .then(res => {
                        var x = res.data
                        data = x.data
                    })
                this.itemsPerPage = data.per_page,
                this.page = data.current_page,
                this.pageCount = data.last_page
                this.list = data.data
                this.loading = false
            }else{
                this.page = 1
                this.init()
            }
        },

        async typing(){
            if(this.search.text != null || this.search.text != ''){
                clearTimeout(this.search.timer)
                this.search.timer = setTimeout(()=>{
                    this.search_user()
                },800)
            }else{
                this.page = 1
                this.init()
            }
        },

        async updateUser(v){
            if(v.update){
                //refresh list
                this.init()
            }
            this.createModal = false
            this.editModal = false
        }

    }

}
</script>