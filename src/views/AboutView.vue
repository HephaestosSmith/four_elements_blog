<template>
 123
</template>

<script>
import { useStore } from 'vuex'
export default {
  created() {
      const http = useStore().state.axios;
      const phpurl = useStore().getters.phpurl;

      http.post(phpurl("CreateTable"))
      .then(function(response){
      console.log(response.data);
      })
      .catch(function (error) { // 请求失败处理
       console.log(error);
      });
  }
}
</script>
