<template>
    <div>
        <!-- 面包屑-->
        <el-breadcrumb separator="/">
            <el-breadcrumb-item :to="{ path: '/index' }">首页</el-breadcrumb-item>
            <el-breadcrumb-item>用户管理</el-breadcrumb-item>
            <el-breadcrumb-item>用户列表</el-breadcrumb-item>
        </el-breadcrumb>

        <el-card>
            <el-row :gutter="20">
                <el-col :span="6">
                    <el-input placeholder="请输入内容" v-model="params.q" clearable @clear="getAdminList">
                        <el-button slot="append" icon="el-icon-search" @click="getAdminList"></el-button>
                    </el-input>
                </el-col>

                <el-col :span="4">
                    <el-button type="primary" @click="addAdmin = true">添加用户</el-button>
                </el-col>

            </el-row>

            <el-table
                :data="adminList"
                border
                stripe
                style="width: 100%">
                <el-table-column prop="id" label="#" width="180"></el-table-column>
                <el-table-column prop="username" label="姓名"  width="180"></el-table-column>
                <el-table-column prop="mobile" label="手机号码"  width="180"></el-table-column>
                <el-table-column prop="email" label="邮箱"  width="180"></el-table-column>

                <el-table-column prop="status" label="状态" >
                    <template v-slot="scope">
                        <el-switch v-model="scope.row.status" @change="changeStatus(scope.row)">
                        </el-switch>
                    </template>
                </el-table-column>
                <el-table-column label="操作" v-slot="scope">
                    <template>
                        <el-button size="mini" type="primary" icon="el-icon-edit" @click="showAdminInfo(scope.row.id)"></el-button>
                        <el-button size="mini" type="danger" icon="el-icon-delete" @click="delAdmin(scope.row.id)"></el-button>
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
<!--        添加用户-->
        <el-dialog
            title="添加用户"
            :visible.sync="addAdmin"
            width="30%"
            show-close
            @close="addDiglogClosed"
           >
            <el-form :model="addFrom" :rules="addFromRules" ref="addFromRef" label-width="90px">
                <el-form-item label="用户名" prop="username">
                    <el-input v-model="addFrom.username"></el-input>
                </el-form-item>

                <el-form-item label="密码" prop="password">
                    <el-input v-model="addFrom.password"></el-input>
                </el-form-item>

                <el-form-item label="邮箱" prop="email">
                    <el-input v-model="addFrom.email"></el-input>
                </el-form-item>

                <el-form-item label="手机号码" prop="mobile">
                    <el-input v-model="addFrom.mobile"></el-input>
                </el-form-item>
            </el-form>

            <span slot="footer" class="dialog-footer">
                <el-button @click="addAdmin = false">取 消</el-button>
                <el-button type="primary" @click="add()">确 定</el-button>
            </span>
        </el-dialog>

<!--        编辑用户-->
        <el-dialog
            title="编辑用户"
            :visible.sync="editAdmin"
            width="30%"
            show-close
        >
            <el-form :model="editFrom" :rules="editFromRules" ref="editFromRef" label-width="90px" v-slot="scope">
                <el-form-item label="用户名" prop="username">
                    <el-input v-model="editFrom.username" disabled></el-input>
                </el-form-item>


                <el-form-item label="邮箱" prop="email">
                    <el-input v-model="editFrom.email"></el-input>
                </el-form-item>

                <el-form-item label="手机号码" prop="mobile">
                    <el-input v-model="editFrom.mobile"></el-input>
                </el-form-item>
            </el-form>

            <span slot="footer" class="dialog-footer">
                <el-button @click="editAdmin = false">取 消</el-button>
                <el-button type="primary" @click="edit()">确 定</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
    export default {
        data() {
            var checkEmail = (rule, value, callback) => {
                const regEmail = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/;
                if (regEmail.test(value)) {
                    return callback();
                }
                callback(new Error('邮箱格式不正确'));
            };
            var checkMobile = (rule, value, callback) => {
                const regMobile = /^1[34578]\d{9}$/;
                if (regMobile.test(value)) {
                    return callback();
                }
                callback(new Error('手机格式不正确'));
            };

            return {
                params: {
                    q: '',
                    p: 1,
                    limit: 10,

                },
                adminList: [],
                total: 0,
                addAdmin: false,
                editAdmin: false,
                editFrom: {},
                //双向绑定input输入框
                addFrom: {
                    username: '',
                    password: '',
                    email: '',
                    mobile: '',
                },
                //验证规则
                addFromRules: {
                    username: [
                        { required: true, message: '请输入用户名', trigger: 'blur'},
                        { min: 5, max: 10, message: '长度在 5 到 10 个字符', trigger: 'blur' }
                    ],
                    password: [
                        { required: true, message: '请输入密码', trigger: 'blur' },
                        { min: 6, max: 15, message: '长度在 6 到 15 个字符', trigger: 'blur' }
                    ],
                    email: [
                        { required: true, validator: checkEmail, trigger: 'blur' }
                    ],
                    mobile: [
                        { required: true, validator: checkMobile, trigger: 'blur' }
                    ]
                },

                editFromRules: {
                    email: [
                        { required: true, validator: checkEmail, trigger: 'blur' }
                    ],
                    mobile: [
                        { required: true, validator: checkMobile, trigger: 'blur' }
                    ]
                },
            }
        },
        created() {
            this.getAdminList();
        },
        methods: {
            //获取用户列表
            async getAdminList() {
                const {data: res} = await this.$http.get('/getAdminList',{params: this.params})
                console.log(res);
                if (res.status != 1) return this.$message.error(res.msg);
                this.adminList = res.content.list;
                this.total = res.content.total;
            },

            //分页
            handleSizeChange(newSize) {
                this.params.limit = newSize
                this.getAdminList()
            },
            handleCurrentChange(newPage) {
                this.params.p = newPage
                this.getAdminList()
            },

            //改变状态
            changeStatus(adminInfo) {
                this.$http.put('/changeStatus',{ id:adminInfo.id, type: adminInfo.status});
            },
            //重置表单
            addDiglogClosed() {
                this.$refs.addFromRef.resetFields();
            },
            //新增用户
            add() {
                this.$refs.addFromRef.validate(async valid=> {
                    if (!valid) return
                    const {data: res} = await this.$http.post('/addAdmin', this.addFrom);
                    if(res.status != 1) return this.$message.error(res.msg);
                    this.$message.success(res.msg);
                    this.addAdmin = false;
                    this.getAdminList();

                })

            },

            //获取用户数据
            async showAdminInfo(id) {
                const {data: res} = await this.$http.get('/getAdminInfo?id='+id);
                if (res.status != 1) return this.$message.error(res.msg);
                this.editFrom = res.content;
                this.editAdmin = true;

            },

            //编辑用户
            edit() {
                this.$refs.editFromRef.validate(async valid=> {
                    if (!valid) return
                    const {data: res} = await this.$http.post('/addAdmin',{id: this.editFrom.id, email: this.editFrom.email, mobile: this.editFrom.mobile});
                    if(res.status != 1) return this.$message.error(res.msg);
                    this.$message.success(res.msg);
                    this.editAdmin = false;
                    this.getAdminList();
                })
            },

             async delAdmin(id) {
                const confirmResult = await this.$confirm('是否删除该用户?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).catch(res => res)

                 if(confirmResult != 'confirm') {
                     return this.$message.info('取消删除');
                 }
                const {data: res} = await this.$http.post('/delAdmin?id='+id);
                if(res.status != 1) return this.$message.error(res.msg);
                this.$message.success(res.msg);
                this.getAdminList();
            }
        }
    }
</script>

<style scoped>


</style>
