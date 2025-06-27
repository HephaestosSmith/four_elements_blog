<template>
    <router-link class="text-decoration-none rounded text-wrap article text-white row" @click="loading(item.UUID)" :to="{ name: 'article', params: { UUID: item.UUID } }" :key="item.UUID" data-toggle="modal" data-target="#ModalView">
      <div class="col">
           <div class="row">
                <div class="col-6">
                   {{ item.CREATEDATE }}
                </div>
                <div class="col-6 text-right">
                   <!--<a class="dropdown"  v-if="loginstatus()">
                     <a class="btn btn-secondary dropdown-toggle h-35" data-toggle="dropdown">
                     </a>
                     <div class="dropdown-menu">
                       <router-link class="dropdown-item btn"  :to="{ name: 'edited', params: { UUID: item.UUID } }" data-toggle="modal" data-target="#ModalView">編輯</router-link>
                       <button class="dropdown-item btn" @click="Delete(item.UUID)">刪除</button>
                     </div>
                  </a>-->
                </div>
           </div>
           <div class="spacer-8"></div>
           <div class="row">
                <div class="col ck-content max-h-200" v-html= item.CONTENT ref="content">
                </div>
           </div>
           <div class="row" v-show="isLongContent">
              <div class="col ck-content text-black more-link" >... MORE</div>
           </div>
           <hr>
           <div class="row">
              <div class="col-7">
                    發文時間:{{ item.CREATETIME }}
              </div>
              <div class="col-5 text-right">
                  作者: {{item.AUTHOR}}
              </div>
           </div>
          </div>
      </router-link><!--</div>-->
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