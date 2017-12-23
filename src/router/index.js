import Vue from 'vue';
import Router from 'vue-router';

const Layout = resolve => require.ensure([], () => resolve(require('../views/layout/Layout')), 'Layout');

const Login = resolve => require.ensure([], () => resolve(require('../views/login/index')), 'Login');

const Index = resolve => require.ensure([], () => resolve(require('../views/index/index')), 'Index');

const PostDetail = resolve => require.ensure([], () => resolve(require('../views/post/detail/index')), 'PostDetail');
const PostPublish = resolve => require.ensure([], () => resolve(require('../views/post/publish/index')), 'PostPublish');
const PostEdit = resolve => require.ensure([], () => resolve(require('../views/post/edit/index')), 'PostEdit')

const GroupDetail = resolve => require.ensure([], () => resolve(require('../views/group/detail/index')), 'GroupDetail');
const GroupCreate = resolve => require.ensure([], () => resolve(require('../views/group/create/index')), 'GroupCreate');
const GroupSet = resolve => require.ensure([], () => resolve(require('../views/group/toset/index')), 'GroupSet');

const Inform = resolve => require.ensure([], () => resolve(require('../views/inform/index')), 'Inform');
const InviteCode = resolve => require.ensure([], () => resolve(require('../views/invitecode/index')), 'InviteCode');

const Collection = resolve => require.ensure([], () => resolve(require('../views/collection/index')), 'Collection');

const PersonalData = resolve => require.ensure([], () => resolve(require('../views/personalData/index')), 'PersonalData');

const Signup = resolve => require.ensure([], () => resolve(require('../views/signup/index')), 'Signup');

const FindPassword = resolve => require.ensure([], () => resolve(require('../views/findpassword/index')), 'FindPassword');

const Resetpsw = resolve => require.ensure([], () => resolve(require('../views/resetpsw/index')), 'Resetpsw');

const Search = resolve => require.ensure([], () => resolve(require('../views/search/index')), 'Search');

const AllGroups = resolve => require.ensure([], () => resolve(require('../views/group/allGroups/index')), 'AllGroups');

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
    path: '/topic',
    name: 'topic',
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
        component: PostDetail
      }
    ]
  },
  {
    path: '/planet',
    name: 'planet',
    component: Layout,
    children: [{ path: 'create', component: GroupCreate }, { path: ':id', component: GroupDetail }]
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
    children: [{ path: '', name: 'inform', component: Inform }]
  },
  {
    path: '/invitecode',
    component: Layout,
    children: [{ path: '', name: 'invitecode', component: InviteCode }]
  },
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
  {
    path: '/findpassword',
    name: 'findpassword',
    component: Layout,
    redirect: '/findpassword/index',
    hidden: true,
    children: [{ path: 'index', component: FindPassword }]
  },
  {
    path: '/resetpsw',
    name: 'resetpsw',
    component: Layout,
    redirect: '/resetpsw/index',
    hidden: true,
    children: [{ path: 'index', component: Resetpsw }]
  },
  {
    path: '/search',
    component: Layout,
    hidden: true,
    children: [{ path: '', name: 'search', component: Search }]
  },
  {
    path: '/universe',
    component: Layout,
    hidden: true,
    children: [{ path: '', name: 'universe', component: AllGroups }]
  }
]

export default new Router({
  // mode: 'history', //后端支持可开
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRouterMap
});
