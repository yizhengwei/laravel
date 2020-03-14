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
                <el-tab-pane label="分类样式" name="first"></el-tab-pane>

                <el-tab-pane label="商品分类" name="second">
                    <el-row>
                        <el-col align="right">
                            <el-button size="mini" align="right" type="primary" @click="showAddCategory('', 1)">新增分类</el-button>
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
                                        <span style="color:#1890FF" @click="showAddCategory(scope.row, '2')">新增二级分类</span>
                                    </el-button>
                                    <el-button
                                        v-if="scope.row.level==2"
                                        type="text"
                                        size="mini">
                                        <span style="color:#1890FF"  @click="showAddCategory(scope.row, '3')">新增三级分类</span>
                                    </el-button>
                                    <el-button
                                        type="text"
                                        size="mini">
                                        <span style="color:#1890FF"  @click="editCategory(scope.row.id)">编辑</span>
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
                            <el-button size="mini" align="right" type="primary" @click="showAddCategory('', 1)">新增规格</el-button>
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
                                {{scope.row}}
                                <div>
                                    <el-button
                                        v-if="scope.row.level==1"
                                        type="text"
                                        size="mini">
                                        <span style="color:#1890FF" @click="showAddCategory(scope.row, 'N')">新增规格值</span>
                                    </el-button>
                                    <el-button
                                        type="text"
                                        size="mini"
                                        v-if="!(scope.row.level==1 && scope.row.status==1)"
                                    >
                                        <span style="color:#1890FF" @click="showAddCategory(scope.row, 'Y')">编辑</span>
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
            <el-form :model="form" :rules="addCateFromRules" ref="addCateFromRef" label-width="80px">
                <el-form-item label="分类名" prop="name" size="mini">
                    <el-input v-model="form.name" ></el-input>
                </el-form-item>
            </el-form>

            <div slot="footer" class="dialog-footer">
                <el-button @click="showAddCate = false">取 消</el-button>
                <el-button type="primary" @click="add()">确定</el-button>
            </div>

        </el-dialog>

        <!--弹出框-->
        <el-dialog title="编辑分类"
                   :visible.sync="editCate"
                   :close-on-click-modal="false"
                   width="30%"
                   @close="editCateDiglogClosed"
        >
            <el-form :model="editFrom" :rules="editCateFromRules" ref="editCateFromRef" label-width="80px">
                <el-form-item label="分类名" prop="name" size="mini">
                    <el-input v-model="editFrom.name" ></el-input>
                </el-form-item>
            </el-form>

            <div slot="footer" class="dialog-footer">
                <el-button @click="editCate = false">取 消</el-button>
                <el-button type="primary" @click="edit()">确定</el-button>
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

                //规格列表
                specList: [],

                showAddCate: false,
                editCate: false,

                form: {
                    name: '',
                },

                editFrom: {},

                addCateFromRules: {
                    name: [
                        { required: true, message: '请输入分类名', trigger: 'blur'},
                        { min: 1, max: 10, message: '分类名称不能为空或长度不能多于10个字符', trigger: 'blur' }
                    ],
                },
                editCateFromRules: {
                    name: [
                        { required: true, message: '请输入分类名', trigger: 'blur'},
                        { min: 1, max: 10, message: '分类名称不能为空或长度不能多于10个字符', trigger: 'blur' }
                    ],
                },

            };
        },
        methods: {

            //获取分类列表
            async getCategoryList() {
                const {data: res} =  await this.$http.post('/getCategoryList');
                if(res.status != 1) return this.$message.error(res.msg);
                this.categoryList = res.content;

            },

            addCateDiglogClosed() {
                this.$refs.addCateFromRef.resetFields();
                console.log('aaa');
            },

            editCateDiglogClosed() {
                this.$refs.editCateFromRef.resetFields();
            },

            //显示新增分类
            showAddCategory(row,val){
                let that = this;
                if(val == 1) {
                    that.pid = 0;
                } else {
                    that.pid = row.id;
                }
                this.showAddCate = true;
            },

            //新增分类
            async add() {
                let that = this;
                this.showAddCate = false;
                let pid = that.pid;
                const {data: res} = await this.$http.post('/saveCategory', {pid: pid, name: this.form.name});
                if(res.status != 1) return this.$message.error(res.msg);
                this.$message.success(res.msg);
                this.getCategoryList();

            },
            //编辑
            edit() {
                this.$refs.editCateFromRef.validate(async valid=> {
                    if (!valid) return
                    let that = this;
                    let pid = that.pid;
                    console.log(pid)
                    const {data: res} = await this.$http.post('/saveCategory',{id: this.editFrom.id,pid: this.editFrom.pid, name: this.editFrom.name});
                    if(res.status != 1) return this.$message.error(res.msg);
                    this.$message.success(res.msg);
                    this.editCate = false;
                    this.getCategoryList();
                })
            },

            //编辑分类
            async editCategory(id) {
                console.log(id);
                const {data: res} = await this.$http.get('/getCateInfo?id='+id);
                if (res.status != 1) return this.$message.error(res.msg);
                this.editFrom = res.content;
                this.editCate = true;
            },

            //移动分类
            async moveCate(id, type) {
                const {data: res} = await this.$http.post('/sortCategory?id='+id, {type: type});
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
                const {data: res} = await this.$http.post('/delCategory?id='+id);
                if(res.status != 1) return this.$message.error(res.msg);
                this.$message.success(res.msg);
                this.getCategoryList();
            },

            //获取规格列表
            async getSpecList() {
                const {data: res} =  await this.$http.post('/getSpecList');
                if(res.status != 1) return this.$message.error(res.msg);
                this.specList = res.content;
            },

        },
        mounted() {
            this.getCategoryList();
            this.getSpecList();

        }
    };
</script>

<style scoped>

</style>
