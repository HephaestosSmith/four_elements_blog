<template>
  <div class="container-fluid app-shell" id="main" :key="display">
    <div class="row app-header sticky-top" id="Header">
      <div class="col-12">
        <router-view name="Header"></router-view>
      </div>
    </div>

    <div class="row app-layout">
      <div class="d-none d-md-block col-md-2 bd-toc"></div>
      <div class="col-md-8 main">
        <router-view></router-view>
      </div>
      <div class="d-none d-xl-block col-xl-2 bd-toc">
        <router-view name="RightList"></router-view>
      </div>

      <div class="modal fade" id="ModalView">
        <div class="modal-dialog modal-xl">
          <div class="modal-content app-modal-content">
            <div class="col-sm-12 sticky-10 modal-close-row">
              <button type="button" class="close text-danger" data-dismiss="modal">X</button>
            </div>

            <div class="modal-body modal-body-scrollable">
              <router-view name="modal"></router-view>
            </div>
          </div>
        </div>
      </div>
    </div>

    <FooterView></FooterView>
  </div>
</template>
<script>
import { useStore } from 'vuex'
import { Modal } from "bootstrap"
import FooterView from './views/FooterView.vue'

export default {
  provide(){
    return {
      reload: this.reload,
      conection: this.conection,
      modalshow:this.modalshow,
      PrismView:this.PrismView
    }
  },
  components:{
    FooterView
  },
  data() {
    return {
      display: 0
     }
  },
  created() {
     document.body.className = 'app-body';
     this.useStore = useStore();
  },
  methods: {
    reload () {
      this.display++;
    },
    conection (data,response,command="Command") {
      let me = this;
      let useStore = me.useStore;
      let http = useStore.state.axios;
      let phpurl = useStore.getters.phpurl;

      http.post(phpurl(command),data)
      .then(response)
      .catch(function (error) {
       alert(error);
      });
    },
    modalshow(){
      let ModalView = new Modal(document.getElementById("ModalView"))
      if(document.getElementsByClassName("show").length == 0){
         ModalView.show();
      }
    }
  }
}
</script>
<style scoped lang="scss">
.ck-editor__editable {
    min-height: 300px;
}
img{
  max-height: 100%;
  max-width: 100%;
}
</style>
