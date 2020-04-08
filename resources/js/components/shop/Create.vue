<template>
    <div>
        <!-- 面包屑-->
        <el-breadcrumb separator="/">
            <el-breadcrumb-item :to="{ path: '/index' }">首页</el-breadcrumb-item>
            <el-breadcrumb-item>商城中心</el-breadcrumb-item>
            <el-breadcrumb-item>商城列表</el-breadcrumb-item>
        </el-breadcrumb>



        <el-card>
            <div class="content-inner">
                <!-- 基本信息 -->
                <div>
                    <h4>基本信息</h4>
                    <div class="row-start mt16">
                        <div class="row-start mr16">
                            <p class="form-label"><span class="red">*</span>商品编码：</p>
                            <el-input v-model="goods.goods_no" clearable size="mini" placeholder="请输入商品编码"></el-input>
                        </div>
                        <div class="row-start mr16">
                            <p class="form-label"><span class="red">*</span>商品名称：</p>
                            <el-input v-model="goods.goods_name" clearable size="mini" placeholder="请输入商品名称"></el-input>
                        </div>
                        <div class="row-start mr16">
                            <p class="form-label"><span class="red">*</span>商品分类：</p>
                            <el-cascader
                                v-model="cates"
                                clearable
                                size="mini"
                                :props="{value: 'id', label:'name', children:'list',checkStrictly: true}"
                                :options="cateList"
                                @change="handleChange"
                            >
                            </el-cascader>
                        </div>
                        <div class="row-start mr16">
                            <p class="form-label">所属品牌：</p>
                            <el-select v-model="goods.brand_id" size="mini" clearable>
                                <el-option
                                    v-for="item in brandList"
                                    :key="item.id"
                                    :value="item.id"
                                    :label="item.brand_name">
                                </el-option>
                            </el-select>
                        </div>
                    </div>
                </div>

                <!-- 规格信息·设置 -->
                <div class="mt24">
                    <h4>规格信息/价格 设置</h4>
                    <div class="row-start-top mt16">
                        <p class="form-label">商品规格：</p>
                        <el-cascader
                            v-model="goodsSpec"
                            :options="specList"
                            :props="{value: 'id', label:'name', children:'list',multiple: true}"
                            size="mini"
                            collapse-tags
                            clearable
                            @change="handleChangeSpec"
                        >
                        </el-cascader>
                    </div>
                </div>


                <div class="mt24">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th> <span>商品规格</span> </th>
                            <th> <span>SKU编码</span> </th>
                            <th> <span>吊牌价（元）</span> </th>
                            <th> <span>销售价（元）</span> </th>
                            <th> <span>库存（件）</span> </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr
                            v-for="(item,index) in skuList"
                            :key="index">

                            <td>
                                <span v-text="item.spec_name + item.spec_val_name"></span>
                            </td>

                            <!-- sku编码 -->
                            <td>
                                <div class="input-xs" >
                                    <el-input clearable size="mini" v-model="item.sku_no"  ></el-input>
                                </div>
                            </td>
                            <!-- 吊牌价 -->
                            <td>
                                <div class="input-xs">
                                    <el-input-number  controls-position="right" clearable size="mini" :min='0' :precision="2" v-model="item.tag_price"></el-input-number>
                                </div>
                            </td>
                            <!-- 销售价 -->
                            <td>
                                <div class="input-xs">
                                    <el-input-number  controls-position="right" clearable size="mini" :min='0' :precision="2" v-model="item.sales_price"></el-input-number>
                                </div>
                            </td>
                            <!-- 库存 -->
                            <td>
                                <div class="input-xs">
                                    <el-input-number  controls-position="right" clearable size="mini" :min='0' v-model="item.stock"></el-input-number>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- 商品展示设置 -->
                <div class="mt24">
                    <h4>商品展示设置</h4>
                    <div class="row-start mt16">
                        <p class="form-label mr8"><span class="red">*</span>商品列表图：</p>
                        <el-upload
                            class="avatar-uploader"
                            action="/upload"
                            :show-file-list="false"
                            :on-success="handleAvatarSuccess"
                            :before-upload="beforeAvatarUpload">
                            <img v-if="goods.list_img" :src="goods.list_img" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                    </div>



                    <div class="row-start mt16">
                        <p class="form-label mr8"><span class="red">*</span>商品轮播图：</p>
                        <template v-if="bannerList"  v-for="item in bannerList">
                            <img  :src="item" class="avatar">
                        </template>
                        <el-upload
                            :limit="3"
                            action="/upload"
                            list-type="picture-card"
                            :on-success="handleAvatarSuccess1"
                         >
                            <i class="el-icon-plus"></i>
                        </el-upload>

                    </div>
                    

                    <!-- 分享描述 -->
                    <div class="row-start input-small mt24">
                        <p class="form-label mr8"><span style="color:transparent;">*</span>商品内容：</p>
                        <el-input
                            type="textarea"
                            :rows="4"
                            placeholder="请输入商品内容"
                            v-model="goods.content">
                        </el-input>
                    </div>

                    <!-- 上架信息· -->
                    <div class="mt24">
                        <h4>上架信息</h4>

                        <div class="row-start mt16">
                            <p class="form-label mr8 w100"><span class="red">*</span>是否上下架：</p>
                            <el-radio-group v-model="goods.status">

                                <el-radio :label="1" >是</el-radio>
                                <el-radio :label="0">否</el-radio>
                            </el-radio-group>
                        </div>
                        <div class="row-start mt16" v-if="goods.status == 1">
                            <p class="form-label mr8 w100"><span class="red">*</span>是否首页展示：</p>
                            <el-radio-group v-model="goods.isShow">

                                <el-radio :label="1" >是</el-radio>
                                <el-radio :label="0">否</el-radio>
                            </el-radio-group>
                        </div>
                    </div>
                    <!-- 按钮 -->
                    <div class="row-start mt24" style="padding-left: 80px;margin-top: 32px;">
                        <el-button size="small" type="primary" @click="confirm()" >保 存</el-button>
                        <el-button size="small" type="plain" @click="goBack" >返 回</el-button>
                    </div>
                </div>
            </div>
        </el-card>

    </div>





