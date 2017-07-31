<template>
  <div class="register-container">
  	<section>
  		<header>注册</header>
  		<div class="form-content">
  			<el-form label-width="100px" class="demo-ruleForm">
            <el-form-item label="邮箱" prop="email" class="form-inputy">
              <el-input placeholder="输入邮箱"></el-input>
            </el-form-item>
            <el-form-item label="昵称" prop="nickname" class="form-inputy">
              <el-input auto-complete="off" placeholder="输入昵称"></el-input>
            </el-form-item>
            <el-form-item label="密码" prop="password" class="form-inputy">
              <el-input type="password" auto-complete="off" placeholder="输入密码"></el-input>
            </el-form-item>
            <el-form-item label="邀请码" prop="inviteword" class="form-inputy">
              <el-input auto-complete="off" placeholder="输入邀请码"></el-input>
            </el-form-item>
            <el-form-item label-width="100px" class="form-btny">
              <el-button type="primary">注册</el-button>
            </el-form-item>
          </el-form>
  		</div>
  	</section>
  </div>
</template>
<script></script>
<style rel="stylesheet/scss" lang="scss" scoped>
.register-container{
	display: flex;
    justify-content: space-between;
    margin: auto;
    max-width: 660px;
    min-width: 380px;
    section {
      flex: 1;
      header {
        margin: 15px 0 20px 0;
        font-family:PingFangHK-Medium;
        font-size:18px;
        color:#5677fc;
      }
      div.form-content {
        width: 100%;
        background:#ffffff;
        border-radius:4px;
        width:660px;
        height:650px;
        padding-top:40px;
      }
    }
}
</style>