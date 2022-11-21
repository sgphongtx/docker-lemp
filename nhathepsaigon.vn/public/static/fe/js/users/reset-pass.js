/**
 * Created by phongtx on 04/06/2016.
 */
$(document).ready(function () {
    $("#reset-pass").on('click', 'input[type=submit]', function (e) {
        $('#reset-pass').validate({
            rules: {
                strPass : {
                    required: true
                },
                strConfirmPass : {
                    required: true
                }
            },
            messages: {
                strPass : {
                    required: 'Mật khẩu mới không được để trống.'
                },
                strConfirmPass : {
                    required: 'Xác nhận mật khẩu mới không được để trống.'
                }
            },
            submitHandler: function (form) {
                $.ajax({
                    type: "POST",
                    url: SITE_URL + "/users/ajax-update-pass",
                    data: $("#reset-pass").serialize(),
                    dataType: "json",
                    beforeSend: function () {
                        $('#modal-loading').modal('show');
                    },
                    success: function (returnData) {
                        $('#modal-loading').modal('hide');
                        bootbox.dialog({
                            message: returnData.msg,
                            title: 'Thông báo',
                            title_className: 'bg-primary'
                        });
                        if (returnData.error == 0) {
                            setTimeout(function () {
                                location.href = SITE_URL;
                            }, 2000);
                        }
                    }
                });
                return false;
            }
        });
    });
});