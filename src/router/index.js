import Vue from 'vue';
import Router from 'vue-router';
const _import = require('./_import_' + process.env.NODE_ENV);
// in development env not use Lazy Loading,because Lazy Loading large page will cause webpack hot update too slow.so only in production use Lazy Loading

/* layout */
//const Layout = _import('layout/Layout');
const Layout = resolve => require.ensure([], () => resolve(require('../views/layout/Layout')), 'Layout');

/* login */
//const Login = _import('login/index');
/*const authRedirect = _import('login/authredirect');*/
const Login = resolve => require.ensure([], () => resolve(require('../views/login/index')), 'Login');

//const Index = _import('index/index');
const Index = resolve => require.ensure([], () => resolve(require('../views/index/index')), 'Index');

//const PostDetail = _import('post/detail/index');
//const PostPublish = _import('post/publish/index');
const PostDetail = resolve => require.ensure([], () => resolve(require('../views/post/detail/index')), 'PostDetail');
const PostPublish = resolve => require.ensure([], () => resolve(require('../views/post/publish/index')), 'PostPublish');
const PostEdit = resolve => require.ensure([], () => resolve(require('../views/post/edit/index')), 'PostEdit')

//const GroupDetail = _import('group/detail/index');
//const GroupCreate = _import('group/create/index');
const GroupDetail = resolve => require.ensure([], () => resolve(require('../views/group/detail/index')), 'GroupDetail');
const GroupCreate = resolve => require.ensure([], () => resolve(require('../views/group/create/index')), 'GroupCreate');
const GroupSet = resolve => require.ensure([], () => resolve(require('../views/group/toset/index')), 'GroupSet');

//const Inform = _import('inform/index');
//const InviteCode = _import('invitecode/index');
const Inform = resolve => require.ensure([], () => resolve(require('../views/inform/index')), 'Inform');
const InviteCode = resolve => require.ensure([], () => resolve(require('../views/invitecode/index')), 'InviteCode');

//const Collection = _import('collection/index');
const Collection = resolve => require.ensure([], () => resolve(require('../views/collection/index')), 'Collection');

//import PersonalData from 'views/personalData'
const PersonalData = resolve => require.ensure([], () => resolve(require('../views/personalData/index')), 'PersonalData');

//import Signup from 'views/signup'
const Signup = resolve => require.ensure([], () => resolve(require('../views/signup/index')), 'Signup');

//import FindPassword from 'views/findpassword'
const FindPassword = resolve => require.ensure([], () => resolve(require('../views/findpassword/index')), 'FindPassword');

//import Resetpsw from 'views/resetpsw'
const Resetpsw = resolve => require.ensure([], () => resolve(require('../views/resetpsw/index')), 'Resetpsw');

//import Search from 'views/search'
const Search = resolve => require.ensure([], () => resolve(require('../views/search/index')), 'Search');

//const AllGroups = _import('group/allGroups/index');
const AllGroups = resolve => require.ensure([], () => resolve(require('../views/group/allGroups/index')), 'AllGroups');

/* Introduction */
//const Introduction = _import('introduction/index');
const Introduction = resolve => require.ensure([], () => resolve(require('../views/introduction/index')), 'Introduction');

/* components */
//const componentsIndex = _import('components/index');
//const Markdown = _import('components/markdown');
const componentsIndex = resolve => require.ensure([], () => resolve(require('../views/components/index')), 'componentsIndex');
const Markdown = resolve => require.ensure([], () => resolve(require('../views/components/markdown')), 'Markdown');

/* error page */
//const Err404 = _import('error/404');
//const Err401 = _import('error/401');
const Err404 = resolve => require.ensure([], () => resolve(require('../views/error/404')), 'Err404');
const Err401 = resolve => require.ensure([], () => resolve(require('../views/error/401')), 'Err401');

/* error log */
//const ErrorLog = _import('errlog/index');
const ErrorLog = resolve => require.ensure([], () => resolve(require('../views/errlog/index')), 'ErrorLog');

/* permission */
//const Permission = _import('permission/index');
const Permission = resolve => require.ensure([], () => resolve(require('../views/permission/index')), 'Permission');

Vue.use(Router);

 /**
  * icon : the icon show in the sidebar
  * hidden : if hidden:true will not show in the sidebar
  * redirect : if redirect:noredirect will not redirct in the levelbar
  * noDropdown : if noDropdown:true will not has submenu
  * meta : { role: ['admin'] }  will control the page role
  **/

export const constantRouterMap = [
  /*{ path: '/authredirect', component: authRedirect, hidden: true },*/
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
    path: '/post',
    name: 'post',
    component: Layout,
    children: [
      { 
        path: 'publish', component: PostPublish
      }, 
      {
        path: ':id/edit', component: PostEdit
      },
      { 
        path: ':id', 
        component: PostDetail,
      }
    ],
  },
  {
    path: '/group',
    name: 'group',
    component: Layout,
    children: [{ path: 'create', component: GroupCreate }, { path: ':id', component: GroupDetail}],
  },
  {
    path: '/set',
    name: 'set',
    component: Layout,
    children: [{ path: '', component: GroupSet }]
  },
  {
    path: '/inform',
    component: Layout,
    children: [{ path: '', name: 'inform', component: Inform}],
  },
  {
    path: '/invitecode',
    component: Layout,
    children: [{ path: '', name: 'invitecode',component: InviteCode}],
  },
  {
    path: '/collection',
    component: Layout,
    children: [{ path: '', name: 'collection', component: Collection}],
  },
  {
    path: '/signup',
    component: Layout,
    children: [{ path: '', component: Signup}],
  },
  {
    path: '/personalData',
    name: 'personalData',
    component: Layout,
    hidden: true,
    children: [{ path: '', component: PersonalData}],
  },
  {
    path: '/findpassword',
    name: 'findpassword',
    component: Layout,
    redirect: '/findpassword/index',
    hidden: true,
    children: [{ path: 'index', component: FindPassword}],
  },
  {
    path: '/resetpsw',
    name: 'resetpsw',
    component: Layout,
    redirect: '/resetpsw/index',
    hidden: true,
    children: [{ path: 'index', component: Resetpsw}],
  },
  {
    path: '/search',
    component: Layout,
    hidden: true,
    children: [{ path: '', name: 'search', component: Search}],
  },
  {
    path: '/allgroups',
    component: Layout,
    hidden: true,
    children: [{ path: '', name: 'allgroups', component: AllGroups}],
  },
]

export default new Router({
  // mode: 'history', //后端支持可开
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRouterMap
});
