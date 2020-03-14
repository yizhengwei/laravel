import VueRouter from "vue-router";
import Vue from "vue";
import Login from "../components/Login"
import Index from "../components/Index";
import Welcome from "../components/Welcome";

import User from "../components/user/User";

import Category from "../components/shop/Category";
import List from "../components/shop/List";
import Test from "../components/Test";

//
Vue.use(VueRouter);

export default new VueRouter({
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
                {path: '/test', component: Test},
                ],
        }

    ],
    // mode: 'history',

})