</template>

<script>
    export default {
        data() {
            return {
                dialogImageUrl: '',
                dialogVisible: false,


                brandList:[], //品牌列表，
                cateList:[], //分类列表
                specList:[],
                specValList: [],
                skuList:[],

                bannerList: [],

                isShowSpecDialog: false,

                goodsSpec:[],

                goods:{
                    id: '',
                    goods_no: '',
                    goods_name:'',
                    brand_id: '',
                    cate_id: '',
                    list_img:'',
                    content: '',
                    status: '',
                    isShow: '',
                },
                cates: [],

                specDialog:[[]],
                specIdx: '',
            }
        },

        computed: {

        },

        methods: {

            handleAvatarSuccess1(res, file) {
                this.bannerList.push(res)
                console.log(this.bannerList)
            },

            handleAvatarSuccess(res, file) {
                console.log(res)
                // this.goods.list_img = URL.createObjectURL(file.raw);
                this.goods.list_img = res;

            },
            beforeAvatarUpload(file) {
                const isJPG = file.type === 'image/jpeg';
                const isLt2M = file.size / 1024 / 1024 < 2;

                if (!isJPG) {
                    this.$message.error('上传头像图片只能是 JPG 格式!');
                }
                if (!isLt2M) {
                    this.$message.error('上传头像图片大小不能超过 2MB!');
                }
                return isJPG && isLt2M;
            },


            async getCategoryList() {
                var that =this;
                const {data: res} =  await this.$http.post('/getCategoryList', {type: 3});
                if(res.status != 1) return this.$message.error(res.msg);
                that.cateList = res.content;

            },

            async getSpecList () {
                var that = this;
                const {data: res} = await this.$http.post('/getSpecList');
                if (res.status != 1) return this.$message.error(res.msg);
                that.specList = res.content;
            },

            async getBrandList() {
                const {data: res} =  await this.$http.post('/getBrandList');
                if(res.status != 1) return this.$message.error(res.msg);
                this.brandList = res.content;

            },

            async getGoodsSpec() {
                const {data: res} =  await this.$http.post('/getGoodsSpec', {goodsSpec:this.goodsSpec});
                if(res.status != 1) return this.$message.error(res.msg);
                this.skuList = res.content;
                console.log(this.skuList)
            },


            handleChange() {
                console.log(this.cates);
                if (this.cates.length != 3) {
                    this.cates = []
                }
            },

            handleChangeSpec() {
                console.log(this.goodsSpec)
                this.getGoodsSpec();

            },

            //获取商品数据
            async getGoodsData() {
                var that=this;
                var form={}
                form.id=this.$route.query.id
                if (form.id === undefined) return;
                const {data: res} =  await this.$http.post('/editView', form);
                var good = res.content.goods;
                console.log(good)
                that.goods = {
                    id: good.id,
                    goods_no: good.goods_no,
                    goods_name: good.goods_name,
                    brand_id: good.brand_id,
                    content: good.content,
                    status: good.status,
                    list_img: good.list_img,
                    isShow: good.isShow,
                }
                that.bannerList = good.img_list;
                console.log(that.bannerList)
                that.cates = good.cate_id;
                that.skuList = good.sku_list;
            },

            pickGoodsImg() {},

            //返回
            goBack () {
                location.href="#/list"
            },

            //保存
            async confirm() {
                var that =this;
                var goods = that.goods;
                goods.id = this.$route.query.id;
                goods.skuList = that.skuList;
                goods.bannerList = that.bannerList;
                const {data: res} =  await this.$http.post('/saveGoods', goods);
                if(res.status != 1) return this.$message.error(res.msg);
                this.cateList = res.content;
                that.$message({
                    type:'success',
                    message: '保存成功！',
                    duration: 1500,
                    onClose: function(){
                        location.href="/#/list";
                    }
                })
            },


        },
        watch: {
            cates (val) {
                var that = this;
                var id = '';
                if(val && val.length>0){
                    id = val[val.length-1];
                }
                that.$set(that.goods, 'cate_id', id);
                console.log(that.goods);
            },
            goods_no(val) {
                let that = this;
                let reg = /[^\u4e00-\u9fa5]+$/;
                if (val!='') {
                    if (!reg.test(that.goods_no)) {
                        that.goods_no = '';
                        that.$message.error('商品编码只能为中文')
                    }
                }
            }
        },
        mounted() {
            this.getCategoryList();
            this.getBrandList();
            this.getSpecList();
            this.getGoodsData();
        },
    }
