/**
 * Created by phongtx on 14/04/2015.
 */
$(document).ready(function () {
    $(function(){
        var strColorTextBanner = $('#strColorTextBanner');
        strColorTextBanner.colorpickerplus();
        strColorTextBanner.on('changeColor', function(e,color){
            if(color==null)
                $(this).val('transparent').css('background-color', '#fff');//tranparent
            else
                $(this).val(color).css('background-color', color);
        });
    });
    config.submit();
    $('.cancel').on('click', function (e) {
        location.href = SITE_URL + '/backend/config';
    });
});
var config = {
    submit : function(){
        $("#add").on('click', 'button[type=submit]', function (e) {
            $.validator.addMethod("isemail", function (value, element) {
                return /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/.test(value);
            });
            $('#add').validate({
                errorPlacement: function (error, element) {
                    element = element.parents('div.form-group').length == 1 ? element.parents('div.form-group') : element;
                    error.insertAfter(element);
                },
                rules: {
                    strWebLink: {
                        required: true
                    },
                    strWebName: {
                        required: true
                    },
                    strWebDescription: {
                        required: true
                    },
                    strEmail: {
                        required: true,
                        isemail: true,
                        maxlength: 100,
                        minlength: 6
                    },
                    strLinkFb: {
                        required: true
                    },
                    strLinkTwitter: {
                        required: true
                    },
                    strLinkGPlus: {
                        required: true
                    },
                    strLinkYoutube: {
                        required: true
                    },
                    strTextBanner: {
                        required: true
                    },
                    strColorTextBanner: {
                        required: true
                    }
                },
                messages: {
                    strWebLink: {
                        required: 'Vui lòng nhập website link'
                    },
                    strWebName: {
                        required: 'Vui lòng nhập website name'
                    },
                    strWebDescription: {
                        required: 'Vui lòng nhập website description'
                    },
                    strEmail: {
                        required: 'Vui lòng nhập email hệ thống',
                        isemail: 'Email không hợp lệ',
                        maxlength: 'Chiều dài tối đa của email là 100 ký tự',
                        minlength: 'Chiều dài tối thiểu của email là 6 ký tự'
                    },
                    strLinkFb: {
                        required: 'Vui lòng nhập link facebook fanpage.'
                    },
                    strLinkTwitter: {
                        required: 'Vui lòng nhập link twitter.'
                    },
                    strLinkGPlus: {
                        required: 'Vui lòng nhập link G+.'
                    },
                    strLinkYoutube: {
                        required: 'Vui lòng nhập link youtube.'
                    },
                    strTextBanner: {
                        required: 'Vui lòng nhập tiêu đề box text banner.'
                    },
                    strColorTextBanner: {
                        required: 'Vui lòng chọn màu box text banner.'
                    }
                },
                submitHandler: function (form) {
                    $.ajax({
                        type: "POST",
                        url: SITE_URL + "/backend/config/ajax-update",
                        data: $("#add").serialize(),
                        dataType: "json",
                        beforeSend: function () {
                        },
                        success: function (returnData) {
                            bootbox.dialog({
                                message: returnData.msg,
                                title: 'Thông báo',
                                title_className: 'bg-primary',
                                buttons: {
                                    close: {
                                        label: "Close",
                                        className: "btn-default",
                                        callback: function () {
                                            //Fix multi modal
                                            setTimeout(function () {
                                                var modal_count = $('.modal-dialog').length;
                                                if (modal_count > 0) {
                                                    $('body').addClass('modal-open');
                                                }
                                            }, 1000);
                                        }
                                    }
                                }
                            });
                            if (returnData.error == 0) {
                                $('#modal-add').modal('hide');
                                setTimeout(function () {
                                    location.href = SITE_URL + '/backend/config';
                                }, 2000);
                            }
                        }
                    });
                    return false;
                }
            });
        });
    }
};