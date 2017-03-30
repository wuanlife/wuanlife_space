;
! function(window, undefined) {

    /**
     * 默认策略
     * @type {Object}
     */
    var strategies = {
        isNonEmpty: function(value, errorMsg) {
            if (value === '') {
                return errorMsg;
            }
        },
        minLength: function(value, length, errorMsg) {
            if (value.length < length) {
                return errorMsg;
            }
        },
        isMobile: function(value, errorMsg) {
            if (!/(^1[3|5|8][0-9]{9}$)/.test(value)) {
                return errorMsg;
            }
        }
    };


    function HJformcheck(){
        this.cache = [];
    };
    HJformcheck.prototype.version = '0.0.1';
    HJformcheck.prototype.add = function(dom, rules) {
        //兼容jquery
        var mydom = dom;
        if(jQuery && dom instanceof jQuery) {
            mydom = dom[0];
        }

        var self = this;
        for (var i = 0, rule; rule = rules[i++];) {
            (function(rule) {
                var strategyAry = rule.strategy.split(':');
                var errorMsg = rule.errorMsg;
                self.cache.push(function() {
                    var strategy = strategyAry.shift();

                    strategyAry.unshift(mydom.value);
                    strategyAry.push(errorMsg);
                    return strategies[strategy].apply(mydom, strategyAry);
                });
            })(rule)
        }
    };
    HJformcheck.prototype.start = function() {
        for (var i = 0, validatorFunc; validatorFunc = this.cache[i++];) {
            var errorMsg = validatorFunc();
            if (errorMsg) {
                return errorMsg;
            }
        }
    };

    /**
     * extend strategies
     * @param  {String} strategyName    the name of this strategy
     * @param  {Function} strategy      the strategy
     * @return {Boolean} add strategy successed or not(already have or bad format)
     */
    HJformcheck.prototype.extend = function(strategyName, strategy) {
        strategies[strategyName] = strategy;
    }



    window.HJformcheck = HJformcheck;
}(window);