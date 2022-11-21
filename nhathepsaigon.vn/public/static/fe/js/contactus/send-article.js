/**
 * Created by phongtx on 30/05/2016.
 */
$(document).ready(function () {
    $('input[name=strTitle]').focus();
    $(".select2").select2();
    CKEDITOR.replace('ckeditor');
    sendArticle.loadCaptcha();
    $('#loadCaptcha').on('click',function(){
        sendArticle.loadCaptcha();
    });
    $("#add").on('click', 'input[type=submit]', function (e) {
        $('#add').validate({
            rules: {
                strTitle : {
                    required: true
                },
                strDesc : {
                    required: true
                },
                ckeditor : {
                    required: true
                },
                intCate: {
                    required: true
                },
                strCode: {
                    required: true
                }
            },
            messages: {
                strTitle : {
                    required: 'Hãy nhập tiêu đề bài viết.'
                },
                strDesc : {
                    required: 'Hãy nhập lời mở đầu bài viết.'
                },
                ckeditor : {
                    required: 'Hãy nội dung bài viết.'
                },
                intCate: {
                    required: 'Hãy chọn danh mục.'
                },
                strCode: {
                    required: 'Hãy nhập mã xác nhận'
                }
            },
            submitHandler: function (form) {
                $.ajax({
                    type: "POST",
                    url: SITE_URL + "/contactus/ajax-add",
                    data: $("#add").serialize(),
                    dataType: "json",
                    beforeSend: function () {
                        $('#modal-loading').modal('show');
                    },
                    success: function (returnData) {
                        $('#modal-loading').modal('hide');
                        if (returnData.error == 2) {
                            sendArticle.loadCaptcha();
                        }
                        bootbox.dialog({
                            message: returnData.msg,
                            title: 'Thông báo',
                            title_className: 'bg-primary'
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
var sendArticle = {
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