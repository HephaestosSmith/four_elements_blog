<template>
    <div class="rounded text-wrap article row" v-if="loginstatus()">
      <div class="col">
         <div class="row">
             <div class="col">
             <ckeditor :editor="editor" v-model="editorData" :config="editorConfig"></ckeditor>
             </div>
         </div>
        <div style="height:8px;"/>
         <div class="row">
             <div class="col-sm-2" style="margin-top: 5px;">
             <a class="btn btn-primary" @click="update()"  style="height:35px; margin-right: 5px;">修改</a>
             <a class="btn btn-primary" @click="PrismView()"  style="height:35px;">預覽</a>
             </div>
             <div class="col-sm-6" style="margin-top: 5px;">
               <div class="row">
                  <div class="col-4" style="padding-right: 5px;padding-left: 10px;">
                     <select class="form-select"  v-model="CATEGORY" style="width: inherit;">
                       <option value=""></option>
                       <option  v-for="(item,index) in CATEGORYS" :key="index">{{ item }}</option>
                     </select>
                  </div>
                  <div class="col-8" style="padding-right: 0px;padding-left: 0px;">
                     <input type="text" class="form-control" placeholder="分類"  v-model="CATEGORY" style="height: 25px;" >
                  </div>
               </div>
             </div>
             <div class="col-sm-4 text-white" style="margin-top: 5px;">
               文章顯示:
             <select class="form-select" v-model="POWER">
               <option value="0">公開</option>
               <option value="1" selected>不公開</option>
             </select>
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
      <div class="col">
        <div class="spinner-grow text-primary" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-secondary" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-success" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-danger" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-warning" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-info" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-light" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-dark" role="status">
          <span class="sr-only">Loading...</span>
        </div>
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
     'conection'
     ],
  props: {
    id: {
      type: String,
    },
  },
  data() {
      return {
          POWER:'1',
          editor: '',
          editorData: '',
          editorConfig: '',
          loading:true,
          CATEGORY:'',
          CATEGORYS:[],
          article:[]
      };
  },
  created(){
     let me = this;
     me.useStore = useStore();
     me.editor = me.useStore.state.CKEditor;
     me.editorConfig = me.useStore.state.editorConfig;
     this.Logined();
  },
  methods:{
      loginstatus(){
         let logined = this.useStore.state.logined;
         let load = this.loading;
         let flag = logined & !load;
         return flag;
      },
      Logined(){
      let me = this;
      let state = me.useStore.state;
      let data = new URLSearchParams();
      data.append('commandType', "check");
      
      me.conection(data,function(response){
       let success = response.data.success;
       if (success == "1"){
           state.logined = true;
           me.getCATEGORYS();
       }
       else{
           state.logined = false;
       }
       me.getAticle();
      });
  },
  getAticle(){
      let me = this;
      let UUID = me.$route.params.UUID;
      
      let data = new URLSearchParams();
      data.append('commandType', "getAticle");
      data.append('SEARCHTYPE', 'CONTENT');
      data.append('KEYWORD', UUID);
      
      me.conection(data,function(response){
       me.loading = false;
       let success = response.data.success;
       if (success == "1"){
           let result = response.data.result[0];
           me.article = result;
           me.CATEGORY = me.article.CATEGORY;
           me.POWER = me.article.POWER;
           me.editorData = me.article.CONTENT;
       }
       else{
          let msg =response.data.msg;
          alert(msg);
       }
      });
  },
  update(){
      let me = this;
      
      let data = new URLSearchParams();
      data.append('commandType', "update");
      data.append('CONTENT', me.editorData);
      data.append('POWER',me.POWER);
      data.append('CATEGORY',me.CATEGORY);
      data.append('UUID',me.article.UUID);
      data.append('MTDT',me.article.MTDT);
      
      if(me.changecheck()){
         me.conection(data,function(response){
          let success = response.data.success;
          if (success == "1"){
              me.$router.push("/");
          }
          else{
             let msg =response.data.msg;
             alert(msg);
          }
         });
      }else{
        me.$router.push("/");
      }
  },
  getCATEGORYS(){
      let me = this;
      let data = new URLSearchParams();
      data.append('commandType', "getCATEGORYS");
      
      me.conection(data,function(response){
       let success = response.data.success;
       if (success == "1"){
           let result = response.data.result;
           let CATEGORYS = [];
           result.forEach(element => {
               if (element["CATEGORY"]!=""){
               CATEGORYS.push(element["CATEGORY"]);
               }
           });
           me.CATEGORYS = CATEGORYS;
       }
       else{
          let msg =response.data.msg;
          me.loading = false;
          alert(msg);
       }
      });
  },
  changecheck(){
      let me = this;
      let oldData = me.article;
      if(oldData.CONTENT != me.editorData){
          return true;
      }else if (oldData.POWER != me.POWER){
          return true;
      }else if (oldData.CATEGORY != me.CATEGORY){
          return true;
      }else{
          return false;
      }
  },  
  PrismView(){
      Prism.highlightAll(); 
  }
  }
}
</script>