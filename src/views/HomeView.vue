<template>
    <div class="rounded text-wrap article row" v-if="loginstatus()">
      <div class="col">
         <div class="row">
             <div class="col-2">
             <a class="btn btn-info" @click="post()"  style="width:100px; height:35px;">發表</a>
             </div>
             <div class="col-2">
             <select class="form-select" aria-label="Default select example" v-model="POWER">
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
    <div v-for="(item,index) in list" :key="index" >
      <div class="rounded text-wrap article text-white row">
      <div class="col">
           <div class="row">
                <div class="col-9">
                </div>
                <div class="col-3">
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
              <div class="col-md-3">
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
            list: [],
            editor: ClassicEditor,
            editorData: '',
            editorConfig: {
                // The configuration of the editor.
            },
            loading:false,
            noDataFlag:false
        };
    },
    created(){
       this.useStore = useStore();
       this.Logined();
       this.getAticle(false);
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
      let http = useStore.state.axios;
      let phpurl = useStore.getters.phpurl;
      
      let data = new URLSearchParams();
      data.append('commandType', "getAticle");
      //取得新的文章
      if(!postflag){
          if(me.list.length > 0){
              let nowData = me.list[me.list.length - 1];
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
               me.list = me.list.reverse();
               me.list.push(result[0]); 
               me.list = me.list.reverse();
           } else{
              result.forEach(element => {
                  me.list.push(element);
              });
           }
           if (result.length == 0){
             me.noDataFlag = true;
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

      http.post(phpurl("Command"),data)
      .then(function(response){
       let success = response.data.success;
       if (success == "1"){
           me.editorData = ""
           me.getAticle(true);
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
      if (!me.loading & !me.noDataFlag){
          if (window.scrollY + document.documentElement.clientHeight >= document.body.scrollHeight - 5) {
              me.loading = true;
              me.getAticle(false);
          }
      }
  }
  }
}
</script>