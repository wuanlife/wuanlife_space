
    /**
     * Created by Administrator on 2015-4-30.
     */
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
        success: function (text, title) {
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "positionClass": "toast-bottom-center",
                "onclick": null,
                "showDuration": "1000",
                "hideDuration": "1000",
                "timeOut": "2000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            toastr.success(text, title);
        },
        /**
         * 失败提示
         * @param text 内容
         * @param title 标题
         */
        error: function (text, title) {
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "positionClass": "toast-bottom-center",
                "onclick": null,
                "showDuration": "1000",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }
            toastr.error(text, title);
        },
        /**
         * 信息提示
         * @param text 内容
         * @param title 标题
         */
        info: function (text, title) {
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "positionClass": "toast-bottom-center",
                "onclick": null,
                "showDuration": "1000",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }
            toastr.info(text, title);
        },
        /**
         * 警告提示
         * @param text 内容
         * @param title 标题
         */
        warning: function (text, title) {
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "positionClass": "toast-bottom-center",
                "onclick": null,
                "showDuration": "1000",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            toastr.warning(text, title);
        }
    };


