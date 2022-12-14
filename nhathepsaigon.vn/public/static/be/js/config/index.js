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
                        required: 'Vui l??ng nh???p website link'
                    },
                    strWebName: {
                        required: 'Vui l??ng nh???p website name'
                    },
                    strWebDescription: {
                        required: 'Vui l??ng nh???p website description'
                    },
                    strEmail: {
                        required: 'Vui l??ng nh???p email h??? th???ng',
                        isemail: 'Email kh??ng h???p l???',
                        maxlength: 'Chi???u d??i t???i ??a c???a email l?? 100 k?? t???',
                        minlength: 'Chi???u d??i t???i thi???u c???a email l?? 6 k?? t???'
                    },
                    strLinkFb: {
                        required: 'Vui l??ng nh???p link facebook fanpage.'
                    },
                    strLinkTwitter: {
                        required: 'Vui l??ng nh???p link twitter.'
                    },
                    strLinkGPlus: {
                        required: 'Vui l??ng nh???p link G+.'
                    },
                    strLinkYoutube: {
                        required: 'Vui l??ng nh???p link youtube.'
                    },
                    strTextBanner: {
                        required: 'Vui l??ng nh???p ti??u ????? box text banner.'
                    },
                    strColorTextBanner: {
                        required: 'Vui l??ng ch???n m??u box text banner.'
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
                                title: 'Th??ng b??o',
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