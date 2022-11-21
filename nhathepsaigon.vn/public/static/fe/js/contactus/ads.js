/**
 * Created by phongtx on 30/05/2016.
 */
$(document).ready(function () {
    $('input[name=strName]').focus();
    CKEDITOR.replace('ckeditor');
    ads.loadCaptcha();
    $('#loadCaptcha').on('click',function(){
        ads.loadCaptcha();
    });
    $("#add").on('click', 'input[type=submit]', function (e) {
        $.validator.addMethod("isemail", function (value, element) {
            return /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/.test(value);
        });
        $.validator.addMethod("isphone", function (value, element) {
            return /^\+?\d{1,3}?[- .]?\(?(?:\d{2,3})\)?[- .]?\d\d\d[- .]?\d\d\d\d$/.test(value);
        });
        $('#add').validate({
            rules: {
                strName : {
                    required: true
                },
                strEmail : {
                    required: true,
                    isemail: true,
                    maxlength: 100,
                    minlength: 6
                },
                strPhone : {
                    required: true,
                    isphone : true
                },
                ckeditor : {
                    required: true
                },
                strCode: {
                    required: true
                }
            },
            messages: {
                strName : {
                    required: 'Hãy nhập họ tên.'
                },
                strEmail : {
                    required: 'Hãy nhập email',
                    isemail: 'Email không hợp lệ',
                    maxlength: 'Chiều dài tối đa của email là 100 ký tự',
                    minlength: 'Chiều dài tối thiểu của email là 6 ký tự'
                },
                strPhone : {
                    required: 'Hãy nhập số điện thoại.',
                    isphone : 'Số điện thoại không hợp lệ'
                },
                ckeditor : {
                    required: 'Hãy nội dung.'
                },
                strCode: {
                    required: 'Hãy nhập mã xác nhận'
                }
            },
            submitHandler: function (form) {
                $.ajax({
                    type: "POST",
                    url: SITE_URL + "/contactus/ajax-insert-ads",
                    data: $("#add").serialize(),
                    dataType: "json",
                    beforeSend: function () {
                        $('#modal-loading').modal('show');
                    },
                    success: function (returnData) {
                        $('#modal-loading').modal('hide');
                        if (returnData.error == 2) {
                            ads.loadCaptcha();
                        }
                        bootbox.dialog({
                            message: returnData.msg,
                            title: 'Thông báo',
                            title_className: 'bg-primary',
                            buttons: {
                                close: {
                                    label: "Close",
                                    className: "btn-default",
                                    callback: function () {
                                        setTimeout(function () {
                                            location.reload();
                                        }, 1000);
                                    }
                                }
                            }
                        });
                        /*if (returnData.error == 0) {
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                        }*/
                    }
                });
                return false;
            }
        });
    });
});
var ads = {
    loadCaptcha: function() {
        $.ajax({
            type: 'GET',
            url: SITE_URL + '/captcha/show',
            dataType: 'json',
            beforeSend: function() {
            },
            success: function(response) {
                if (response != false) {
                    $('#showCaptcha').html(response.html);
                }
            }
        });
    }
}