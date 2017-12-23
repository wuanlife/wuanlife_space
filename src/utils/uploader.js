// TODO: make it resuable and configurable

import { UploaderBuilder, Uploader } from 'qiniu4js';
import { getToken } from 'api/qiniu';

// qiniu4js uploader object
export const uploader = new UploaderBuilder()
  .debug(false)// 开启debug，默认false
  .domain({ http: 'http://upload.qiniu.com', https: 'https://up.qbox.me' })
  .retry(2)// 设置重传次数，默认0，不重传
  .compress(0.5)// 默认为1,范围0-1
  .scale([200, 0])  // 第一个参数是宽度，第二个是高度,[200,0],限定高度，宽度等比缩放.[0,100]限定宽度,高度等比缩放.[200,100]固定长宽
  .size(1024 * 1024)
  .chunk(true)
  .auto(true)
  .multiple(false)
  .accept(['.gif', '.png', '.jpg'])// 过滤文件，默认无，详细配置见http://www.w3schools.com/tags/
  .tokenShare(true)
  .tokenFunc((setToken, task) => {
    let token = '';
    getToken().then(res => {
      token = res.uploadToken
    });
    setTimeout(() => {
      setToken(token);
    }, 1000);
  })
  // 任务拦截器
  .interceptor({
      // 拦截任务,返回true，任务将会从任务队列中剔除，不会被上传
    onIntercept(task) {
      return task.file.size > 4 * 1024 * 1024;
    },
    // 中断任务，返回true，任务队列将会在这里中断，不会执行上传操作。
    onInterrupt(task) {
      if (this.onIntercept(task)) {
        alert('请上传小于4m的文件');
        return true;
      } else {
        return false;
      }
    }
  })
  .listener({
    onReady(tasks) {
        // 该回调函数在图片处理前执行,也就是说task.file中的图片都是没有处理过的
      // 选择上传文件确定后,该生命周期函数会被回调。

    }, onStart(tasks) {
        // 所有内部图片任务处理后执行
      // 开始上传
      console.log('upload start')
    }, onTaskGetKey(task) {

    }, onTaskProgress(task) {
        // 每一个任务的上传进度,通过`task.progress`获取
      console.log(task.progress);
    }, onTaskSuccess(task) {
      // 一个任务上传成功后回调
      console.log(task.result.key);// 文件的key
      console.log(task.result.hash);// 文件hash
      console.log(process.env.QINIU_DOMAIN_URL + task.result.key);
      return process.env.QINIU_DOMAIN_URL + task.result.key;
    }, onTaskFail(task) {
      // 一个任务在经历重传后依然失败后回调此函数

    }, onTaskRetry(task) {
      // 开始重传

    }, onFinish(tasks) {
        // 所有任务结束后回调，注意，结束不等于都成功，该函数会在所有HTTP上传请求响应后回调(包括重传请求)。

    }
  }).build();