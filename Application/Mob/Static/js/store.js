$(document).ready(function () {
    add_goods();//新增商品
    create_shop();//创建微店
    cart_add_item();//加入购物车
    buy_add_item();//立即购买
    place_order();//下单
    item_ok();//确认订单
    del_goods();//删除购物车商品
    collect();//收藏，取消收藏
    sure_send();//确认发货
    change_price();//价格修改
    do_search_shop;//搜索商店
    add_more_shop();//加载更多店铺，包含热门店铺和普通店铺
    add_more_goods();//加载更多商品，包含最新上架和店铺查看,分类查看
    surePay();
});

//新增商品
var add_goods = function () {
    $('.add-goods').unbind('click');
    $('.add-goods').click(function () {
        var data = $(".goods").serialize();
        var url = $(".goods").attr('data-url');
        var index_url = $(this).attr('data-detail-url');
        $.post(url, data, function (msg) {
            if (msg.status == 1) {
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.href = index_url;
                }, 500);
            } else {
                toast.error(msg.info);
            }

        }, 'json')
    });
}
//新增店铺
var create_shop = function () {
    $('.createShop').unbind('click');
    $('.createShop').click(function () {
        var data = $(".shop").serialize();
        var url = $(".shop").attr('data-url');
        var index_url = $(this).attr('data-shop-url');
        $.post(url, data, function (msg) {
            if (msg.status == 1) {
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.href = index_url;
                }, 500);
            } else {
                toast.error(msg.info);
            }

        }, 'json')
    });
}
//加入购物车
var cart_add_item = function () {
    $('.cartadditem').unbind('click');
    $('.cartadditem').click(function () {
        var data = $(".addcar").serialize();
        var url = $(this).attr('data-url');
        $.post(url, data, function (msg) {
            if (msg.status == 1) {
                toast.success(msg.info);
            } else {
                toast.error(msg.info);
            }

        }, 'json')
    });
}
//立即购买
var buy_add_item = function () {
    $('.buyadditem').unbind('click');
    $('.buyadditem').click(function () {
        var data = $(".addcar").serialize();
        var url = $(this).attr('data-url');
        var index_url = $(this).attr('data-redirect-url');
        $.post(url, data, function (msg) {
            if (msg.status == 1) {
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.href = index_url;
                }, 500);
            } else {
                toast.error(msg.info);
            }

        }, 'json')
    });
}
//下单
var place_order = function () {
    $('.placeorder').unbind('click');
    $('.placeorder').click(function () {
        $(".addorder").submit();

    });
}
//确认订单
var item_ok = function () {
    $('.item-ok').unbind('click');
    $('.item-ok').click(function () {
        var data = $(".sure-item").serialize();
        var url = $(".sure-item").attr('data-url');
        var index_url = $(this).attr('data-redirect-url');
        $.post(url, data, function (msg) {
            if (msg.status == 1) {
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.href = index_url;
                }, 500);
            } else {
                toast.error(msg.info);
            }

        }, 'json')
    });
}
//删除购物车商品
var del_goods = function () {
    $('.del-goods').unbind('click');
    $('.del-goods').click(function () {
        var id = $(this).attr('data-id');
        var url = $(this).attr('data-url');
        $.post(url, {id: id}, function (msg) {
            if (msg.status == 1) {
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.reload();
                }, 500);
            } else {
                toast.error(msg.info);
            }

        }, 'json')
    });
}
//收藏取消收藏
var collect = function () {
    $('.collect').unbind('click');
    $('.collect').click(function () {
        var data_id = $(this).attr('data-id');
        var url = $(this).attr('data-url');
        $.post(url, {id: data_id}, function (msg) {
            if (msg.status == 1) {
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.reload();
                }, 500);
            } else if (msg.status == 2) {
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.reload();
                }, 500);
            } else {
                toast.error(msg.info);
            }
        })
    })
}
//确认发货
var sure_send = function () {
    $('.sureSend').unbind('click');
    $('.sureSend').click(function () {
        var data = $(".sure-send").serialize();
        var url = $(".sure-send").attr('data-url');
        $.post(url, data, function (msg) {
            if (msg.status == 1) {
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.reload();
                }, 500);
            } else {
                toast.error(msg.info);
            }

        }, 'json')
    });
}
//修改价格
var change_price = function () {
    $('.sure-change').unbind('click');
    $('.sure-change').click(function () {
        var data = $(".change-price").serialize();
        var url = $(".change-price").attr('data-url');
        $.post(url, data, function (msg) {
            if (msg.status == 1) {
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.reload();
                }, 500);
            } else {
                toast.error(msg.info);
            }

        }, 'json')
    });
}
//搜索功能
var do_search_shop = function () {
    $('.do-search-shop').unbind('click');
    $('.do-search-shop').click(function () {
        var key = $(this).parents('.shop-key').find('.key').val();
        var url = $(this).attr('data-url');
        var type = $(this).attr('aType');
        $.post(url, {key: key, type: type}, function (msg) {
            if (msg.status) {
                $(".shoplist").html(msg.html);
                page++;
            } else {
                toast.error(msg.info);
            }

        })
    })
}
var do_search_goods = function () {
    $('.do-search-goods').unbind('click');
    $('.do-search-goods').click(function () {
        var key = $(this).parents('.good-key').find('.key').val();
        var url = $(this).attr('data-url');
        var type = $(this).attr('aType');
        $.post(url, {key: key, type: type}, function (msg) {
            if (msg.status) {
                $(".goodlist").html(msg.html);
                page++;
            } else {
                toast.error(msg.info);
            }

        })
    })
}
//加载更多店铺
var page = 1;
var add_more_shop = function () {
    $('.add-more-shop').unbind('click');
    $('.add-more-shop').click(function () {
        $(".add-more-shop").html("查看更多...");
        var url = $(this).attr('data-url');
        var type = $(this).attr('data-type');
        $.post(url, {page: page + 1, type: type}, function (msg) {
            if (msg.status) {
                $(".shoplist").append(msg.html);
                page++;
            } else {
                $(".add-more-shop").html("全部加载完成！");
                $(".look-more").delay(500).hide(0);
            }
        })
    })
}
//加载更多商品
var add_more_goods = function () {
    $('.add-more-goods').unbind('click');
    $('.add-more-goods').click(function () {
        $(".add-more-goods").html("查看更多...");
        var url = $(this).attr('data-url');
        var goodsId = $(this).attr('data-type');
        var shopId = $(this).attr('shop-id');
        /*var type = $(this).attr('data-type');*/
        $.post(url, {page: page + 1, goodsId: goodsId,shopId:shopId}, function (msg) {
            if (msg.status) {
                $(".goodslist").append(msg.html);
                page++;
            } else {
                $(".add-more-goods").html("全部加载完成！");
                $(".look-more").delay(500).hide(0);
            }
        })
    })
}

//付款
var surePay = function () {
    $('.surePay').unbind('click');
    $('.surePay').click(function () {
        var url = $(this).attr('data-url');
        var order_id = $(this).attr('order-id');
        var index_url = $(this).attr('data-index');
        $.post(url, {id:order_id}, function (msg) {
            if (msg.status == 1) {
                toast.success(msg.info);
                setTimeout(function () {
                    window.location.href = index_url;
                }, 500);
            } else {
                toast.error(msg.info);
            }
        }, 'json')
    });
}