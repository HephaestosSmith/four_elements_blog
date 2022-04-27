<template>
<div class="container-fluid" id="main"  v-if="isRouterAlive">
  <div class="row bg-dark sticky-top"  id="Header">
    <div class="col-12">
     <router-view name="Header"/>
    </div>
  </div>
  <div class="row">
    <div class="d-none d-md-block col-md-2 bd-toc">
    </div>
    <div class="col-md-8 main">
       <router-view/>
    </div>
    <div class="d-none d-xl-block col-xl-2 bd-toc">
       <router-view name="RightList" />
    </div>
  </div>
  <!--<div class="row"  id="Footer">
      <router-view name="Footer"/>
  </div>-->
</div>
</template>
<script>
export default {
  provide(){    
    return {
      reload: this.reload      
    }
  },
  data() {
    return {
      isRouterAlive: true,
     }
  },
  created() {
    //document.title = "四元素部落格";
    document.getElementsByTagName("body")[0].className="bg-secondary";
  },
  methods: {
    reload () {
      this.isRouterAlive = false;
      this.$nextTick( ()=> { this.isRouterAlive=true } ) ;
    }
  }
}
</script>
<style lang="scss">
#Footer{  
  background-color: rgb(45, 45, 45);
  color: rgb(112, 112, 112);
  text-align: left;
}
.article {
  background-color: rgba(245, 245, 245, 0.15);
  margin-top: 1rem;
  padding: 1rem 1rem 1rem 0.5rem;
}
.ck-editor__editable {
    min-height: 300px;
}
</style>
