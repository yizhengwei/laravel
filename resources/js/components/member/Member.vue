<template>
    <div>
        <!-- 面包屑-->
        <el-breadcrumb separator="/">
            <el-breadcrumb-item :to="{ path: '/index' }">首页</el-breadcrumb-item>
            <el-breadcrumb-item>会员管理</el-breadcrumb-item>
            <el-breadcrumb-item>会员列表</el-breadcrumb-item>
        </el-breadcrumb>

        <el-card>

            <el-row :gutter="20">
                <el-col :span="6">
                    <el-input placeholder="请输入会员手机号" v-model="params.q" clearable @clear="getMemberList" size="mini">
                        <el-button slot="append" icon="el-icon-search"  @click="getMemberList"></el-button>
                    </el-input>
                </el-col>
            </el-row>


            <el-table
                :data="memberList"
                stripe
                border
                style="width: 100%">
                <el-table-column type="index" label="#"></el-table-column>
                <el-table-column label="会员头像">
                    <template slot-scope="scope">
                        <img
                            :src="scope.row.avatarUrl"
                            v-if="scope.row.avatarUrl!=''"
                            style="height: auto; width:50px; height:50px">
                        <img v-else src="/images/logo.png" style="height: auto;">

                    </template>
                </el-table-column>

                <el-table-column label="会员昵称" prop="nickname"></el-table-column>
                <el-table-column label="会员电话" prop="phone"></el-table-column>
                <el-table-column label="等级" >初级会员</el-table-column>
                <el-table-column label="来源" >微信小程序</el-table-column>
                <el-table-column label="注册时间" prop="register_time"></el-table-column>
                <el-table-column label="操作">
                    <template v-slot="scope">
                        <el-button
                            type="text"
                            size="mini"
                        >
                            <span style="color:#1890FF">详情</span>
                        </el-button>
                        <el-button
                            type="text"
                            size="mini"
                        >
                            <span style="color:red">加入黑名单</span>
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>

            <el-pagination
                @size-change="handleSizeChange"
                @current-change="handleCurrentChange"
                :current-page="params.p"
                :page-sizes="[10, 20, 30, 40]"
                :page-size="params.limit"
                layout="total, sizes, prev, pager, next, jumper"
                :total="total">
            </el-pagination>

        </el-card>

    </div>
</template>

<script>
    export default {
        data() {
            return{
                params: {
                    q: '',
                    p: 1,
                    limit: 10,

                },
                total: 0,
                memberList: [],
            }
        },

        methods: {

            handleSizeChange(newSize) {
                this.params.limit = newSize
                this.getOrderList()
            },

            handleCurrentChange(newPage) {
                this.params.p = newPage
                this.getOrderList()
            },

            async getMemberList() {
                const {data: res} = await this.$http.get('/getMemberList',{params: this.params})
                if (res.status != 1) return this.$message.error(res.msg);
                this.memberList = res.content.data;
                this.total = res.content.total;
            }
        },

        mounted() {
            this.getMemberList();
        }

    }
</script>

<style scoped>

</style>
