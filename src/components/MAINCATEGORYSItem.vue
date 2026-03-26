<template>
  <div class="rounded-2xl border border-slate-800/80 bg-slate-900/70 p-4 shadow-xl">
    <h5 class="mb-3 text-lg font-semibold text-slate-100">分類</h5>
    <div class="mb-2" v-for="(item,index) in MAINCATEGORYS" :key="index">
      <div class="cursor-pointer rounded-lg px-3 py-2 font-medium text-slate-200 transition hover:bg-indigo-500/20" @click="item.ISSHOW = !item.ISSHOW">
        {{item.CATEGORYNAME}}
      </div>
      <div class="ml-4 overflow-hidden border-l border-slate-700 pl-2 transition-all" :class="{ 'max-h-96': item.ISSHOW, 'max-h-0': !item.ISSHOW }">
        <div class="cursor-pointer rounded-lg px-3 py-2 text-sm text-slate-400 transition hover:bg-indigo-500/20 hover:text-white" v-for="(subitem,subindex) in item.SUBCATEGORYS" :key="subindex" @click="SearchCATEGORY(subitem.CATEGORYNAME)">{{subitem.CATEGORYNAME}}</div>
      </div>
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
  data() {
      return {
          MAINCATEGORYS:[],
          useStore:[]
      };
  },
  watch:{
       "useStore.state.MAINCATEGORYS": function (){
        this.MAINCATEGORYS = this.useStore.state.MAINCATEGORYS;
       }
  },
  created(){
     let me = this;
     me.useStore = useStore();
  },
  methods:{
  SearchCATEGORY(CATEGORY){
    let me = this;
    let state = me.useStore.state;
    state.homeloadflag = true;
    state.list = [];

    state.SEARCHTYPE = "CATEGORY";
    state.KEYWORD = CATEGORY;
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
     })
   }
  }
}
</script>
