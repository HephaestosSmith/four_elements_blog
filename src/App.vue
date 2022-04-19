<template>
<div class="container-fluid" id="main">
  <div class="row bg-dark"  id="Header">
     <router-view name="Header"/>
  </div>
  <div class="row"  style="height: 850px;">
    <div class="col-sm-1">
    </div>
    <div class="col-sm-8">
       <router-view/>
    </div>
    <div class="col-sm-1">
       <router-view  class="view RightList" name="RightList" />
    </div>
  </div>
  <!--<div class="row"  id="Footer">
      <router-view name="Footer"/>
  </div>-->
</div>
</template>
<script>
export default {
  created() {
    document.getElementsByTagName("body")[0].className="bg-secondary";
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
