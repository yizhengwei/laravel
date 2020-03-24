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
                                :options="cateList">
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
                    <h6>规格信息/价格 设置</h6>
                    <div class="row-start-top mt16">
                        <p class="form-label">商品规格：</p>
                        <div>
                            <ul>
                                <li
                                    class="spec-row"
                                    v-for="(item,i) in goodsSpec"
                                    style="width:700px;"
                                    :key="item.id">
                                    {{goodsSpec}}
                                    <div class="row-between">
                                        <div>
                                            <!-- 选择规格 -->
                                            <el-select
                                                class="mr16"
                                                v-model="item.spec_id"
                                                size="small"
                                                clearable
                                                @change="handleSpec(i)" >
                                                <el-option
                                                    v-for="item2 in specList"
                                                    :key="item2.id"
                                                    :value="item2.id"
                                                    :label="item2.name">
                                                </el-option>
                                            </el-select>
                                            <!-- 选择规格值 -->
                                            <el-button type="plain" size="small" @click="showSpecDialog(i)" >选择规格值</el-button>
                                        </div>
<!--                                        <div>-->
<!--                                            <el-button-->
<!--                                                v-show="goodsSpec.length>1"-->
<!--                                                type="text"-->
<!--                                                size="mini"-->
<!--                                                @click="deleteSpec(i)">-->
<!--                                                <span class="red">删除</span>-->
<!--                                            </el-button>-->
<!--                                        </div>-->
                                    </div>
<!--                                    <div class="row-start-wrap" v-if="specDialog[i].length>0">-->
<!--                                        <div-->
<!--                                            class="spec-val-cell mr16 mt16"-->
<!--                                            v-for="(specval, j) in specDialog[i]"-->
<!--                                            :key="specval.id"-->
<!--                                            v-show="specval.is">-->
<!--                                            <span v-text="specval.name"></span>-->
<!--                                            <span class="del-spec" @click="pickSpecVal(j, i)"></span>-->
<!--                                        </div>-->
<!--                                    </div>-->
                                </li>
                            </ul>
<!--                            <el-button  class="mt16" type="primary" size="small"  @click="addSpecItem">-->
<!--                                <span>添加规格</span>-->
<!--                                <span v-text="goodsSpec.length"></span>-->
<!--                                <span>/3</span>-->
<!--                            </el-button>-->
                        </div>
                    </div>
                </div>

                <!-- sku列表 -->
                <div class="mt24">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th
                                v-for="item in goodsSpec"
                                :key="item.spec_id" >
                                <span v-text="item.spec_name"></span>
                            </th>
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
                            <!-- sku -->
                            <td
                                v-for="(item2,j) in item.spec_list"
                                :key="item2.spec_val_id"
                                v-if="handleRowShow(index, j)"
                                :rowspan="mapRowSpan(index,j)">
                                <span v-text="item2.spec_val_name"></span>
                            </td>

                            <!-- sku编码 -->
                            <td>
                                <div class="input-xs">
                                    <el-input clearable size="mini" v-model="item.sku_no" @blur="checkSkuNo(item.sku_no,index)" :disabled="canEdit"></el-input>
                                </div>
                            </td>
                            <!-- 吊牌价 -->
                            <td>
                                <div class="input-xs">
                                    <el-input-number  controls-position="right" clearable size="mini" :min='0' :precision="2" v-model="item.tag_price" :disabled="canEdit"></el-input-number>
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
                                    <el-input-number  controls-position="right" clearable size="mini" :min='0' v-model="item.stock" :disabled="platforms=='pos'"></el-input-number>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- 商品展示设置 -->
                <div class="mt24">
                    <h4>商品展示设置</h4>
<!--                    <div class="row-start mt16">-->
<!--                        <p class="form-label mr8"><span class="red">*</span>商品列表图：</p>-->
<!--                        <div class="spec-img-upload-middle" @click="pickGoodsImg">-->
<!--                            <span>+</span>-->
<!--                            <p>建议图片宽度350</p>-->
<!--                            <img v-if="goods.list_img" :src="'/thumb/350x350/'+goods.list_img" >-->
<!--                        </div>-->
<!--                    </div>-->

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


                brandList:[], //品牌列表
                cateList:[], //分类列表
                specList:[],
                skuList:[],


                isShowSpecDialog: false,

                goodsSpec:[
                    {
                        spec_id: '',
                        spec_name: '',
                    }
                ],

                goods:{
                    id: '',
                    goods_no: '',
                    goods_name:'',
                    brand_id: '',
                    cate_id: '',
                    // list_img:'',
                    // list_img_url: '',
                    content: '',
                    status: '',
                },
                cates: [],
            }
        },

        computed: {

        },

        methods: {
            async getCategoryList() {
                const {data: res} =  await this.$http.post('/getCategoryList');
                if(res.status != 1) return this.$message.error(res.msg);
                this.cateList = res.content;


            },

            async getSpecList () {
                const {data: res} = await this.$http.post('/getSpecList');
                if (res.status != 1) return this.$message.error(res.msg);
                this.specList = res.content;
                console.log(this.specList);
            },

            async getBrandList() {
                const {data: res} =  await this.$http.post('/getBrandList');
                if(res.status != 1) return this.$message.error(res.msg);
                this.brandList = res.content;

            },

            //获取商品数据
            async getGoodsData() {
                var that=this;
                var form={}
                form.id=this.$route.query.id
                const {data: res} =  await this.$http.post('/editView', form);
                var good = res.content.goods;
                console.log(good)
                that.goods = {
                    id: good.id,
                    goods_no: good.goods_no,
                    goods_name: good.goods_no,
                    brand_id: good.brand_id,
                    content: good.content,
                    status: good.status,
                }
                that.cates = good.cate_id;
            },

            pickGoodsImg() {},

            async confirm() {
                var that =this;
                var goods = that.goods;
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

            goBack() {},

            handleSpec (i) {
                var that = this;
                var specList = that.specList;
                var goodsSpec = that.goodsSpec;
                // // 商品规格
                var obj = goodsSpec[i];
                for(var k=0 ; k<goodsSpec.length; k++){
                    if(i!=k && obj.spec_id===goodsSpec[k]['spec_id']){
                        that.$message.error('该规格已被选择！');
                        return;
                    }
                }
                console.log(specList)

                specList.forEach(function(item){
                    if(item.id === obj.spec_id){
                        obj.spec_name = item.name;

                        // item.spec_val.forEach(function(val){
                        //     val.is = false;
                        // })
                        // that.$set(that.specDialog, i, item.spec_val);
                    }
                });
                that.$set(that.goodsSpec, i, obj);
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

</style>
