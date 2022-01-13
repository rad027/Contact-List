<template>
    <div class="px-2" v-if="auth">
        <v-card class="elevation-1" style="border-radius: 3px 3px;">
            <v-card-title class="elevation-1">
                Contact List
            </v-card-title>
            <v-card-text class="px-1 mt-5">
                <addModal v-if="createModal" v-on:updateContact="updateContact" />
                <editModal v-if="editModal" :old="edit.data" v-on:updateContact="updateContact" />
                <v-data-table hide-default-footer :page.sync="page" :loading="loading" :headers="headers" :items="list" item-key="id" class="elevation-0">
                    <template v-slot:top>
                        <v-toolbar class="elevation-0 px-1">
                            <v-text-field v-model="search.text" label="Search" class="mx-4" @keydown="typing"></v-text-field>
                            <v-spacer></v-spacer>
                            <v-btn text color="green" @click="createModal = true" v-if="user.permissions.filter(o => o.name === 'can create contact').length > 0">
                                <v-icon left>
                                    mdi-plus
                                </v-icon>
                                Create
                            </v-btn>
                        </v-toolbar>
                    </template>
                    <template v-slot:[`item.phone_numbers`]="{ item }">
                    <v-chip v-for="(p,pi) in item.phone_numbers" :key="pi" class="ma-1">{{ p.value }}</v-chip>
                    </template>
                    <template v-slot:[`item.actions`]="props">
                        <v-icon class="mr-1" :id="props.item.id" @click="openEditModal(props.item)" v-if="user.permissions.filter(o => o.name === 'can update contact').length > 0">
                            mdi-pencil
                        </v-icon>
                            <v-icon @click="deleteContact(props.item)" v-if="user.permissions.filter(o => o.name === 'can delete contact').length > 0">
                                mdi-delete
                            </v-icon>
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import axios from 'axios'
import addModal from './Contacts/addModal.vue'
import editModal from './Contacts/editModal.vue'
export default {
    middleware: "auth",

    head() {
        return { title: this.$t("home") };
    },

    computed : {
        auth(){
            return this.$store.getters['auth/check']
        },
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
        await axios.post('/functions/contacts/list?page=1')
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

    data() {
        return {
            createModal: false,
            editModal: false,
            loading: true,
            search: {
                text: null,
                timer: null,
                loading: false,
            },
            headers: [
                { text: "NAME", sortable: false, align: "center", value: "name" },
                { text: "ADDRESS", sortable: false, align: "center", value: "address" },
                { text: "PHONE NUMBER/s", sortable: false, align: "center", value: "phone_numbers" },
                { text: "FB USERNAME", sortable: false, align: "center", value: "facebook" },
                { text: "ACTIONS", sortable: false, align: "center", value: "actions" },
            ],
            list: [],
            page: 1,
            pageCount: 0,
            itemsPerPage: 1,
            edit: {
                data: {},
            },
        };
    },

    methods : {

      async deleteContact(item){
          this.$swal.fire({
              title: 'Are you sure?',
              text: "You are about to delete this role!",
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
                  await axios.post('/functions/contacts/delete', { id : item.id })
                      .then(() => {
                          swalala.close()
                          this.$swal.fire('Contact has been deleted!', '', 'success')
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

      async openEditModal(item){
          this.edit.data = item
          this.editModal = true
      },

      async updateContact(v){
        if(v.update){
          this.init()
        }
        this.createModal = false
        this.editModal = false
      },

      async init(){
          this.loading = true
          var data
          await axios.post('/functions/contacts/list?page='+this.page)
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

      async search_contact(){
          if(this.search.text != null || this.search.text != ''){
              this.loading = true
              var data
              await axios.post('/functions/contacts/search?page='+this.page, { keyword : this.search.text })
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
                  this.search_contact()
              },800)
          }
      },

    }

}
</script>
