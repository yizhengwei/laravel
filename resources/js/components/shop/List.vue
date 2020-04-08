<template>
    <div>
        <!-- 面包屑-->
        <el-breadcrumb separator="/">
            <el-breadcrumb-item :to="{ path: '/index' }">首页</el-breadcrumb-item>
            <el-breadcrumb-item>商城中心</el-breadcrumb-item>
            <el-breadcrumb-item>商城列表</el-breadcrumb-item>
        </el-breadcrumb>

        <el-card>

            <div class="twentyEightWrap">
                <!-- 品牌 -->
                <div class="item">
                    <span>品牌：</span>
                    <div class="select_wrap">
                        <el-select v-model="brand" placeholder="请选择" clearable size="mini">
                            <el-option
                                v-for="item in brandList"
                                :key="item.id"
                                :label="item.brand_name"
                                :value="item.id">
                            </el-option>
                        </el-select>
                    </div>
                </div>
                <!-- 分类： -->
                <div class="item" style="margin-bottom: 16px;">
                    <span>分类：</span>
                    <div class="select_wrap" >
                        <el-cascader
                            v-model="cate_ids"
                            :options="categoryList"
                            :props="{value: 'id', label: 'name', children: 'list', checkStrictly: true}"
                            clearable
                            size="mini">
                        </el-cascader>
                    </div>
                </div>
                <!-- 是否有列表图 -->
                <div class="item">
                    <span>是否有列表图：</span>
                    <div class="select_wrap">
                        <el-select v-model="listMap" placeholder="请选择" clearable size="mini">
                            <el-option
                                v-for="item in listMapOptions"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value"
                                >
                            </el-option>
                        </el-select>
                    </div>
                </div>
                <!-- 商品编码 -->
                <div class="item">
                    <span>商品编码或款名：</span>
                    <div class="select_wrap select_wrap2" style="margin-left:-4px;width: 130px;">
                        <el-input size="mini" placeholder="请输入编码或款名" v-model.trim="goods_no" clearable></el-input>
                    </div>
                </div>

                <div style="display: inline-block; height: 28px;vertical-align: top;" >
                    <div class="sousuo comBtns" @click="searchData">搜 索</div>
                    <div class="chongzhi comBtns" @click="resetData">重 置</div>
                </div>

                <div class="twentyEightWrap">
                    <div class="manyBtn manyBtn2" @click="toCreate('')">新增商品</div>
                </div>
            </div>

            <div></div>

            <el-table
                ref="multipleTable"
                :data="tableData"
                tooltip-effect="dark"
                style="width: 100%"
                border
                @selection-change="handleSelectionChange">
                <el-table-column
                    type="selection"
                    width="55">
                </el-table-column>
                <el-table-column
                    label="商品">
                    <template slot-scope="scope">
                        <div class="shangpi">
                            <div class="left_img">
                                <img
                                    :src="scope.row.img"
                                    v-if="scope.row.img!=''"
                                    style="height: auto;">
                                <img v-else src="/images/logo.png" style="height: auto;">
                            </div>
                            <div class="right_content">
                                <div class="top_title">
                                    <a :href="'/#/edit?id='+scope.row.id" v-if="scope.row.goods_name!=''">{{scope.row.goods_name}}</a>
                                    <span v-if="scope.row.goods_name==''">暂无</span>
                                </div>
                                <div class="code_1">
                                    <span v-text="scope.row.goods_no" v-if="scope.row.goods_no!=''"></span>
                                    <span v-if="scope.row.goods_no==''">暂无</span>
                                </div>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                    label="上架时间"
                >
                    <template slot-scope="scope">
                        <span v-if="scope.row.status == 1">{{scope.row.puton_time}}</span>
                        <span v-if="scope.row.status == 0">暂无</span>
                    </template>
                </el-table-column>
                <el-table-column
                    prop="avg_tag"
                    label="平均吊牌价(元)"
                    width="120">
                </el-table-column>

                <el-table-column
                    sortable
                    :default-sort="'ascending'"
                    :sort-orders="['descending','ascending']"
                    width="100"
                    prop="stock">
                    <template slot="header" slot-scope="scope">
                        库存
                        <span class="light_an">
                                <el-tooltip
                                    effect="dark"
                                    placement="top">
                                </el-tooltip>
                            </span>
                    </template>
                </el-table-column>
                <el-table-column
                    label="操作"
                    fixed="right"
                    width="200">
                    <template slot-scope="scope">
                        <el-button
                            type="text"
                            size="mini"
                        >
                            <span style="color:#1890FF" @click="toCreate(scope.row.id)">编辑</span>
                        </el-button>

                        <el-button
                            type="text"
                            size="mini"
                            v-if="scope.row.status == 1"
                        >
                            <span style="color:#1890FF" @click="toShelf(1,scope.row)">下架</span>
                        </el-button>

                        <el-button
                            type="text"
                            size="mini"
                            v-if="scope.row.status == 0"
                        >
                            <span style="color:#1890FF" @click="toShelf(0,scope.row)">上架</span>
                        </el-button>

                        <el-button
                            type="text"
                            size="mini"
                            >
                            <span style="color:red" @click="delGoods(scope.row)">删除</span>
                        </el-button>
                    </template>

                </el-table-column>
            </el-table>

            <el-pagination
                @size-change="handleSizeChange"
                @current-change="handleCurrentChange"
                :current-page="params.p"
                background
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
            return {
                value: 1,
                //分类ids
                brand: '',
                brandList: [],

                cate_ids: '',
                categoryList: [],

                //是否有列表图
                listMap: '',
                listMapOptions:[
                    {value:1,label:'是'},
                    {value:2,label:'否'}
                ],

                goods_no: '',    //输入的商品编码或商品名称

                //分类
                params: {
                    p: 1,
                    limit: 10,

                },
                total: 0,


                tableData: [],
                multipleSelection: []
            }
        },

        methods: {

            async getBrandList() {
                const {data: res} =  await this.$http.post('/getBrandList');
                if(res.status != 1) return this.$message.error(res.msg);
                this.brandList = res.content;

            },

            //获取分类
            async getCategoryList() {
                const {data: res} =  await this.$http.post('/getCategoryList');
                if(res.status != 1) return this.$message.error(res.msg);
                this.categoryList = res.content;

            },

            //查询数据
            searchData() {
                var that=this;
                that.getData()
            },

            //获取商城列列表数据
            async getData() {
                var that=this;
                var form = {};

                form.brand = that.brand;
                form.cate_id  = that.cate_ids;
                form.goods_no = that.goods_no;
                form.list_img = that.listMap;
                form.p = that.params.p;
                form.limit = that.params.limit;

                console.log(form);

                const {data: res} =  await this.$http.post('/getGoodsList',form);
                if(res.status != 1) return this.$message.error(res.msg);
                this.tableData = res.content.list;
                this.total = res.content.total;
                console.log(this.tableData);
            },

            //重置查询数据
            resetData() {
                var that =this;
                that.brand = '';
                that.goods_no = '';
                that.cate_ids = '';
                that.listMap = '';
                that.searchData();
            },

            //上下架商品
            async toShelf(val,coding){
                var form={};
                form.status=val;
                form.goods_no = coding.goods_no;

                const {data: res} =  await this.$http.post('/onSale',form);
                if(res.status != 1) {
                    coding.status = !coding.status;
                    return this.$message.error(res.msg);
                }
                this.getData();
                return this.$message.success(res.msg);

            },

            async delGoods(val) {

                const confirmResult = await this.$confirm('是否删除该商品?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).catch(res => res)

                if(confirmResult != 'confirm') {
                    return this.$message.info('取消删除');
                }
                const {data: res} =  await this.$http.post('/delGoods',{id: val.id});
                if(res.status != 1) return this.$message.error(res.msg);
                this.$message.success(res.msg);
                this.getData();

            },

            //分页
            handleSizeChange(newSize) {
                this.params.limit = newSize
                this.getData()
            },
            handleCurrentChange(newPage) {
                this.params.p = newPage
                this.getData()
            },

            //用户返回被选中的内容;
            handleSelectionChange(val) {
                this.multipleSelection = val;
                console.log( this.multipleSelection);
            },

            //去往编辑页面
            toCreate(val){
                if(val!=''){
                    // console.log(val)
                    // window.location.href="#/edit?id="+val;
                    this.$router.push({
                        path: '/edit',
                        query: {
                            id: val
                        }
                    })
                }else{
                    window.location.href="#/edit";
                }
            },
        },

        mounted() {
            this.getBrandList();
            this.getCategoryList();
            this.getData();
        },

    }
