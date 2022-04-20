<template>
    <div class="rounded text-wrap article row" v-if="logined">
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
    <div v-for="(item,index) in list[0]" :key="index" >
      <div class="rounded text-wrap article text-white row">
      <div class="col">
           <div class="row">
                <div class="col-9">
                </div>
                <div class="col-3">
                   {{ item.CREATETIME }}
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
                  AUTHOR: {{item.AUTHOR}}
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
</template>

<script>
import { useStore } from 'vuex'
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

export default {
    data() {
        return {
            logined:false,
            POWER:'1',
            list: [],
            editor: ClassicEditor,
            editorData: '',
            editorConfig: {
                // The configuration of the editor.
            }
        };
    },
    created(){
       this.useStore = useStore();
       this.Logined();
       this.getAticle();
    },
  methods:{
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
           me.logined = true;
       }
       else{
           me.logined = false;
       }
      })
      .catch(function (error) {
       alert(error);
      });
  },
  getAticle(){
      let me = this;
      let useStore = me.useStore;
      let http = useStore.state.axios;
      let phpurl = useStore.getters.phpurl;
      
      let data = new URLSearchParams();
      data.append('commandType', "getAticle");

      http.post(phpurl("Command"),data)
      .then(function(response){
       let success = response.data.success;
       if (success == "1"){
           me.list.push(response.data.result);
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
           me.getAticle();
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