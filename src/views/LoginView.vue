<script>
import { useStore } from 'vuex'
export default {
  data() {
    return {
      username: '',
      password: ''
    }
  },
  created() {
      this.useStore = useStore();
  },
  methods:{
  login(){
      let me = this;
      let useStore = me.useStore;
      let http = useStore.state.axios;
      let phpurl = useStore.getters.phpurl;
      let Cookies = useStore.state.Cookies;
      let data = new URLSearchParams();
      data.append('commandType', "login");
      data.append('username', me.username);
      data.append('password', me.password);

      http.post(phpurl("Command"),data)
      .then(function(response){
       let success = response.data.success;
       let result = response.data.result;
       if (success == "1"){
           Cookies.set('TOKEN',result.TOKEN, { expires: 1 });
           Cookies.set('username',result.username, { expires: 1 });
           Cookies.set('authorname',result.authorname, { expires: 1 });
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
<template>
    <div class="rounded text-wrap article">
        <form class="col-5">
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="Username" v-model="username">
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Password" v-model="password">
        </div>
        <a class="btn btn-info" @click="login()"  style="width:100px; height:35px;">登入</a>
        </form>
    </div>
</template>

