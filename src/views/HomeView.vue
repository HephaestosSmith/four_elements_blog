<template>
    <div class="rounded text-wrap article row" v-if="loginstatus()">
      <div class="col">
         <div class="row">
             <div class="col-sm-2" style="margin-top: 5px;">
             <a class="btn btn-primary" @click="post()"  style="width:100px; height:35px;">發表</a>
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
        <div style="height:8px;"/>
         <div class="row">
             <div class="col">
             <ckeditor :editor="editor" v-model="editorData" :config="editorConfig"></ckeditor>
             </div>
         </div>
      </div>
    </div>
    <div v-for="(item,index) in this.useStore.state.list" :key="index" >
      <div class="rounded text-wrap article text-white row">
      <div class="col">
           <div class="row">
                <div class="col-9">
                    <div class="row">
                      <a class="btn btn-info" @click="post(item.UUID)"  style="width:80px; height:35px; margin-left: 10px;">修改</a>
                      <a class="btn btn-danger" @click="Delete(item.UUID)"  style="width:80px; height:35px; margin-left: 10px;">刪除</a>
                    </div>
                </div>
                <div class="col-3 text-right">
                   {{ item.CREATEDATE }}
                </div>
           </div>
           <div style="height:8px;"/>
           <div class="row">
                <div class="col" v-html= item.CONTENT>
                </div>
           </div>
           <hr>
           <div class="row">
              <div class="col-10 col-md-9">
                    發文時間:{{ item.CREATETIME }}
              </div>
              <div class="col-md-3 text-right">
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
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

export default {
    data() {
        return {
            POWER:'1',
            editor: ClassicEditor,
            editorData: '',
            editorConfig: {
                // The configuration of the editor.
            },
            loading:false,
            CATEGORY:'',
            CATEGORYS:[]
        };
    },
    created(){
       this.useStore = useStore();
       this.Logined();
       this.getAticle(false);
       this.useStore.state.SEARCHTYPE = 'default';
       window.addEventListener("scroll", this.handleScroll);
    },
  methods:{
      loginstatus(){
         return this.useStore.state.logined;
      },
      Logined(){
      let me = this;
      let useStore = me.useStore;
      let http = useStore.state.axios;
      let phpurl = useStore.getters.phpurl;
      
      let data = new URLSearchParams();
      data.append('commandType', "check");

      http.post(phpurl("Command"),data)
      .then(function(response){
       let success = response.data.success;
       if (success == "1"){
           useStore.state.logined = true;
           me.getCATEGORYS();
       }
       else{
           useStore.state.logined = false;
       }
      })
      .catch(function (error) {
       alert(error);
      });
  },
  getAticle(postflag/*檢查是否發文章*/){
      let me = this;
      let useStore = me.useStore;
      let state = me.useStore.state;
      let http = state.axios;
      let phpurl = useStore.getters.phpurl;
      
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

      http.post(phpurl("Command"),data)
      .then(function(response){
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
      })
      .catch(function (error) {
       me.loading = false;
       alert(error);
      });
  },
  post(){
      let me = this;
      let useStore = me.useStore;
      let http = useStore.state.axios;
      let phpurl = useStore.getters.phpurl;
      
      let data = new URLSearchParams();
      data.append('commandType', "post");
      data.append('content', me.editorData);
      data.append('POWER',me.POWER);
      data.append('CATEGORY',me.CATEGORY);
      

      http.post(phpurl("Command"),data)
      .then(function(response){
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
      })
      .catch(function (error) {
       alert(error);
      });
  },
  handleScroll(){
      let me = this;
      let state = me.useStore.state;
      if (!me.loading & !state.noDataFlag){
          if (window.scrollY + document.documentElement.clientHeight >= document.body.scrollHeight - 5) {
              me.loading = true;
              me.getAticle(false);
          }
      }
  },
  getCATEGORYS(){
      let me = this;
      let useStore = me.useStore;
      let http = useStore.state.axios;
      let phpurl = useStore.getters.phpurl;
      
      let data = new URLSearchParams();
      data.append('commandType', "getCATEGORYS");

      http.post(phpurl("Command"),data)
      .then(function(response){
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
      .catch(function (error) {
       me.loading = false;
       alert(error);
      });
  },
  Delete(UUID){
      if(!confirm("是否刪除文章?")){
        return;
      }
      
      let me = this;
      let useStore = me.useStore;
      let state = me.useStore.state;
      let http = useStore.state.axios;
      let phpurl = useStore.getters.phpurl;
      
      let data = new URLSearchParams();
      data.append('commandType', "delete");
      data.append('UUID', UUID);
      
      http.post(phpurl("Command"),data)
      .then(function(response){
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
      .catch(function (error) {
       alert(error);
      });
  }
  }
}
</script>