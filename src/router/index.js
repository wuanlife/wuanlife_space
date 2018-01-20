import Vue from 'vue';
import Router from 'vue-router';

const Layout = resolve => require.ensure([], () => resolve(require('../views/layout/Layout')), 'Layout');

const Login = resolve => require.ensure([], () => resolve(require('../views/login/index')), 'Login'); // 登录
const Signup = resolve => require.ensure([], () => resolve(require('../views/signup/index')), 'Signup'); // 注册
// const Resetpsw = resolve => require.ensure([], () => resolve(require('../views/resetpsw/index')), 'Resetpsw'); // 重置密码
// const FindPassword = resolve => require.ensure([], () => resolve(require('../views/findpassword/index')), 'FindPassword'); // 找回密码

const Changepsw = resolve => require.ensure([], () => resolve(require('../views/changepsw/index')), 'Changepsw'); // 修改密码

const Index = resolve => require.ensure([], () => resolve(require('../views/index/index')), 'Index');

const ArticleDetail = resolve => require.ensure([], () => resolve(require('../views/articles/detail/index')), 'ArticleDetail');
// const ArticleDrafts
const EditorNewDraft = resolve => require.ensure([], () => resolve(require('../views/editor/drafts/new')), 'EditorNewDraft');
const EditorEdit = resolve => require.ensure([], () => resolve(require('../views/editor/edit')), 'EditorEdit')

// const Inform = resolve => require.ensure([], () => resolve(require('../views/inform/index')), 'Inform');

const Collection = resolve => require.ensure([], () => resolve(require('../views/collection/index')), 'Collection');

const PersonalData = resolve => require.ensure([], () => resolve(require('../views/personalData/index')), 'PersonalData');

const Search = resolve => require.ensure([], () => resolve(require('../views/search/index')), 'Search');

const MySpace = resolve => require.ensure([], () => resolve(require('../views/mySpace/index')), 'MySpace');

/* error page */
const Err404 = resolve => require.ensure([], () => resolve(require('../views/error/404')), 'Err404');
const Err401 = resolve => require.ensure([], () => resolve(require('../views/error/401')), 'Err401');

Vue.use(Router);

 /**
  * icon : the icon show in the sidebar
  * hidden : if hidden:true will not show in the sidebar
  * redirect : if redirect:noredirect will not redirct in the levelbar
  * noDropdown : if noDropdown:true will not has submenu
  * meta : { role: ['admin'] }  will control the page role
  **/

export const constantRouterMap = [
  /* { path: '/authredirect', component: authRedirect, hidden: true },*/
  { path: '/404', component: Err404, hidden: true },
  { path: '/401', component: Err401, hidden: true },
  {
    path: '/',
    component: Layout,
    redirect: '/index',
    name: '首页',
    hidden: true,
    children: [{
      path: 'index',
      component: Index
    }]
  },
  {
    path: '/login',
    component: Layout,
    children: [{ path: '', name: 'login', component: Login }]
  },
  {
    path: '/article',
    name: 'article',
    component: Layout,
    children: [
      {
        path: ':id',
        component: ArticleDetail
      }
    ]
  },
  {
    path: '/editor',
    name: 'editor',
    component: Layout,
    children: [
      {
        path: 'article/:id',
        component: EditorEdit
      }, {
        path: 'drafts/new',
        component: EditorNewDraft
      }
      // 后续加入drafts
    ]
  },
  // {
  //   path: '/inform',
  //   component: Layout,
  //   children: [{ path: '', name: 'inform', component: Inform }]
  // },
  {
    path: '/collection',
    component: Layout,
    children: [{ path: '', name: 'collection', component: Collection }]
  },
  {
    path: '/signup',
    component: Layout,
    children: [{ path: '', component: Signup }]
  },
  {
    path: '/personalData',
    name: 'personalData',
    component: Layout,
    hidden: true,
    children: [{ path: '', component: PersonalData }]
  },
  // {
  //   path: '/findpassword',
  //   name: 'findpassword',
  //   component: Layout,
  //   redirect: '/findpassword/index',
  //   hidden: true,
  //   children: [{ path: 'index', component: FindPassword }]
  // },
  // {
  //   path: '/resetpsw',
  //   name: 'resetpsw',
  //   component: Layout,
  //   redirect: '/resetpsw/index',
  //   hidden: true,
  //   children: [{ path: 'index', component: Resetpsw }]
  // },
  {
    path: '/changepsw',
    name: 'changepsw',
    component: Layout,
    redirect: '/changepsw/index',
    hidden: true,
    children: [{ path: 'index', component: Changepsw }]
  },
  {
    path: '/search',
    component: Layout,
    hidden: true,
    children: [{ path: '', name: 'search', component: Search }]
  },
  {
    path: '/myspace',
    component: Layout,
    children: [
      { path: '', component: MySpace },
      { path: ':id', component: MySpace }
    ]
  }
]

export default new Router({
  mode: 'history', // 后端支持可开
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRouterMap
});
