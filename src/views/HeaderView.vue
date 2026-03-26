<template>
  <nav class="mx-auto mb-5 mt-4 max-w-7xl rounded-2xl border border-slate-800/80 bg-slate-950/70 px-4 py-3 shadow-2xl backdrop-blur">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
      <div class="flex items-center gap-4">
        <router-link class="text-xl font-bold text-slate-100 no-underline" to="/" @click="home()">{{ HomeName }}</router-link>
        <ul class="m-0 flex list-none items-center gap-2 p-0">
          <li>
            <router-link class="rounded-lg px-3 py-2 text-slate-300 transition hover:bg-indigo-500/20 hover:text-white" to="/" @click="home()" v-if="installflag">首頁</router-link>
          </li>
          <HeaderMAINCATEGORYSItem />
          <li v-if="!loginstatus() && installflag">
            <router-link class="rounded-lg px-3 py-2 text-slate-300 transition hover:bg-indigo-500/20 hover:text-white" to="/login">登入</router-link>
          </li>
          <li v-if="loginstatus() && installflag">
            <router-link class="rounded-lg px-3 py-2 text-slate-300 transition hover:bg-indigo-500/20 hover:text-white" to="/" @click="logout()">登出</router-link>
          </li>
        </ul>
      </div>

      <div class="flex items-center gap-2" v-if="installflag">
        <input
          class="w-56 rounded-xl border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 outline-none ring-indigo-400 transition focus:ring"
          type="search"
          placeholder="輸入關鍵字"
          v-model="SearchData"
        >
        <button class="rounded-xl bg-indigo-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-400" @click="HeaderSearch(true)">查詢</button>
      </div>
    </div>
  </nav>
</template>

<script>
import HeaderMAINCATEGORYSItem from '../components/HeaderMAINCATEGORYSItem.vue'
import { useStore } from 'vuex'
import Cookies from 'vue-cookie'

export default {
  inject: [
     'conection'
     ],
  data() {
      return {
          SearchData: '',
          HomeName:'想個好名字吧',
          installflag:false
      };
  },
  components: {
    HeaderMAINCATEGORYSItem
  },
  created(){
     this.useStore = useStore();
     this.Logined();
  },
  methods:{
  loginstatus(){
     return this.useStore.state.logined;
  },
  Logined(){
      let me = this;
      if(me.$route.name!="install"){
         let state = me.useStore.state;
         let data = new URLSearchParams();
         data.append('commandType', "gethomename");
         me.conection(data,function(response){
          let success = response.data.success;
          if (success == "1"){
              let result = response.data.result[0];
              me.HomeName = result['PAGENAME'];
              me.installflag = true;
          }});

         data = new URLSearchParams();
         data.append('commandType', "check");

         me.conection(data,function(response){
          let success = response.data.success;
          if (success == "1"){
              state.logined = true;
          }
          else{
              state.logined = false;
          } });
      }
  },
  logout(){
      let me = this;
      let state = me.useStore.state;
      Cookies.delete('username');
      Cookies.delete('TOKEN');
      Cookies.delete('authorname');
      state.list = [];
      state.logined = false;
      state.noDataFlag = false;
      me.Logined();
      me.HeaderSearch();
  },
  home(){
      let me = this;
      let state = me.useStore.state;
      state.list = [];
      state.logined = false;
      state.noDataFlag = false;
      if(me.$route.name!="install"){
         me.Logined();
         me.HeaderSearch();
      }
  },
  HeaderSearch(flag){
      let me = this;
      let state = me.useStore.state;
      state.homeloadflag = true;

      if(!flag){
        me.SearchData = '';
        state.SEARCHTYPE = "default";
      }

      if(me.SearchData.length == 0){
        state.SEARCHTYPE = "default";
      }else{
        state.SEARCHTYPE = "KEYWORD";
      }
      state.KEYWORD = me.SearchData;
      state.noDataFlag = true;
      state.list = [];

      let data = new URLSearchParams();
      data.append('commandType', "getAticle");
      data.append('SEARCHTYPE', state.SEARCHTYPE);
      data.append('KEYWORD', state.KEYWORD);

      me.conection(data,function(response){
       state.homeloadflag = false;
       let success = response.data.success;
       if (success == "1"){
           let result = response.data.result;
              result.forEach(element => {
                  state.list.push(element);
              });
           state.noDataFlag = false;
       }
      });
  }
  }
}
</script>
