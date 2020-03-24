<template>

    <div class="login_container">

        <div class="login_box">
            <div class="avatar_box">
                <img src="../../image/logo.png">
            </div>
            <!--    登录表单区域-->
            <el-form :model="loginForm" :rules="loginFormRules" ref="loginFromRef" class="login_form">
                <!--用户名-->
                <el-form-item prop="username">
                    <el-input prefix-icon="el-icon-user-solid" v-model="loginForm.username" placeholder="请输入用户名"></el-input>
                </el-form-item>
                <!--密码-->
                <el-form-item prop="password">
                    <el-input prefix-icon="el-icon-success" v-model="loginForm.password" type="password" placeholder="请输入密码"></el-input>
                </el-form-item>
                <!--按钮-->
                <el-form-item class="btns">
                    <el-button type="primary" @click="login()">登录</el-button>
                    <el-button type="info" @click="resetLoginForm()">重置</el-button>
                </el-form-item>
            </el-form>
        </div>

    </div>

</template>

<script>
    export default {
        data() {
            return {
                //登录表单的数据绑定
                loginForm: {
                    username: 'admin',
                    password: '123456',
                },
                categoryList: [],
                //表单验证规则对象
                loginFormRules: {
                    //验证用户名是否合法
                    username: [
                        //required是否是必填项  trigger触发时机 文本框失去焦点的时候
                        { required: true, message: '请输入用户名', trigger: 'blur' },
                        { min: 5, max: 10, message: '长度在 5 到 10 个字符', trigger: 'blur' }
                    ],

                    //验证密码是否合法
                    password: [
                        { required: true, message: '请输入密码', trigger: 'blur' },
                        { min: 6, max: 15, message: '长度在 6 到 15 个字符', trigger: 'blur' }
                    ],
                },
                //重置表单
                resetLoginForm() {
                    console.log(this);
                    this.$refs.loginFromRef.resetFields();
                },
                login() {
                    this.$refs.loginFromRef.validate(async valid => {
                        if(!valid) return;
                        const {data: res} = await this.$http.post('/login', this.loginForm);
                        if(res.status != 1) return this.$message.error(res.msg);
                        this.$message.success(res.msg);
                        window.sessionStorage.setItem('role_id', res.content.role_id);
                        this.$router.push('/index');

                    });
                },

            }
        },
    }
</script>

<style scoped>
    .login_container{
        background-color: #2b4b6b;
        height: 100%;
    }
    .login_box{
        height: 300px;
        width: 450px;
        background-color: white;
        border-radius: 3px;
        position:absolute;
        left:50%;
        top:50%;
        transform: translate(-50%, -50%);
    }
    .avatar_box{
        height: 130px;
        width: 130px;
        border: 1px solid #eee;
        border-radius: 50%;
        padding: 10px;
        box-shadow:0 0 10px #ddd;
        position: absolute;
        left:50%;
        transform:translate(-50%, -50%);
        background-color: #fff;

    }
    .avatar_box img{
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background-color: #eee;
    }

    .login_form{
        position: absolute;
        bottom: 0;
        width: 100%;
        padding:0 20px;
        box-sizing: border-box;
    }
    .btns{
        display: flex;
        justify-content: flex-end;
    }
</style>
