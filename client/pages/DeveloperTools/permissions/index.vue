<template>
    <div class="px-2">
        <v-card class="elevation-1" style="border-radius : 3px 3px">
            <v-card-title class="elevation-1">
                Role List
            </v-card-title>
            <v-card-text class="px-1 mt-5">
                <addModal v-if="createModal" v-on:updateUser="updateUser" />
                <editModal v-if="editModal" :old="edit.data" v-on:updateUser="updateUser" />
                <v-data-table hide-default-footer :page.sync="page" :loading="loading" :headers="headers" :items="list" item-key="id" class="elevation-0">
                    <template v-slot:top>
                        <v-toolbar class="elevation-0 px-1">
                            <v-text-field v-model="search.text" label="Search" class="mx-4" @keydown="typing"></v-text-field>
                            <v-spacer></v-spacer>
                            <v-btn text color="green" @click="createModal = true" v-if="user.permissions.filter(o => o.name === 'can create permission').length > 0">
                                <v-icon left>
                                    mdi-plus
                                </v-icon>
                                Create
                            </v-btn>
                        </v-toolbar>
                    </template>
                    <template v-slot:[`item.actions`]="props">
                        <v-icon class="mr-1" :id="props.item.id" @click="openEditModal(props.item)" v-if="user.permissions.filter(o => o.name === 'can update permission').length > 0">
                            mdi-pencil
                        </v-icon>
                        <v-icon @click="deletePerm(props.item)" v-if="user.permissions.filter(o => o.name === 'can delete permission').length > 0">
                            mdi-delete
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
        allowed : [ 'can list permission' ]
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
        await axios.post('/functions/permissions/list?page=1')
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
            title : 'Permission List'
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

        async openEditModal(item){
            this.edit.data = item
            this.editModal = true
        },

        async deletePerm(item){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete this permission!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Delete it!'
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
                    await axios.post('/functions/permissions/delete', { id : item.id })
                        .then(() => {
                            swalala.close()
                            this.$swal.fire('Permission has been deleted!', '', 'success')
                            this.init()
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

        async init(){
            this.loading = true
            var data
            await axios.post('/functions/permissions/list?page='+this.page)
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
                await axios.post('/functions/permissions/search?page='+this.page, { keyword : this.search.text })
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