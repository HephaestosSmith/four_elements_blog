<template>
  <!--<div class="col-12">
    <a id="header">四元素部落格</a>
    <p><router-link to="/" class="btn btn-primary btn-xs" role="button">Home</router-link>
       | <router-link to="/about" class="btn btn-primary btn-xs" role="button">about</router-link></p>
  </div>-->
<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
  <!-- Brand -->
  <a class="navbar-brand" href="#">四元素部落格</a>
  <!-- Links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <router-link class="nav-link" to="/">Home</router-link>
    </li>
    <li class="nav-item">
      <router-link class="nav-link" to="/login">login</router-link>
    </li>
    <li class="nav-item">
      <router-link class="nav-link" to="/about">about</router-link>
    </li>
 
    <!-- Dropdown
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
        Dropdown link
      </a>
      <div class="dropdown-menu">
        <a class="dropdown-item" href="#">Link 1</a>
        <a class="dropdown-item" href="#">Link 2</a>
        <a class="dropdown-item" href="#">Link 3</a>
      </div>
    </li> -->
  </ul>
</nav>
</template>