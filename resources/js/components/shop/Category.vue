<template slot-scope="scope">

    <div>
        <!-- 面包屑-->
        <el-breadcrumb separator="/">
            <el-breadcrumb-item :to="{ path: '/index' }">首页</el-breadcrumb-item>
            <el-breadcrumb-item>商城中心</el-breadcrumb-item>
            <el-breadcrumb-item>商品分类</el-breadcrumb-item>
        </el-breadcrumb>


        <!--卡片-->
        <el-card>
            <!--按钮-->
            <el-tabs v-model="tab" >
                <el-tab-pane label="商品分类" name="second">
                    <el-row>
                        <el-col align="right">
                            <el-button size="mini" align="right" type="primary" @click="showAddCategory(newCate, 'N',1)">新增分类</el-button>
                        </el-col>
                    </el-row>

                    <el-table
                        :data="categoryList"
                        style="width: 100%"
                        row-key="id"
                        :tree-props="{children: 'list'}"
                        :header-row-style="{color:'#000000', fontSize: '12px', background:'#f3f3f3'}">
                        <el-table-column label="分类" align="left">
                            <template slot-scope="scope">
                                <img class="cell-img" :style="{marginLeft: scope.row.list ? '0': '23px' }" :src="scope.row.list_img" v-if="scope.row.level==2&&scope.row.list_img!=''">
                                <span>{{scope.row.name}}</span>
                            </template>
                        </el-table-column>

                        <el-table-column label="操作" align="right">
                            <template slot-scope="scope">
                                <div>
                                    <el-button
                                        v-if="scope.row.level==1"
                                        type="text"
                                        size="mini">
                                        <span style="color:#1890FF" @click="showAddCategory(scope.row, 'N',2)">新增二级分类</span>
                                    </el-button>
                                    <el-button
                                        v-if="scope.row.level==2"
                                        type="text"
                                        size="mini">
                                        <span style="color:#1890FF"  @click="showAddCategory(scope.row, 'N',3)">新增三级分类</span>
                                    </el-button>
                                    <el-button
                                        type="text"
                                        size="mini">
                                        <span style="color:#1890FF"  @click="showAddCategory(scope.row, 'Y',4)">编辑</span>
                                    </el-button>
                                    <el-button
                                        type="text"
                                        size="mini"
                                        @click="moveCate(scope.row.id, 'up')">
                                        <span style="color:#1890FF"  >上移</span>
                                    </el-button>
                                    <el-button
                                        type="text"
                                        size="mini"
                                        @click="moveCate(scope.row.id, 'down')">
                                        <span style="color:#1890FF">下移</span>
                                    </el-button>
                                    <el-button
                                        type="text"
                                        size="mini"
                                        @click="delCate(scope.row.id)">
                                        <span style="color:red">删除</span>
                                    </el-button>
                                </div>
                            </template>
                        </el-table-column>
                    </el-table>
                </el-tab-pane>

                <el-tab-pane label="商品规格" name="third">
                    <el-row>
                        <el-col align="right">
                            <el-button size="mini" align="right" type="primary" @click="showAddSpecs('', 1)">新增规格</el-button>
                        </el-col>
                    </el-row>

                    <el-table
                        :data="specList"
                        style="width: 100%"
                        row-key="id"
                        :tree-props="{children: 'list'}"
                        :header-row-style="{color:'#000000', fontSize: '12px', background:'#f3f3f3'}">
                        <el-table-column label="规格" align="left">
                            <template slot-scope="scope">
                                <span>{{scope.row.name}}</span>
                            </template>
                        </el-table-column>

                        <el-table-column label="操作" align="right">
                            <template slot-scope="scope">
                                <div>
                                    <el-button
                                        v-if="scope.row.level==1"
                                        type="text"
                                        size="mini">
                                        <span style="color:#1890FF" @click="showAddSpecs(scope.row, 'N')">新增规格值</span>
                                    </el-button>
                                    <el-button
                                        type="text"
                                        size="mini"
                                    >
                                        <span style="color:#1890FF" @click="showAddSpecs(scope.row, 'Y')">编辑</span>
                                    </el-button>
                                    <el-button
                                        type="text"
                                        size="mini"
                                        v-if="scope.row.level!=1"
                                        @click="moveSpecs(scope.row.id, 'up')">
                                        <span style="color:#1890FF"  >上移</span>
                                    </el-button>
                                    <el-button
                                        type="text"
                                        size="mini"
                                        v-if="scope.row.level!=1"
                                        @click="moveSpecs(scope.row.id, 'down')">
                                        <span style="color:#1890FF">下移</span>
                                    </el-button>
                                    <el-button
                                        type="text"
                                        size="mini"
                                        v-if="scope.row.status!=1">
                                        <span style="color:red" @click="deleteSpec(scope.row)">删除</span>
                                    </el-button>
                                </div>
                            </template>
                        </el-table-column>

                    </el-table>

                </el-tab-pane>


            </el-tabs>
        </el-card>

        <!--弹出框-->
        <el-dialog title="商品分类"
                   :visible.sync="showAddCate"
                   :close-on-click-modal="false"
                   width="30%"
                   @close="addCateDiglogClosed"
        >
            <el-form :model="newCate" :rules="addCateFromRules" ref="addCateFromRef" label-width="80px">
                <el-form-item label="分类名" prop="name" size="mini">
                    <el-input v-model="newCate.name" ></el-input>
                </el-form-item>
                <el-form-item label="父级分类"  size="mini">
                        <el-cascader
                            clearable
                            change-on-select
                            v-model="newCate.pid"
                            :options="parentCateList"
                            :props="{value: 'id', label:'name', children:'list',checkStrictly: true}"
                            @change="handleChange"
                        >

                        </el-cascader>


                </el-form-item>
                <el-form-item label="分类图标" size="mini" v-if="newCate.pid!=0">

                    <el-upload
                        class="avatar-uploader"
                        action="/upload"
                        :show-file-list="false"
                        :on-success="handleAvatarSuccess"
                        :before-upload="beforeAvatarUpload">
                        <img v-if="newCate.list_img" :src="newCate.list_img" class="avatar">
                        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                </el-form-item>
            </el-form>



            <div slot="footer" class="dialog-footer">
                <el-button @click="showAddCate = false">取 消</el-button>
                <el-button type="primary" @click="add()">确定</el-button>
            </div>

        </el-dialog>

        <!--弹出框-->
        <el-dialog title="商品规格"
                   :visible.sync="showAddSpec"
                   :close-on-click-modal="false"
                   width="30%"
                   @close="addSpecDiglogClosed"
        >
            <el-form :model="newSpec" :rules="addSpecFromRules" ref="addSpecFromRef" label-width="80px">
                <el-form-item label="规格值" prop="name" size="mini">
                    <el-input v-model="newSpec.name" ></el-input>
                </el-form-item>
            </el-form>

            <div slot="footer" class="dialog-footer">
                <el-button @click="showAddSpec = false">取 消</el-button>
                <el-button type="primary" @click="addSpec()">确定</el-button>
            </div>

        </el-dialog>

    </div>

