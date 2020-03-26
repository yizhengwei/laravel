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
                    <el-input placeholder="请输入内容" v-model="params.q" clearable @clear="getAdminList" size="mini">
                        <el-button slot="append" icon="el-icon-search" @click="getAdminList" ></el-button>
                    </el-input>
                </el-col>

                <el-col :span="4">
                    <el-button
                        type="primary"
                        @click="showAdminDig(addFrom, 'N')"
                        size="mini"
                        :disabled="role_id == 1 ? false : true"
                    >添加用户
                    </el-button>
                </el-col>

            </el-row>



            <el-table
                :data="adminList"
                border
                stripe
                style="width: 100%">
                <el-table-column prop="id" label="#" width="80"></el-table-column>
                <el-table-column prop="username" label="用户名"  width="180"></el-table-column>
                <el-table-column prop="name" label="角色"  width="180"></el-table-column>
                <el-table-column prop="mobile" label="手机号码"  width="180"></el-table-column>
                <el-table-column prop="email" label="邮箱"  width="180"></el-table-column>

                <el-table-column prop="status" label="状态" width="100">
                    <template v-slot="scope">
                        <el-switch
                            v-model="scope.row.status"
                            active-value="1"
                            inactive-value="0"
                            @change="changeStatus(scope.row)"
                            :disabled="role_id == 1 ? false : true"
                            >
                        </el-switch>
                    </template>
                </el-table-column>

                <el-table-column label="操作">
                    <template v-slot="scope">
                        <el-button
                            type="text"
                            size="mini"
                            v-if="role_id == 1"
                        >
                            <span style="color:#1890FF" @click="showAdminDig(scope.row, 'Y')">编辑</span>
<!--                            <span style="color:#1890FF" @click="showAdminInfo(scope.row.id)">编辑</span>-->
                        </el-button>
                        <el-button
                            type="text"
                            size="mini"
                            v-if="role_id == 1"
                        >
                            <span style="color:#1890FF">分配权限</span>
                        </el-button>
                        <el-button
                            type="text"
                            size="mini"
                            v-if="role_id == 1"
                        >
                            <span style="color:red" @click="delAdmin(scope.row.id)">删除</span>
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
                    <el-input v-model="addFrom.username" :disabled="isEdit"></el-input>
                </el-form-item>

                <el-form-item label="密码" prop="password">
                    <el-input v-model="addFrom.password" ></el-input>
                </el-form-item>

                <el-form-item label="邮箱" prop="email">
                    <el-input v-model="addFrom.email"></el-input>
                </el-form-item>

                <el-form-item label="手机号码" prop="mobile">
                    <el-input v-model="addFrom.mobile"></el-input>
                </el-form-item>

                <el-form-item label="角色" >
                    <el-select v-model="addFrom.role_id" placeholder="请选择">
                        <el-option
                            v-for="item in roleList"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-form>

            <span slot="footer" class="dialog-footer">
                <el-button @click="addAdmin = false">取 消</el-button>
                <el-button type="primary" @click="add()">确 定</el-button>
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
                role_id: '', //权限id

                isEdit: false,

                params: {
                    q: '',
                    p: 1,
                    limit: 10,

                },
                value: "100",
                adminList: [],
                roleList: [],
                total: 0,
                addAdmin: false,
                //双向绑定input输入框
                addFrom: {
                    username: '',
                    password: '',
                    email: '',
                    mobile: '',
                    role_id: '',
                },

                status: '',
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

            }
        },
        created() {
            this.getAdminList();
        },
        methods: {
            //获取用户列表
            async getAdminList() {
                var that = this;
                const {data: res} = await this.$http.get('/getAdminList',{params: this.params})
                if (res.status != 1) return this.$message.error(res.msg);
                that.adminList = res.content.list;
                that.total = res.content.total;
                that.roleList = res.content.role;
                that.role_id = window.sessionStorage.getItem('role_id');
                console.log(that.role_id);
            },

            showAdminDig(row, isUserEdit) {
                let that = this;
                console.log(row)
                this.addAdmin = true;

                if(isUserEdit == 'Y') {
                    this.addFrom =JSON.parse(JSON.stringify(row));
                    that.isEdit = true;
                }
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
            async changeStatus(adminInfo) {
                console.log(adminInfo);
                const{data: res} = await this.$http.post('/changeStatus',{ id:adminInfo.id, type: adminInfo.status})
                if(res.status != 1) {
                    adminInfo.status = !adminInfo.status;
                    return this.$message.error(res.msg);
                }
                this.getAdminList();
                return this.$message.success(res.msg);
            },

            //重置表单
            addDiglogClosed() {
                this.$refs.addFromRef.resetFields();
            },
            //新增用户
            add() {
                console.log(this.addFrom)
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
