import Vue from 'vue'
import axios from 'axios'

Vue.prototype.$http = axios
Vue.prototype.$phpurl = phpurl();

function phpurl(name){
  return name = './api/controllers/' + name + '.php';
}