</template>

<script>
    export default {
        data() {
            return {
                //页头;
                tab: 'second',

                //分类列表
                categoryList: [],
                parentCateList: [],

                //弹出框绑定数据
                newCate: {
                    id: '',
                    pid:'',
                    name:'',
                    list_img: '',
                },
                showAddCate: false,
                addCateFromRules: {
                    name: [
                        { required: true, message: '请输入分类名', trigger: 'blur'},
                        { min: 1, max: 10, message: '分类名称不能为空或长度不能多于10个字符', trigger: 'blur' }
                    ],
                },


                //规格列表
                specList: [],
                newSpec: {
                    id: '',
                    pid: '',
                    name: '',

                },

                showAddSpec: false,
                addSpecFromRules: {
                    name: [
                        { required: true, message: '请输入分类名', trigger: 'blur'},
                        { min: 1, max: 10, message: '规格名称不能为空或长度不能多于10个字符', trigger: 'blur' }
                    ],
                }
            };
        },
        methods: {
            handleAvatarSuccess(res, file) {
                console.log(res)
                // this.goods.list_img = URL.createObjectURL(file.raw);
                this.newCate.list_img = res;

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

            //获取分类列表
            async getCategoryList() {
                var that = this;
                const {data: res} =  await this.$http.post('/getCategoryList');
                if(res.status != 1) return this.$message.error(res.msg);
                var list = res.content;
                list.forEach(function (item, i) {
                    item.level=1;
                    item.index= i;
                    var sub2 = item.list;
                    if(sub2){
                        sub2.forEach(function(item2, j){
                            item2.level = 2;
                            item2.index = i+'-'+j;
                            var sub3 = item2.list;
                            if(sub3){
                                sub3.forEach(function(item3, k){
                                    item3.level = 3;
                                    item3.index = i+'-'+j+'-'+k;
                                })
                            }
                        })
                    }
                })
                that.categoryList = list;
                console.log(that.categoryList);
            },

            async getParentCateList() {
                const {data: res} =  await this.$http.post('/getCategoryList', {type: 2});
                if(res.status != 1) return this.$message.error(res.msg);
                this.parentCateList = res.content;
                console.log(this.parentCateList);
            },


            //重置表单
            addCateDiglogClosed() {
                this.$refs.addCateFromRef.resetFields();
            },

            showAddCategory(row, isCateEdit,val) {
                let that = this;
                console.log(row)
                this.showAddCate = true;
                let addLevel = row.level === undefined ? 'a' : row.level;
                console.log(addLevel)
                if(addLevel == 'a'){
                    this.newCate.pid = 0;
                }else{
                    this.newCate.pid = row.id;
                }
                if(isCateEdit == 'Y'){
                    this.newCate =JSON.parse(JSON.stringify(row)) ;
                }
            },

            //新增分类
            async add() {
                const {data: res} = await this.$http.post('/saveCategory', this.newCate);
                if(res.status != 1) return this.$message.error(res.msg);
                this.$message.success(res.msg);
                this.showAddCate = false;
                this.getCategoryList();
            },

            //移动分类
            async moveCate(id, type) {
                const {data: res} = await this.$http.post('/sortCategory?', {id: id,type: type});
                if(res.status != 1) return this.$message.error(res.msg);
                this.$message.success(res.msg);
                this.getCategoryList();
            },

            //删除分类
            async delCate(id) {
                const confirmResult = await this.$confirm('是否删除该分类?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).catch(res => res)

                if(confirmResult != 'confirm') {
                    return this.$message.info('取消删除');
                }
                const {data: res} = await this.$http.post('/delCategory?',{id: id});
                if(res.status != 1) return this.$message.error(res.msg);
                this.$message.success(res.msg);
                this.getCategoryList();
            },


            async getSpecList () {
                var that = this;
                const {data: res} = await this.$http.post('/getSpecList');
                if (res.status != 1) return this.$message.error(res.msg);
                var list = res.content;
                list.forEach(function(item, i) {
                    item.level=1;
                    item.index= i;
                    var specVal = item.list || [];
                    specVal.forEach( function( item2, j ){
                        item2.level = 2;
                        item2.index = i+'-'+j;
                    })
                })
                that.specList = list;
            },

            //重置表单
            addSpecDiglogClosed() {
                this.$refs.addSpecFromRef.resetFields();
            },


            showAddSpecs(row, isSpecEdit) {
                let that = this;
                console.log(row.name)
                this.showAddSpec = true;
                let addLevel = row.level === undefined ? 'a' : row.level;
                console.log(addLevel)
                if(addLevel == 'a'){
                    this.newSpec.pid = 0;
                } else {
                    this.newSpec.pid = row.id;
                }
                if(isSpecEdit == 'Y'){
                    this.newSpec =JSON.parse(JSON.stringify(row)) ;
                }
            },

            //新增分类
            async addSpec() {
                const {data: res} = await this.$http.post('/saveSpec', this.newSpec);
                if(res.status != 1) return this.$message.error(res.msg);
                this.$message.success(res.msg);
                this.showAddSpec = false;
                this.getSpecList();
            },

            //删除分类
            async deleteSpec(id) {
                const confirmResult = await this.$confirm('是否删除该规格?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).catch(res => res)

                if(confirmResult != 'confirm') {
                    return this.$message.info('取消删除');
                }
                const {data: res} = await this.$http.post('/delSpec?',{id: id});
                if(res.status != 1) return this.$message.error(res.msg);
                this.$message.success(res.msg);
                this.getSpecList();
            },

            async moveSpecs(id, type) {
                const {data: res} = await this.$http.post('/sortSpec?', {id: id,type: type});
                if(res.status != 1) return this.$message.error(res.msg);
                this.$message.success(res.msg);
                this.getSpecList();
            },

            handleChange() {
                if(this.newCate.pid.length > 0) {
                    this.newCate.pid = this.newCate.pid[this.newCate.pid.length-1];
                }

                if(this.newCate.pid.length == 0) {
                    this.newCate.pid = 0
                }
                console.log(this.newCate.pid );
            }

        },
        mounted() {
            this.getCategoryList();
            this.getSpecList();
            this.getParentCateList();
        }
    };
</script>

<style scoped>
    .el-cascader{
        width: 100%
    }

    .row-start{
        display: flex;
        flex-flow: row nowrap;
        justify-content: flex-start;
        align-items: center;
    }

    img{
        width: 80px;
        height: 80px;
    }

    .cell-img{
        width: 30px;
        height: 30px;
        margin-right: 10px;
    }
</style>



