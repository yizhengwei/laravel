import VueRouter from "vue-router";
import Vue from "vue";
import Login from "../components/Login"
import Index from "../components/Index";
import Welcome from "../components/Welcome";

import User from "../components/user/User";

import Category from "../components/shop/Category";
import List from "../components/shop/List";
import Create from "../components/shop/Create";
import Role from "../components/power/Role";
import Test1 from "../components/shop/Test1";
import Order from "../components/order/Order";
import Member from "../components/member/Member";
import Detail from "../components/order/Detail";

//
Vue.use(VueRouter);

const router =  new VueRouter({
    routes:[
        {
            path: '/',
            redirect: '/login',
        },
        {
            path: '/login',
            component: Login
        },


        {
            path: '/index',
            component: Index,
            redirect: '/welcome',
            children: [
                {path: '/welcome', component: Welcome},
                {path: '/user', component: User},
                {path: '/list',component: List},
                {path: '/category',component: Category},
                {path: '/edit', component: Create},
                {path: '/role', component: Role},
                {path: '/test', component: Test1},
                {path: '/order', component: Order},
                {path: '/member', component: Member},
                {path: '/detail', component: Detail}
                ],
        }

    ],
    // mode: 'history',

})

//路由导航守卫

router.beforeEach((to, from, next) => {
    //to 将要访问的路径;
    //from 从哪个路径挑战而来
    //next 是一个函数  表示放行  next('/login') 强制跳转

    if(to.path === '/login') return next();
    const role_id = window.sessionStorage.getItem('role_id');
    if(!role_id) return next('/login');
    next();

})

export default router

