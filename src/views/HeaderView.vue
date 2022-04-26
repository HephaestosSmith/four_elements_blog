<template>
  <!--<div class="col-12">
    <a id="header">四元素部落格</a>
    <p><router-link to="/" class="btn btn-primary btn-xs" role="button">Home</router-link>
       | <router-link to="/about" class="btn btn-primary btn-xs" role="button">about</router-link></p>
  </div>-->
<nav class="navbar navbar-expand-lg bg-dark navbar-dark" role="navigation">
  <!-- Brand -->
  <router-link class="navbar-brand" to="/" @click="HeaderSearch(false)">四元素部落格</router-link>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
  <!-- Links -->
  <ul class="navbar-nav mr-auto">
    <li class="nav-item">
      <router-link class="nav-link" to="/" @click="HeaderSearch(false)">首頁</router-link>
    </li>
    <li class="nav-item" v-if="!loginstatus()">
      <router-link class="nav-link" to="/login">登入</router-link>
    </li>
    <li class="nav-item" v-if="loginstatus()">
      <router-link class="nav-link"  to="/" @click="logout()">登出</router-link>
    </li>
    <!--<li class="nav-item">
      <router-link class="nav-link" to="/about">about</router-link>
    </li>
     Dropdown
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
        Dropdown link
      </a>
      <div class="dropdown-menu">
        <a class="dropdown-item" href="#">Link 1</a>
        <a class="dropdown-item" href="#">Link 2</a>
        <a class="dropdown-item" href="#">Link 3</a>
      </div>
    </li> -->
  </ul>
  <div class="form-inline my-2 my-lg-0">
  <input class="form-control mr-sm-2" type="search" placeholder="Search" v-model="SearchData" >
  <button class="btn btn-outline-success " @click="HeaderSearch(true)" >Search</button>
  </div>
  </div>
</nav>
</template>

<script>
import { useStore } from 'vuex'
import Cookies from 'vue-cookie'

export default {
  inject: [
     'reload'
     ],
  data() {
      return {
          SearchData: ''
      };
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
  logout(){
      let me = this;
      Cookies.delete('username');
      Cookies.delete('TOKEN');
      me.reload();
  },
  HeaderSearch(flag){
      let me = this;
      let useStore = me.useStore;
      let state = me.useStore.state;
      let http = state.axios;
      let phpurl = useStore.getters.phpurl;
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
      let data = new URLSearchParams();
      data.append('commandType', "getAticle");
      data.append('SEARCHTYPE', state.SEARCHTYPE);
      data.append('KEYWORD', state.KEYWORD);
      
      state.noDataFlag = true;
      state.list = [];
      http.post(phpurl("Command"),data)
      .then(function(response){
       let success = response.data.success;
       if (success == "1"){
           let result = response.data.result;
              result.forEach(element => {
                  state.list.push(element);
              });
           state.noDataFlag = false;
       }
      })
      .catch(function (error) {
       alert(error);
      });
        
  }
  }
}
</script>