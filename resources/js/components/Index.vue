<template>
    <el-container class="home-container">
        <!-- 头部 -->
        <el-header>
           <div>
               <img src="../../image/school.png">
               <span>后台商城管理系统</span>

               <el-button type="text" @click="logout">退出登录</el-button>
           </div>

        </el-header>
        <!-- 内容-->
        <el-container>
        <!-- 侧边栏-->
            <el-aside :width="isCollapse ? '64px' : '200px'" >
                <div class="toggle-button" @click="toggleCollapse">|||</div>
                <el-menu
                    background-color="#D3DCE6"
                    text-color="#fffff"
                    :router="true"
                    unique-opened
                    :collapse="isCollapse"
                    :collapse-transition="false"
                    >
                    <!--一级菜单-->
                    <el-submenu :index="item.id + '' " v-for="item in menulist" :key="item.id">
                         <!--一级菜单模版区域-->
                        <template slot="title">
                            <i :class="item.icon"></i>
                            <span>{{item.name}}</span>
                        </template>

                        <el-menu-item :index="subItem.path + ''" v-for="subItem in item.list" :key="subItem.id">
                            <template slot="title">
                                <span>{{subItem.name}}</span>
                            </template>
                        </el-menu-item>

                    </el-submenu>

                </el-menu>
            </el-aside>
        <!-- 右侧内容主体-->
            <el-main>
                <router-view></router-view>
            </el-main>
        </el-container>
    </el-container>
</template>

<script>
    export default {
        data() {
            return {
                menulist: [],
                isCollapse: false
            };
        },
        created() {
            this.getMenuList();
        },
        methods: {
            async getMenuList() {
                const {data: res} = await this.$http.post('/getMenuList')
                if (res.status != 1) return this.$message.error(res.msg);
                this.menulist = res.content;
            },

            //点击菜单按钮的展开和收缩实现
            toggleCollapse() {
                this.isCollapse = !this.isCollapse;
            },

            logout() {
                window.sessionStorage.clear();
                this.$router.push('/login');
            }
        }
    }

</script>

<style scoped>

    .home-container{
        height: 100%;
    }
    .el-header {
        background-color: #B3C0D1;
        color: #333;
        line-height: 50px;
        padding-left:0;
    }

    .el-header img{
        width: 20%;
        height: 20%;
        /*display: inline-block;*/
        vertical-align: middle;
    }

    .el-header span{
        margin-left:360px;
        color:#0d1e5b;


    }

    .el-header .el-button{
        float: right;
        line-height: 30px;
        vertical-align: middle;
        color:#0d1e5b;
    }

    .el-aside {
        background-color: #D3DCE6;
        color: #333;
        line-height: 200px;
    }
    .el-main {
        background-color: #E9EEF3;
        color: #333;
    }
    .toggle-button{
        background-color: #D3DCE6;
        font-size: 10px;
        line-height: 24px;
        color: #B3C0D1;
        text-align:center;
        letter-spacing: 0.5em;
        cursor: pointer;
    }
</style>