</script>
<style scoped>

    .item{
        display:inline-block;
        margin-left:10px;
    }

    .item span{
        font-size: 12px;
    }

    .select_wrap{
        width:140px;
        float:right;

    }

    .comBtns{
         display: inline-block;
         width:64px;
         height:28px;
         line-height: 28px;
         text-align: center;
         font-size: 12px;
         margin-right:8px;
         cursor: pointer;
         border-radius: 4px;
     }
    .twentyEightWrap .sousuo{
        color:#fff;
        background:linear-gradient(134deg,rgba(211,220,230,1) 0%,rgba(179,192,209,1) 100%);
    }

    .twentyEightWrap .chongzhi{
        color:rgba(0, 0, 0, .65);
        border:1px solid rgba(217, 217, 217, 1);
    }



    .shangpi{
        width:100%;
        height:80px;
    }

    .el-table thead{
        color:rgba(0, 0, 0, 0.65);
        font-size: 12px;
    }
    .el-table{
        color:rgba(0, 0, 0, 0.65);
        font-size: 12px;
        text-align:center;
    }

    .shangpi .left_img{
        display: inline-block;
        width:80px;
        height: 80px;
        overflow: hidden;  /*超出内容不可见*/
        vertical-align: top;
        margin-right: 8px;
    }
    .left_img img{
        width:80px;
    }

    .shangpi .right_content{
        display: inline-flex;
        width:calc(100% - 94px);
        height: 80px;
        vertical-align: top;
        flex-direction: column;
        justify-content: space-around;
        align-items: center;
    }
    .right_content .top_title{
        line-height: 16px;
        width:100%;
        height: 32px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        box-sizing: border-box;
        padding-right:15px;
        margin-bottom: 8px;
    }
     .top_title a{
        width:100%;
        -webkit-box-orient:vertical;
        -webkit-line-clamp:2;
        height:34px;
        overflow:hidden;
        text-overflow:ellipsis;
        text-decoration:none;
    }

    .code_1{
        color: rgba(0, 0, 0, 0.65);
        width:100%;
        height:12px;
        line-height: 12px;
        margin-bottom: 14px;
        font-size: 12px;
    }
    .twentyEightWrap .manyBtn{
        min-width:80px;
        height:28px;
        border-radius:4px;
        border:1px solid rgba(211,220,230,1);
        display: inline-block;
        vertical-align: top;
        magin-left: 20px;
        font-weight: 400;
        color:#FD9026;
        line-height: 28px;
        box-sizing: border-box;
        padding:0 16px;
        text-align: center;
        cursor: pointer;
    }

    .twentyEightWrap .manyBtn2{
        background:linear-gradient(134deg,rgba(211,220,230,1) 0%,rgba(179,192,209,1) 100%);
        color: #fff;
        float: right;
        margin-bottom:10px;
    }



</style>
