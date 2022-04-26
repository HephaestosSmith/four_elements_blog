import { createStore } from 'vuex'
import axios from 'axios'
import Cookies from 'vue-cookie'

axios.defaults.withCredentials = true;
axios.defaults.baseURL='/api'
//app.provide('axios', axios);
//app.provide('phpurl', function(name) {    
//  return name = '/controllers/' + name + '.php';
//})

export default createStore({
  state: {
    axios:axios,
    phpPath:'/controllers/',
    phpType:'.php',
    Cookies:Cookies,
    logined:false,
    list:[],
    KEYWORD:'',
    noDataFlag:false,
    SEARCHTYPE:'default',
    Createflag:false
  },
  getters: {
    phpurl: (state) => (name) => {
      return state.phpPath + name + state.phpType
    }
  },
  mutations: {
  },
  actions: {
  },
  modules: {
  }
})
