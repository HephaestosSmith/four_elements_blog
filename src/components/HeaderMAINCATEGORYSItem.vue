<template>
  <li class="group relative list-none">
    <div class="cursor-pointer rounded-lg px-3 py-2 text-slate-300 transition hover:bg-indigo-500/20 hover:text-white">
      分類
    </div>
    <div class="invisible absolute left-0 top-full z-50 mt-2 w-52 rounded-xl border border-slate-700 bg-slate-900/95 p-2 opacity-0 shadow-xl transition group-hover:visible group-hover:opacity-100">
      <div class="relative" v-for="(item,index) in MAINCATEGORYS" :key="index">
        <div class="rounded-lg px-3 py-2 text-slate-200">{{item.CATEGORYNAME}}</div>
        <div class="ml-2 border-l border-slate-700 pl-2">
          <a
            class="block cursor-pointer rounded-lg px-3 py-2 text-sm text-slate-400 transition hover:bg-indigo-500/20 hover:text-white"
            v-for="(subitem,subindex) in item.SUBCATEGORYS"
            :key="subindex"
            @click="SearchCATEGORY(subitem.CATEGORYNAME)"
          >{{subitem.CATEGORYNAME}}</a>
        </div>
      </div>
    </div>
  </li>
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
  created(){
     let me = this;
     me.useStore = useStore();
     me.getALLCATEGORYS();
  },
  methods:{
  getALLCATEGORYS(){
      let me = this;
      let data = new URLSearchParams();
      data.append('commandType', "getALLCATEGORYS");

      me.conection(data,function(response){
       let success = response.data.success;
       if (success == "1"){
           let result = response.data.result
           me.MAINCATEGORYS = result.filter(function(item) {
               return item.MAINCATEGORYID === 0
           });
           me.MAINCATEGORYS.forEach(element =>{
               let SUBCATEGORYS = {"SUBCATEGORYS":result.filter(function(item) {
                                                   return item.MAINCATEGORYID === element.CATEGORYINDEX
                                                }),"ISSHOW":false};
               element = Object.assign(element,SUBCATEGORYS)
             });
          me.useStore.state.MAINCATEGORYS = me.MAINCATEGORYS;
       }
       else{
          let msg =response.data.msg;
          me.loading = false;
          alert(msg);
       }
      })
  },
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
    data.append('KEYWORD',state.KEYWORD);

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
