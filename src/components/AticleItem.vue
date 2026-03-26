<template>
  <div class="article rounded-2xl border border-slate-800/80 bg-slate-900/80 p-4 text-slate-100" v-if="!useStore.state.modalloadflag">
    <div :key="uuid">
      <div class="flex items-center justify-between text-sm text-slate-400">
        <div>{{ article.CREATEDATE }}</div>
        <div>
          <div class="relative" v-if="loginstatus()">
            <button class="rounded-lg border border-slate-700 px-3 py-1 text-xs text-slate-300">操作</button>
            <div class="dropdown-menu static-dropdown mt-2 rounded-xl border border-slate-700 bg-slate-900 p-1">
              <router-link class="dropdown-item btn" :to="{ name: 'edited', params: { UUID:uuid } }">編輯</router-link>
              <button class="dropdown-item btn" @click="Delete(uuid)">刪除</button>
            </div>
          </div>
        </div>
      </div>

      <div class="spacer-8"></div>
      <div class="ck-content" v-html="article.CONTENT"></div>
      <hr class="my-3 border-slate-700">
      <div class="flex items-center justify-between text-xs text-slate-400">
        <div>發文時間:{{ article.CREATETIME }}</div>
        <div>作者: {{article.AUTHOR}}</div>
      </div>
    </div>
  </div>

  <div class="py-6" v-if="useStore.state.modalloadflag">
    <div class="flex justify-center">
      <span class="loading loading-dots loading-lg text-indigo-400"></span>
    </div>
  </div>
</template>
<script>
import { useStore } from 'vuex'

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
          POWER:'1',
          UUID:'',
          article:{CONTENT:'',
                   AUTHOR:'',
                   CREATEDATE:''}
      };
  },
  watch:{
    article:function (){
         this.$nextTick(function () {
           this.useStore.commit('PrismView');
         });
    }
  },
  created(){
     let me = this;
     me.useStore = useStore();
     me.article = {CONTENT:'',
                   AUTHOR:'',
                   CREATEDATE:''}
     me.getAticle();
  },
  methods:{
      loginstatus(){
         let logined = this.useStore.state.logined;
         let load = this.useStore.state.modalloadflag;
         let flag = logined & !load;
         return flag;
      },
  getAticle(){
      let me = this;
      let UUID = me.uuid;
      let useStore = me.useStore;
      let state = me.useStore.state;
      let articledata  = state.list.filter(function(item) {
                 return item.UUID === UUID
             });

      let data = new URLSearchParams();
      data.append('commandType', "getAticle");
      data.append('SEARCHTYPE', 'CONTENT');
      data.append('KEYWORD', UUID);
      if (articledata.length == 0){
      me.conection(data,function(response){
       state.modalloadflag = false;
       let success = response.data.success;
       if (success == "1"){
           if(response.data.result.length >0){
           let result = response.data.result[0];
           me.article = result;
           me.UUID = UUID;
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
      }else{
       me.article = articledata [0];
       state.modalloadflag= false;
      }
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
<style scoped>
.static-dropdown {
  position: absolute;
  right: 0;
  min-width: 120px;
  display: none;
}

.relative:hover .static-dropdown {
  display: block;
}
</style>
