<template>
  <!--<div class="col-12">
    <a id="header">四元素部落格</a>
    <p><router-link to="/" class="btn btn-primary btn-xs" role="button">Home</router-link>
       | <router-link to="/about" class="btn btn-primary btn-xs" role="button">about</router-link></p>
  </div>-->
<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
  <!-- Brand -->
  <a class="navbar-brand" href="#">四元素部落格</a>
  <!-- Links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <router-link class="nav-link" to="/">首頁</router-link>
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
</nav>
</template>

<script>
import { useStore } from 'vuex'
import Cookies from 'vue-cookie'

export default {inject: [
     'reload'
     ],
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
  }
  }
}
</script>