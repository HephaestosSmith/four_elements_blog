<template>
      <div class="rounded text-wrap article text-white row" v-if="!loading">
      <div class="col">
           <div class="row">
                <div class="col-6">
                  {{ CREATEDATE }}
                </div>
                <div class="col-6 text-right">
                   <a class="dropdown"  v-if="loginstatus()">
                     <a class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" style="height: 35px;">
                     </a>
                     <div class="dropdown-menu">
                       <router-link class="dropdown-item btn"  :to="{ name: 'edited', params: { UUID:UUID } }">編輯</router-link>
                       <button class="dropdown-item btn" @click="Delete(UUID)">刪除</button>
                     </div>
                  </a>
                </div>
           </div>
           <div style="height:8px;"/>
           <div class="row">
                <div class="col ck-content" v-html= CONTENT>
                </div>
           </div>
           <hr>
           <div class="row">
              <div class="col-7">
                    發文時間:{{ CREATETIME }}
              </div>
              <div class="col-5 text-right">
                  作者: {{AUTHOR}}
              </div>
           </div>
          </div>
      </div>
    <div class="row">
      <div class="col">
         <div style="height:20px"/>
      </div>
    </div>
    <div class="row" v-if="loading">
      <div class="col d-flex justify-content-center">
        <div class="spinner-grow text-primary m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-success m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-danger m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-warning m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-info m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-light m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-dark m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <!--
         
        <div class="spinner-border text-primary m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-primary m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-secondary m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-success m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-danger m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-warning m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-info m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-light m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-dark m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>-->
      </div>
    </div>
</template>
<script>
import { useStore } from 'vuex'
import Prism from "prismjs";
import 'prismjs/components/prism-bash'
import 'prismjs/components/prism-javascript'
import 'prismjs/components/prism-json'
import 'prismjs/components/prism-liquid'
import 'prismjs/components/prism-markdown'
import 'prismjs/components/prism-markup-templating'
import 'prismjs/components/prism-php'
import 'prismjs/components/prism-scss'
import "prismjs/themes/prism-tomorrow.css"; // you can change

export default {
  inject: [
     'conection',
     'modalshow'
     ],
  props: {
    uuid: {
      type: String,
    },
  },
  data() {
      return {
          id : '',
          POWER:'1',
          UUID:'',
          CREATEDATE:'',
          CREATETIME:'',
          CONTENT:'',
          AUTHOR:'',
          loading:true,
          article:[]
      };
  },
  updated(){
     this.PrismView();
  },
  created(){
     let me = this;
     me.useStore = useStore();
     me.editor = me.useStore.state.CKEditor;
     me.editorConfig = me.useStore.state.editorConfig;
     me.getAticle();
  },
  methods:{
      loginstatus(){
         let logined = this.useStore.state.logined;
         let load = this.loading;
         let flag = logined & !load;
         return flag;
      },
  getAticle(){
      let me = this;
      let UUID = me.$route.params.UUID;
      let useStore = me.useStore;
      
      let data = new URLSearchParams();
      data.append('commandType', "getAticle");
      data.append('SEARCHTYPE', 'CONTENT');
      data.append('KEYWORD', UUID);
      
      me.conection(data,function(response){
       me.loading = false;
       let success = response.data.success;
       if (success == "1"){
           if(response.data.result.length >0){
           let result = response.data.result[0];
           me.article = result;
           me.UUID = UUID;
           me.CREATETIME = me.article.CREATETIME;
           me.CREATEDATE = me.article.CREATEDATE;
           me.POWER = me.article.POWER;
           me.CONTENT = me.article.CONTENT;
           me.AUTHOR = me.article.AUTHOR;
           me.modalshow();
           }else{
             useStore.commit('ModalViewColse');
           }
       }
       else{
          let msg =response.data.msg;
          alert(msg);
       }
      });
  },
  PrismView(){
      Prism.highlightAll(); 
  },
  Delete(UUID){
      if(!confirm("是否刪除文章?")){
        return;
      }
      let me = this;
      let useStore = me.useStore;
      let state = me.useStore.state;
      
      let data = new URLSearchParams();
      data.append('commandType', "delete");
      data.append('UUID', UUID);
      
      me.conection(data,function(response){
       let success = response.data.success;
       if (success == "1"){
             let list = [];
             list = state.list.filter(function(item) {
                 return item.UUID !== UUID
             });
             state.list = list;
             useStore.commit('ModalViewColse');
       }
       else{
          let msg =response.data.msg;
          alert(msg);
       }
      })
  }
  }
}
</script>