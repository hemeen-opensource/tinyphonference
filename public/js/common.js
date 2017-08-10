


/**公用的异步*/
var ajaxsubmit = function (url, param, fun, type) {
    return $.ajax({
        type: isNullStr(type) ? "post" : type,
        url: url,
        timeout: 60 * 1000 * 5,
        data: param,
        cache: false,
        dataType: "json"
    }).done(function (datas) {
        if (datas) {
            //超时跳转登陆页
            if (datas.errorCode == '0010') {
                showtoast(datas.errorMsg, ctx + "/login");
            } else {
                fun(datas);
            }
        }
    }).fail(function (datas) {
        showtoast("网络错误，请重试");
    });
};


/**公用的同步*/
var ajaxsync = function (url, param, fun, type) {
    return $.ajax({
        type: isNullStr(type) ? "post" : type,
        url: url,
        timeout: 60 * 1000 * 5,
        data: param,
        cache: false,
        dataType: "json",
        async: false
    }).done(function (datas) {
        if (datas.success) {
            fun(datas);
            return
        }
        showtoast(datas.message);
    }).fail(function (datas) {
        showtoast("网络错误，请重试");
    });
};

function isNullStr(data) {
    if (data == null || data.trim() == "" || data == undefined) {
        return true;
    } else {
        return false;
    }
}

var showtoastNum = '';
function showtoast(tips, url) {
    if (showtoastNum != '') {
        clearInterval(showtoastNum);
    }
    $('.tips-toast').hide().html('');
    $('.tips-toast').css('display', 'block').html(tips);
    showtoastNum = setInterval(function () {
        hidetoast(url);
    }, 3000)
}
function hidetoast(url) {
    $('.tips-toast').hide();
    if (url != undefined && url != '' && url != 'undefined') {
        window.location.href = url;
    }
}

var countdown = 60;
function settime(val) {
    if (countdown == 0) {
        val.removeAttr("disabled");
        val.html('获取验证码');
        countdown = 60;
        return;
    } else {
        val.attr('disabled', "true");
        val.html("重新发送(" + countdown + ")");
        countdown--;
    }
    setTimeout(function () {
        settime(val)
    }, 1000)
}


// 月校验
function coomonMeetMonth(start_month,end_month) {
    var arr1 = start_month.split("-");
    var starttime = new Date(arr1[0], arr1[1], arr1[2]);
    var starttimes = starttime.getTime();
    var arr2 = end_month.split("-");
    var endtime = new Date(arr2[0], arr2[1], arr2[2]);
    var endtimes = endtime.getTime();
    if (starttimes > endtimes) {
        return '结束时间必须在开始时间后';
    }
    var startday = new Date(arr1[0], arr1[1]);
    var endday = new Date(arr2[0], arr2[1]);
    if(startday.getTime() != endday.getTime()){
        return '时间段必须在同一个自然月内';
    }
    return ''
}