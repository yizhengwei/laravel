<template>
    <div>
        <!-- 面包屑-->
        <el-breadcrumb separator="/">
            <el-breadcrumb-item :to="{ path: '/index' }">首页</el-breadcrumb-item>
            <el-breadcrumb-item>订单管理</el-breadcrumb-item>
            <el-breadcrumb-item>订单列表</el-breadcrumb-item>
        </el-breadcrumb>

        <el-card>
            <el-row :gutter="20">
                <el-col :span="6">
                    <el-input placeholder="输入订单内容" v-model="params.q" clearable @clear="getOrderList" size="mini">
                        <el-button slot="append" icon="el-icon-search"  @click="getOrderList"></el-button>
                    </el-input>
                </el-col>


            </el-row>
            <el-table
                :data="orderList"
                stripe
                border
                style="width: 100%">
                <el-table-column type="index" label="#"></el-table-column>
                <el-table-column label="订单编号"  prop="order_no"></el-table-column>
                <el-table-column label="订单价格"  prop="sales_num"></el-table-column>
                <el-table-column label="下单时间"  prop="buy_time"></el-table-column>
                <el-table-column label="操作">
                    <template v-slot="scope">
                        <el-button
                            type="text"
                            size="mini"
                        >
                            <span style="color:#1890FF" @click="detail(scope.row.id)">详情</span>
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
                orderList: [],
            }
        },

        methods: {
            //分页
            handleSizeChange(newSize) {
                this.params.limit = newSize
                this.getOrderList()
            },

            handleCurrentChange(newPage) {
                this.params.p = newPage
                this.getOrderList()
            },

            getOrderList() {
                var that = this;
                this.$http.get('/getOrderList',{params: this.params}).then(res => {
                    if (res.data.status != 1) return this.$message.error(res.data.msg);
                    that.orderList = res.data.content.data;
                    that.total = res.data.content.total;
                })
            },


            // async getOrderList() {
            //     var that = this;
            //     const {data: res} = await this.$http.get('/getOrderList',{params: this.params})
            //     if (res.status != 1) return this.$message.error(res.msg);
            //     that.orderList = res.content.data;
            //     that.total = res.content.total;
            // },

            detail(val) {
                if(val!=''){
                    // console.log(val)
                    // window.location.href="#/edit?id="+val;
                    this.$router.push({
                        path: '/detail',
                        query: {
                            id: val
                        }
                    })
                }else{
                    window.location.href="#/detail";
                }
            },
        },

        mounted() {
            this.getOrderList();
        }
    }

</script>

<style scoped>

</style>
