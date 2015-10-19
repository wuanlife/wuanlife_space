/**
 * 操纵toastor的便捷类
 * @type {{success: success, error: error, info: info, warning: warning}}
 */
var toast = {
    /**
     * 成功提示
     * @param text 内容
     * @param title 标题
     */
    success: function (text) {
       $.messager.show(text, {placement: 'bottom',type:'success'});
    },
    /**
     * 失败提示
     * @param text 内容
     * @param title 标题
     */
    error: function (text) {
       $.messager.show(text, {placement: 'bottom',type:'error'});
    },
    /**
     * 信息提示
     * @param text 内容
     * @param title 标题
     */
    info: function (text) {
        $.messager.show(text, {placement: 'bottom',type:'info'});
    },
    /**
     * 警告提示
     * @param text 内容
     * @param title 标题
     */
    warning: function (text, title) {
        $.messager.show(text, {placement: 'bottom',type:'warning'});
    },

    /**
     *  显示loading
     * @param text
     */
    showLoading: function () {
        $('body').append('<div class="big_loading"><img src="'+ThinkPHP.PUBLIC+'/images/big_loading.gif"/></div>');
    },
    /**
     * 隐藏loading
     * @param text
     */
    hideLoading: function () {
        $('div').remove('.big_loading');
    }
}