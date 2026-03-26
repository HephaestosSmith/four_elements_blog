<template>
  <div id="main" :key="display" class="min-h-screen bg-slate-950 text-slate-100">
    <div id="Header" class="sticky top-0 z-40">
      <router-view name="Header"></router-view>
    </div>

    <div class="mx-auto grid w-full max-w-7xl grid-cols-1 gap-4 px-3 pb-8 md:grid-cols-[220px_minmax(0,1fr)] xl:grid-cols-[220px_minmax(0,1fr)_260px]">
      <aside class="hidden md:block"></aside>

      <main class="min-w-0">
        <router-view></router-view>
      </main>

      <aside class="hidden xl:block">
        <router-view name="RightList"></router-view>
      </aside>
    </div>

    <div
      id="ModalView"
      class="app-modal-mask"
      :class="{ show: modalVisible }"
      tabindex="-1"
      @keydown.esc="modalVisible = false"
      @click.self="modalVisible = false"
    >
      <div class="modal-dialog modal-xl">
        <div class="modal-content app-modal-content rounded-2xl border border-slate-700 bg-slate-900">
          <div class="modal-close-row px-4 pt-3 text-right">
            <button type="button" class="rounded-lg border border-slate-700 px-3 py-1 text-sm text-slate-300 transition hover:bg-slate-800" @click="modalVisible = false">關閉</button>
          </div>

          <div class="modal-body modal-body-scrollable px-2 pb-4">
            <router-view name="modal"></router-view>
          </div>
        </div>
      </div>
    </div>

    <FooterView></FooterView>
  </div>
</template>
<script>
import { useStore } from 'vuex'
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
      display: 0,
      modalVisible: false
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
      this.modalVisible = true;
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