</script>

<style scoped>
    .row-start{
        display: flex;
        flex-flow: row nowrap;
        justify-content: flex-start;
        align-items: center;
    }
    .form-label{
        display: inline-block;
        white-space: nowrap;
        color: rgba(0, 0, 0, .65);
        text-align: right;
        font-size: 12px;
    }
    .mt16{
        margin-top: 16px;
    }
    .mr16{
        margin-right: 16px;
    }
    .mt24{
        margin-top: 24px;
    }
    .mr8{
        margin-right: 8px;
    }
    .red{
        color: red;
    }
    .input-small{
        width: 700px;
    }

    .avatar-uploader {
        border: 1px dashed #d9d9d9;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .avatar-uploader .el-upload:hover {
        border-color: #409EFF;
    }
    .avatar-uploader-icon {
        font-size: 28px;
        color: #8c939d;
        width: 178px;
        height: 178px;
        line-height: 178px;
        text-align: center;
    }
    .avatar {
        width: 178px;
        height: 178px;
        display: block;
    }
    .table {
        width: 100%;
        max-width: 100%;
        margin-bottom: 1rem;
        background-color: transparent;
        font-size: 1px;
    }

    .table th,
    .table td {
        padding: 0.9375rem;
        vertical-align: top;
        border-top: 1px solid #ebedf2;
    }

    .table thead th {
        vertical-align: bottom;
        border-bottom: 2px solid #ebedf2;
    }

    .table tbody + tbody {
        border-top: 2px solid #ebedf2;
    }

    .table .table {
        background-color: #fff;
    }
    .table-bordered {
        border: 1px solid #ebedf2;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #ebedf2;
        text-align: center;

    }

    .table-bordered thead th,
    .table-bordered thead td {
        border-bottom-width: 1px;
    }
    table {
        border-collapse: collapse;
    }



</style>
