# 写给项目新人的须知

## 项目结构
一些特殊文件夹我已经在下面标出来了
```Javascript
.
├── build         // 构建相关文件夹，一般开发者不需要关心
├── config        // 配置项，包括生产环境和开发环境的常量配置，包括API地址，七牛地址等。
├── src           // 源代码
│   ├── api         // api相关封装
│   │   └── mock      // mock数据部分
│   ├── assets      // 一些静态资源
│   ├── components  // 公用组件部分
│   ├── directive   // directive文件夹
│   ├── filters     // filters文件夹
│   ├── router      // 路由部分
│   ├── store       // Vuex的store
│   │   └── modules   // Vuex的模块，现阶段只有user
│   ├── styles      // 公共样式
│   ├── utils       // 公共工具类
│   └── views       // **所有页面,最主要的部分**
│       └── layout    // 布局部分的代码，所有路由都使用这一种布局，以后有需求可能会增加其他layout
└── static          // 暂时没用
```

## mock的使用
### 开关
现在是在config里面配置，之后可能会做package.json里面scripts来开启mock。
```Javascript
module.exports = {
    NODE_ENV: '"development"',
    API_MOCK: true,  // this is switch
}
```

### Mock方法
进入**src/api/mock**文件夹

在对应文件夹下写mock代码
```Javascript
// api/post
export function getMockTest() {
  return fetch({
    url: '/test',
    method: 'get'
  })
}

// api/mock/post
mockAdapter.onGet('/test').reply(200, {
    test: 'keke'
})
```
在自己的组件里面引入对应api
```Javascript
import { getMockTest } from 'api/post';
```

这种方式比较无痛，不需要mock的时候直接关闭开关就行。


## 关于新页面布局的一些规范
就现在来看，页面只有两个类型，一个是包含侧边栏aside的，另一个是不包含只有一块的。

由于布局样式很多重复代码，所以我抽离了出来放到了styles文件夹下的layout.scss做全局样式。

### 新页面规范
所有页面根元素需要加上**.view-container**

如果是包含侧边栏的，页面结构应该这样
```
<template>
  <div id="" class="view-container">
    <aside>
    </aside>
    <section>
    </section>
  </div>
</template>
```
如果不包含，则没有aside标签。
> 这里为什么把aside放前面其实是因为css选择器没有前向的选择，只有+选择器去选择兄弟，由于有aside和没有aside的section样式不一样，所以做了个折中，大家编码的时候注意一下aside和section的前后顺序。

## 关于页面样式的一些规范
本项目使用.vue单文件开发，并且使用scss预处理器。项目使用sass-resources-loader加载了全局设置文件，开发者直接在vue文件的style标签里面可以直接使用变量
```Vue
<style rel="stylesheet/scss" lang="scss" scoped>
.app-wrapper {
  @include clearfix; // 在mixin.scss定义
  position: relative;
  height: 100%;
  width: 100%;
  .navbar-wrapper {
    z-index: 100;
  }
  .main-container {
    background-color: #f8f9fa;
    height: calc(100vh - $nav-height); // 变量在_variables.scss中定义
    overflow: auto;
    transition: all 0.28s ease-out;
  }
}
</style>
```
配置在build/utils.js中

## 代码提交流程
组员代码提交使用pull request提交
1. 组员先fork我们的wuanlife库
2. 所有的更改在自己的fork库中完成
3. 修改完毕后push更改到自己的库中
4. 提交pull request把代码合并到wuanlife库

参考教程[Git：fork 源仓库、fork 仓库副本和 local 仓库的同步](https://www.jianshu.com/p/29775d91f536)

## 开发需要注意的东西
所有对于公共区域文件(styles、utils等)的修改都必须在讨论组里面告知