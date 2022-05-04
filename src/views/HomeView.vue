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
             <div class="col-sm-2 text-white" style="margin-top: 5px;">
               文章顯示:
             <select class="form-select" v-model="POWER">
               <option value="0">公開</option>
               <option value="1" selected>不公開</option>
             </select>
             </div>
             <div class="col-sm-8" style="margin-top: 5px;">
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
             <div class="col-sm-2" style="margin-top: 5px;">
             <a class="btn btn-primary" @click="PrismView()"  style="height:35px; margin-right: 5px;">預覽</a>
             <a class="btn btn-primary" @click="post()"  style="height:35px;">發表</a>
             </div>
         </div>
      </div>
    </div>
    <div v-for="(item,index) in this.useStore.state.list" :key="index" >
      <div class="rounded text-wrap article text-white row">
      <div class="col">
           <div class="row">
                <div class="col-6">
                  {{ item.CREATEDATE }}
                </div>
                <div class="col-6 text-right">
                   <a class="dropdown"  v-if="loginstatus()">
                     <a class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" style="height: 35px;">
                     </a>
                     <div class="dropdown-menu">
                       <router-link class="dropdown-item btn"  :to="{ name: 'edited', params: { UUID: item.UUID } }">編輯</router-link>
                       <button class="dropdown-item btn" @click="Delete(item.UUID)">刪除</button>
                     </div>
                  </a>
                </div>
           </div>
           <div style="height:8px;"/>
           <div class="row">
                <div class="col ck-content" v-html= item.CONTENT>
                </div>
           </div>
           <hr>
           <div class="row">
              <div class="col-7">
                    發文時間:{{ item.CREATETIME }}
              </div>
              <div class="col-5 text-right">
                  作者: {{item.AUTHOR}}
              </div>
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
  data() {
      return {
          POWER:'1',
          editor: '',
          editorData: '',
          editorConfig: '' ,
          loading:true,
          CATEGORY:'',
          CATEGORYS:[],
          listcount:0
      };
  },
  updated(){
    let me = this;
    let list = me.useStore.state.list.length;
    if (me.listcount != list){
      me.listcount =list;
      me.PrismView();
    }
  },
  created(){
     let me = this;
     me.useStore = useStore();
     me.useStore.state.list = [];
     me.loading = true;
     me.editor = me.useStore.state.CKEditor;
     me.editorConfig = me.useStore.state.editorConfig;
     me.Logined();
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

      if (!state.Createflag){
        state.Createflag = true;
        window.addEventListener('scroll', this.handleScroll, true);
      }

      let data = new URLSearchParams();
      data.append('commandType', "check");
      state.SEARCHTYPE = 'default';

      me.conection(data,function(response){
       let success = response.data.success;
       if (success == "1"){
           state.logined = true;
           me.getCATEGORYS();
       }
       else{
           state.logined = false;
       }
       me.getAticle(false);
      });
  },
  getAticle(postflag/*檢查是否發文章*/){
      let me = this;
      let state = me.useStore.state;
      
      let data = new URLSearchParams();
      data.append('commandType', "getAticle");
      data.append('SEARCHTYPE', state.SEARCHTYPE);
      data.append('KEYWORD', state.KEYWORD);

      //取得新的文章
      if(!postflag){
          if(state.list.length > 0){
              let nowData = state.list[state.list.length - 1];
              let lastCreatedate = nowData["CREATETIME"];
              data.append('CREATETIME', lastCreatedate);
          }
      }
      
      me.conection(data,function(response){
       let success = response.data.success;
       if (success == "1"){
           me.loading = false;
           let result = response.data.result;
           if(postflag){
               state.list = state.list.reverse();
               state.list.push(result[0]); 
               state.list = state.list.reverse();
           } else{
              result.forEach(element => {
                  state.list.push(element);
              });
           }
           if (result.length == 0){
             state.noDataFlag = true;
           }
       }
       else{
          let msg =response.data.msg;
          me.loading = false;
          alert(msg);
       }
      });
  },
  post(){
      let me = this;
      
      let data = new URLSearchParams();
      data.append('commandType', "post");
      data.append('content', me.editorData);
      data.append('POWER',me.POWER);
      data.append('CATEGORY',me.CATEGORY);
      
      me.conection(data,function(response){
       let success = response.data.success;
       if (success == "1"){
           me.editorData = "";
           me.getAticle(true);
           me.getCATEGORYS();
           me.CATEGORY = "";
       }
       else{
          let msg =response.data.msg;
          alert(msg);
       }
      });
  },
  handleScroll(){
      let me = this;
      let state = me.useStore.state;
      if (window.scrollY + document.documentElement.clientHeight >= document.body.scrollHeight - 5) {
          if (!me.loading & !state.noDataFlag){
              me.loading = true;
              me.getAticle(false);
          }
      }
  },
  PrismView(){
      Prism.highlightAll(); 
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
      })
  },
  Delete(UUID){
      if(!confirm("是否刪除文章?")){
        return;
      }
      let me = this;
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