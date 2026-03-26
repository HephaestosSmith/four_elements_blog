<template>
    <div class="rounded text-wrap article row" v-if="loginstatus()">
      <div class="col">
         <div class="row">
             <div class="col">
             <ckeditor :editor="editor" v-model="editorData" :config="editorConfig"></ckeditor>
             </div>
         </div>
        <div class="spacer-8"></div>
         <div class="row">
             <div class="col-sm-2 text-white mt-5px">
               文章顯示:
             <select class="form-select" v-model="POWER">
               <option value="0">公開</option>
               <option value="1" selected>不公開</option>
             </select>
             </div>
             <div class="col-sm-4 mt-5px">
               <div class="row">
                  <div class="col-4 pr-5 pl-10">
                     <select class="form-select w-inherit"  v-model="MAINCATEGORY">
                       <option value=""></option>
                       <option  v-for="(item,index) in MAINCATEGORYS" :key="index">{{ item }}</option>
                     </select>
                  </div>
                  <div class="col-8 pr-0 pl-0">
                     <input type="text" class="form-control h-25" placeholder="主分類"  v-model="MAINCATEGORY" >
                  </div>
               </div>
             </div>
             <div class="col-sm-4 mt-5px">
               <div class="row">
                  <div class="col-4 pr-5 pl-10">
                     <select class="form-select w-inherit"  v-model="SUBCATEGORY">
                       <option value=""></option>
                       <option  v-for="(item,index) in SUBCATEGORYS" :key="index">{{ item }}</option>
                     </select>
                  </div>
                  <div class="col-8 pr-0 pl-0">
                     <input type="text" class="form-control h-25" placeholder="子分類"  v-model="SUBCATEGORY" >
                  </div>
               </div>
             </div>
             <div class="col-sm-2 mt-5px">
             <a class="btn btn-primary h-35 mr-5px" @click="PrismView()">預覽</a>
             <a class="btn btn-primary h-35" @click="post()">發表</a>
             </div>
         </div>
      </div>
    </div>
    <div v-for="(item,index) in this.useStore.state.list" :key="index" >
      <HomeAticleItem :item="item" :key="item.UUID" />
    </div>
    <div class="row">
      <div class="col">
         <div class="spacer-20"></div>
      </div>
    </div>
    <div class="row" v-if="this.useStore.state.homeloadflag">
      <div class="col d-flex justify-content-center">
        <div class="spinner-grow text-primary m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-success m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-danger m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-warning m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-info m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-light m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-dark m-5" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>
    </div>
    <div class="rounded text-wrap article row"  v-if="noData()">
             <div class="col-12 text-white text-center fw-bold">
               查無資料
             </div>
    </div>
</template>
<script>
import { useStore } from 'vuex'
import HomeAticleItem from '../components/HomeAticleItem.vue'


