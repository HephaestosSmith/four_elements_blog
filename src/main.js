import { createApp } from 'vue'
import App from './App.vue'
import store from './store'
import router from './router'
import './css/custom.css'
import axios from 'axios'
import Cookies from 'vue-cookie'
import CKEditor from '@ckeditor/ckeditor5-vue'

axios.defaults.baseURL='/api'

const app = createApp(App)

router.beforeEach((to, from, next) => {
      if (to.meta.installAuth) {
        let data = new URLSearchParams();
        data.append('name',to.name);
        data.append('param',JSON.stringify(to.params));
        axios.post('/controllers/install.php',data).then((response) => {
          if (response.data.success == "1") {
            next({path: '/'});
          }
          else{
            next();
          }
        });
      }else if (to.meta.loginAuth) {
        let data = new URLSearchParams();
        data.append('commandType', "check");
        data.append('username', Cookies.get('username'));
        data.append('TOKEN', Cookies.get('TOKEN'));
        axios.post('/controllers/Command.php',data).then((response) => {
          if (response.data.success == "0") {
            Cookies.delete('username');
            Cookies.delete('TOKEN');
            next();
          }
          else{
            next({path: '/'});
          }
        });
      }else if(to.meta.requiresAuth){
        let data = new URLSearchParams();
        data.append('commandType', "checkPOWER");
        data.append('username', Cookies.get('username'));
        data.append('TOKEN', Cookies.get('TOKEN'));
        data.append('UUID',to.params.UUID)
        axios.post('/controllers/Command.php',data).then((response) => {
          if (response.data.success == "1") {
            next();
          }else{
            next({path: '/'});
          }
        });
      }else{
        next();
      }
      if (to.meta.installedAuth) {
          let data = new URLSearchParams();
          data = new URLSearchParams();
          data.append('commandType',"getTitle");
          data.append('name',to.name);
          data.append('param',JSON.stringify(to.params));
          axios.post('/controllers/Command.php',data).then((response) => {
            if (response.data.success == "1") {
              let title = response.data.result.title;
              document.title = title;
            }else{
              next({path: '/install'});
            }
          }).catch(function (error){
            alert(error);
          });
        }

    });

app
  .use(router)
  .use(store)
  .use(CKEditor)
  .mount('#app');
