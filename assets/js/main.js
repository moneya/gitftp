$(function () {

//    ajaxHelper
    window._ajax = function (arg) {
        return $.ajax(arg)
                .error(function (data) {
                    switch (data.status) {
                        case '0':
                            $.alert({title: 'Problem'})
                            break;
                    }
                })
    }

});