export default {
  inject: [
     'conection'
     ],
  data() {
      return {
          POWER:'1',
          editor: '',
          editorData: '',
          editorConfig: '' ,
          SUBCATEGORY:'',
          SUBCATEGORYS:[],
          MAINCATEGORY:'',
          MAINCATEGORYS:[],
          listcount:0,
          list:[],
          logined:false
      };
  },
  components: {
    HomeAticleItem
  },
  watch:{
       MAINCATEGORY: function (){
         this.getSUBCATEGORYS();
       },
       logined: function (){
         if(this.logined){
           this.getMAINCATEGORYS();
         }
       }
  },
  created(){
     let me = this;
     me.useStore = useStore();
     me.useStore.state.list = [];
     me.list = me.useStore.state.list;
     me.useStore.state.homeloadflag = true;
     me.editor = me.useStore.state.CKEditor;
     me.editorConfig = me.useStore.state.editorConfig;
     me.Logined();
  },
  methods:{
      loginstatus(){
         this.logined = this.useStore.state.logined;
         return this.logined;
      },
      Logined(){
      let me = this;
      let state = me.useStore.state;

      if (!state.Createflag){
        state.Createflag = true;
        window.addEventListener('scroll', this.handleScroll, true);
      }

      state.SEARCHTYPE = 'default';
      me.getAticle(false);
  },
  getAticle(postflag/*檢查是否發文章*/){
      let me = this;
      let state = me.useStore.state;
      
      let data = new URLSearchParams();
      data.append('commandType', "getAticle");
      data.append('SEARCHTYPE', state.SEARCHTYPE);
      data.append('KEYWORD', state.KEYWORD);

      //取得新的文章
      if(!postflag){
          if(state.list.length > 0){
              let nowData = state.list[state.list.length - 1];
              let lastCreatedate = nowData["CREATETIME"];
              data.append('CREATETIME', lastCreatedate);
          }
      }
      
      me.conection(data,function(response){
       let success = response.data.success;
       if (success == "1"){
           me.useStore.state.homeloadflag = false;
           let result = response.data.result;
           if(postflag){
               state.list = state.list.reverse();
               state.list.push(result[0]); 
               state.list = state.list.reverse();
           } else{
              result.forEach(element => {
                  state.list.push(element);
              });
           }
           if (result.length == 0){
             state.noDataFlag = true;
           }
       }
       else{
          let msg =response.data.msg;
          me.useStore.state.homeloadflag = false;
          alert(msg);
       }
      });
  },
  post(){
      let me = this;
      
      if ( me.editorData == ""){
        alert("請輸入文章");
        return;
      }

      if (me.MAINCATEGORY != ""){
          if (me.SUBCATEGORY == ""){
            alert("請選擇或輸入子分類");
            return;
          }
      }

      let data = new URLSearchParams();
      data.append('commandType', "post");
      data.append('content', me.editorData);
      data.append('POWER',me.POWER);
      data.append('MAINCATEGORY',me.MAINCATEGORY);
      data.append('SUBCATEGORY',me.SUBCATEGORY);
      
      me.conection(data,function(response){
       let success = response.data.success;
       if (success == "1"){
           me.editorData = "";
           me.getAticle(true);
           me.getMAINCATEGORYS();
           me.MAINCATEGORY = "";
           me.SUBCATEGORY = "";
       }
       else{
          let msg =response.data.msg;
          alert(msg);
       }
      });
  },
  handleScroll(){
      let me = this;
      let state = me.useStore.state;
      if (window.scrollY + document.documentElement.clientHeight >= document.body.scrollHeight - 5) {
          if (!me.useStore.state.homeloadflag & !state.noDataFlag){
              me.useStore.state.homeloadflag = true;
              me.getAticle(false);
          }
      }
  },
  getMAINCATEGORYS(){
      let me = this;
      let data = new URLSearchParams();
      data.append('commandType', "getMAINCATEGORYS");
      
      me.conection(data,function(response){
       let success = response.data.success;
       if (success == "1"){
           let result = response.data.result;
           let MAINCATEGORYS = [];
           result.forEach(element => {
               if (element["CATEGORYNAME"]!=""){
                   if(element["MAINCATEGORYID"] == 0){
                      MAINCATEGORYS.push(element["CATEGORYNAME"]);
                   }
               }
           });
           me.MAINCATEGORYS = MAINCATEGORYS;
       }
       else{
          let msg =response.data.msg;
          me.useStore.state.homeloadflag = false;
          alert(msg);
       }
      })
  },
  getSUBCATEGORYS(){
      let me = this;
      let data = new URLSearchParams();
      data.append('commandType', "getSUBCATEGORYS");
      data.append('MAINCATEGORY', me.MAINCATEGORY);
      
      me.conection(data,function(response){
       let success = response.data.success;
       if (success == "1"){
           let result = response.data.result;
           let SUBCATEGORYS = [];
           result.forEach(element => {
               if (element["CATEGORYNAME"]!=""){
                   if(element["MAINCATEGORYID"] != 0){
                      SUBCATEGORYS.push(element["CATEGORYNAME"]);
                   }
               }
           });
           me.SUBCATEGORYS = SUBCATEGORYS;
       }
       else{
          let msg =response.data.msg;
          me.useStore.state.homeloadflag = false;
          alert(msg);
       }
      })
  },
  Delete(UUID){
      if(!confirm("是否刪除文章?")){
        return;
      }
      let me = this;
      let state = me.useStore.state;
      
      let data = new URLSearchParams();
      data.append('commandType', "delete");
      data.append('UUID', UUID);
      
      me.conection(data,function(response){
       let success = response.data.success;
       if (success == "1"){
             let list = [];
             list = state.list.filter(function(item) {
                 return item.UUID !== UUID
             });
             state.list = list;
       }
       else{
          let msg =response.data.msg;
          alert(msg);
       }
      })
  },
  noData(){
    let state = this.useStore.state;
    if(state.list.length == 0 && !state.homeloadflag){
      return true;
    }else{
      return false;
    }
  }
  }
}
</script>