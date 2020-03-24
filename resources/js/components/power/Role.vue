<template>
   <div>
       <!-- 面包屑-->
       <el-breadcrumb separator="/">
           <el-breadcrumb-item :to="{ path: '/index' }">首页</el-breadcrumb-item>
           <el-breadcrumb-item>权限管理</el-breadcrumb-item>
           <el-breadcrumb-item>角色列表</el-breadcrumb-item>
       </el-breadcrumb>

       <el-card>
           <el-table
               :data="roleList"
               style="width: 100%"
               border
               stripe
           >
               <!--展开列-->
               <el-table-column
               type="expand"
               width="80">
                </el-table-column>
               <el-table-column
                   prop="id"
                   label="#"
                   width="180">
               </el-table-column>
               <el-table-column
                   prop="name"
                   label="角色"
                   >
               </el-table-column>
               <el-table-column
                   prop="name_info"
                   label="描述"
                   >
               </el-table-column>
               <el-table-column
                   prop="name_info"
                   label="操作"
                   >
                   <template slot-scope="scope">
                       <el-button
                           type="text"
                           size="mini"
                       >
                           <span style="color:#1890FF">编辑</span>
                       </el-button>

                       <el-button
                           type="text"
                           size="mini"
                       >
                           <span style="color:#1890FF" >权限分配</span>
                       </el-button>


                       <el-button
                           type="text"
                           size="mini"
                       >
                           <span style="color:red">删除</span>
                       </el-button>
                   </template>
               </el-table-column>
           </el-table>
       </el-card>
   </div>
</template>

<script>
    export default {
        data() {
            return {
                roleList:[],
            }
        },

        methods: {

            async getRoleList() {
                const {data: res} =  await this.$http.post('/getRoleList');
                if(res.status != 1) return this.$message.error(res.msg);
                this.roleList = res.content;
            },

        },

        mounted() {
            this.getRoleList();
        },
    }
</script>

<style scoped>

</style>
