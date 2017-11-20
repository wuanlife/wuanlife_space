# wuanlife_vuepc

## 生产环境部署说明
### 克隆及编译
你可以选择克隆项目到服务器再编译（有可能因为内存原因而编译失败）或者在本地编译后上传到服务器中（推荐）。上传可以使用scp或者filezilla等
```
git clone https://github.com/wuanlife/wuanlife.git

cd wuanlife
npm install
// 也许你在编译之前需要对config进行一些配置
npm run build:prod  // 生产环境
```

### 配置
```
cd config
nano prod.env.js (生产环境只需要修改这个文件)
```

```
module.exports = {
    NODE_ENV: '"production"',               // 这个不要改
    BASE_API: '"http://47.88.58.119:800/"', // API服务器地址
    APP_ORIGIN: '"http://wuanlife.com"',    // app的origin，用于CORS，现在暂时没用
    // 七牛域名
    QINIU_DOMAIN_URL: '"http://7xlx4u.com1.z0.glb.clouddn.com/"'  // 用于七牛上传图片
}
```

### 配置Web Server
编译后生成的dist文件夹就是生产环境下的wuanlife项目。根据用户使用情况的不同，自行在nginx或者apache中配置路由。
