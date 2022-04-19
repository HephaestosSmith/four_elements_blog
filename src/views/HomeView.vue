<template>
    
    <div class="rounded text-wrap article">
        <a class="btn btn-info" @click="post()"  style="width:100px; height:35px;">發表</a>
        <div style="height:8px;"/>
        <ckeditor :editor="editor" v-model="editorData" :config="editorConfig"></ckeditor>
    </div>
    <div class="rounded text-wrap article text-white">
        我的測試
    </div>
</template>

<script>
import { useStore } from 'vuex'
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

export default {
    data() {
        return {
            editor: ClassicEditor,
            editorData: '',
            editorConfig: {
                // The configuration of the editor.
            }
        };
    },
    created(){
       this.useStore = useStore();
    },
  methods:{
  post(){
      let me = this;
      let useStore = me.useStore;
      let http = useStore.state.axios;
      let phpurl = useStore.getters.phpurl;
      
      let data = new URLSearchParams();
      data.append('commandType', "post");
      data.append('content', me.editorData);

      http.post(phpurl("Command"),data)
      .then(function(response){
       let success = response.data.success;
       if (success == "1"){
           me.editorData = ""
           me.$router.push('/')
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