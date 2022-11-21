/**
 * Created by phongtx on 04/06/2016.
 */
$(document).ready(function () {
    $('#strBirthday').datepicker({
        format: "dd/mm/yyyy",
        autoclose: true
    });
    $("#edit").on('click', 'input[type=submit]', function (e) {
        $('#edit').validate({
            rules: {
                strFullname : {
                    required: true
                },
                strBirthday : {
                    required: true
                },
                strAddress : {
                    required: true
                },
                strPhone : {
                    required: true
                },
                intGender : {
                    required: true
                }
            },
            messages: {
                strFullname : {
                    required: 'Họ tên không được để trống.'
                },
                strBirthday : {
                    required: 'Ngày sinh không được để trống.'
                },
                strAddress : {
                    required: 'Địa chỉ không được để trống.'
                },
                strPhone : {
                    required: 'Số điện thoại không được để trống.'
                },
                intGender : {
                    required: 'Giới tính không được để trống.'
                }
            },
            submitHandler: function (form) {
                $.ajax({
                    type: "POST",
                    url: SITE_URL + "/users/ajax-update-user",
                    data: $("#edit").serialize(),
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
                                location.reload();
                            }, 2000);
                        }
                    }
                });
                return false;
            }
        });
    });
});
