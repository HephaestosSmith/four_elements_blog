<template>
  <router-link
    class="article block rounded-2xl border border-slate-800/80 bg-slate-900/70 p-4 text-slate-100 no-underline transition hover:-translate-y-0.5 hover:border-indigo-500/60"
    @click="loading(item.UUID)"
    :to="{ name: 'article', params: { UUID: item.UUID } }"
    :key="item.UUID"
  >
    <div class="mb-2 flex items-center justify-between text-sm text-slate-400">
      <div>{{ item.CREATEDATE }}</div>
    </div>

    <div class="spacer-8"></div>

    <div class="ck-content max-h-200 overflow-hidden" v-html="item.CONTENT" ref="content"></div>

    <div class="mt-2" v-show="isLongContent">
      <div class="text-sm font-semibold text-indigo-300">... MORE</div>
    </div>

    <hr class="my-3 border-slate-700">

    <div class="flex items-center justify-between text-xs text-slate-400">
      <div>發文時間:{{ item.CREATETIME }}</div>
      <div>作者: {{item.AUTHOR}}</div>
    </div>
  </router-link>
</template>
<script>
import { useStore } from 'vuex'

export default {
  props: {
    item: {
      type: Object,
    },
  },
  data() {
      return {
          isLongContent:false,
          tempuuid:''
      };
  },
  watch:{
       "item.CONTENT": function (){
         this.$nextTick(function () {
           this.calculateHeight();
         });
       }
  },
  mounted() {
       let me = this;
       me.useStore = useStore();
       me.calculateHeight();
  },
  methods:{
  calculateHeight(){
          let contentHeight = this.$refs.content.clientHeight
          if (contentHeight >= 200) {
            this.isLongContent = true
          } else {
            this.isLongContent = false
          }
          this.useStore.commit('PrismView');
  },
  loading(uuid){
      let me = this;
      if(uuid != me.$route.params.UUID){
        me.useStore.state.modalloadflag = true;
      }
  }
  }
}
</script>
