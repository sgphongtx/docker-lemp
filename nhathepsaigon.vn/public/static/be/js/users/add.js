/**
 * @name        :   add.js
 * @author      :   PhongTX
 * @copyright   :   Fpt Online
 * Date: 10/04/2015
 * Time: 08:45
 */
$(document).ready(function () {
    $('.act-add').on('click', function (e) {
        e.preventDefault();
        $('#modal-add').modal('show');
        var intUserId = $(this).attr('data-id');
        $.ajax({
            type: "POST",
            url: SITE_URL + '/backend/users/ajax-get-detail-user',
            data: {intUserId: intUserId},
            dataType: "json",
            beforeSend: function () {
            },
            success: function (returnData) {
                $('#modal-add .modal-content').html(returnData.html);
                $('#strBirthday').datepicker({
                    format: "dd/mm/yyyy",
                    autoclose: true
                });

                $('#frm-user-add').validate({
                    errorPlacement: function (error, element) {
                        element = element.parents('div.form-group').length == 1 ? element.parents('div.form-group') : element;
                        error.insertAfter(element);
                    },
                    errorElement: "div",
                    rules: {
                        strEmail: {
                            required: true,
                            email: true
                        },
                        intGroupId: {
                            required: true
                        },
                        strPassWord: {
                            required: true
                        },
                        strConfirmPassWord: {
                            required: true
                        },
                        strFullname: {
                            required: true
                        },
                        strBirthday: {
                            required: true
                        },
                        strAddress: {
                            required: true
                        },
                        strPhone: {
                            required: true
                        }
                    },
                    messages: {
                        strEmail: {
                            required: 'Email không được để trống',
                            email: 'Email không đúng định dạng'
                        },
                        intGroupId: {
                            required: 'Vui lòng chọn nhóm quyền'
                        },
                        strPassWord: {
                            required: 'Mật khẩu không được để trống'
                        },
                        strConfirmPassWord: {
                            required: 'Xác nhận mật khẩu không được để trống'
                        },
                        strFullname: {
                            required: 'Họ tên không được để trống'
                        },
                        strBirthday: {
                            required: 'Ngày sinh không được để trống'
                        },
                        strAddress: {
                            required: 'Địa chỉ không được để trống'
                        },
                        strPhone: {
                            required: 'Số điện thoại không được để trống'
                        }
                    },
                    submitHandler: function (form) {
                        $.ajax({
                            type: "POST",
                            url: SITE_URL + "/backend/users/ajax-add",
                            data: $("#frm-user-add").serialize(),
                            dataType: "json",
                            beforeSend: function () {
                            },
                            success: function (returnData) {
                                if(returnData.error == 0)
                                {
                                    $('#modal-add').modal('hide');
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
                                                //Fix multi modal
                                                setTimeout(function(){
                                                    var modal_count = $('.modal-dialog').length;
                                                    if(modal_count > 0)
                                                    {
                                                        $('body').addClass('modal-open');
                                                    }
                                                }, 1000);
                                            }
                                        }
                                    }
                                });
                                if (returnData.error == 0) {
                                    setTimeout(function () {
                                        location.href = SITE_URL + '/backend/users';
                                    }, 2000);
                                }
                            }
                        });
                    }
                });
            }
        });
        return false;
    });
    //Inactive user
    $('.act-update-status').on('click', function () {
        var $this = this;
        var intUserId = $($this).attr('data-id');
        var intStatus = $($this).attr('data-status');
        var strName = $($this).attr('data-name');
        var label = '';
        if (intStatus == 1) {
            intStatus = 2;
            label = 'inactive';
        } else {
            intStatus = 1;
            label = 'active';
        }
        bootbox.dialog({
            message: 'Bạn có chắc chắn muốn ' + label + ' user [' + strName + '] không?',
            title: 'Xác nhận',
            title_className: 'bg-primary',
            buttons: {
                success: {
                    label: "Yes",
                    className: "btn-primary",
                    callback: function () {
                        $.ajax({
                            type: "POST",
                            url: SITE_URL + "/backend/users/ajax-update",
                            data: {intUserId: intUserId, intStatus: intStatus, strName: strName},
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
                                            }
                                        }
                                    }
                                });
                                setTimeout(function () {
                                    location.reload();
                                }, 2000);
                            }
                        });
                    }
                },
                close: {
                    label: "Cancel",
                    className: "btn-default",
                    callback: function () {
                    }
                }
            }
        });
        return false;
    });
    //Del user
    $('.act-del').on('click', function () {
        var $this = this;
        var intUserId = $($this).attr('data-id');
        var strName = $($this).attr('data-name');
        bootbox.dialog({
            message: 'Bạn có chắc chắn muốn xóa user [' + strName + '] không?',
            title: 'Xác nhận',
            title_className: 'bg-primary',
            buttons: {
                success: {
                    label: "Yes",
                    className: "btn-primary",
                    callback: function () {
                        $.ajax({
                            type: "POST",
                            url: SITE_URL + "/backend/users/ajax-del",
                            data: {intUserId: intUserId, strName: strName},
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
                                            }
                                        }
                                    }
                                });
                                setTimeout(function () {
                                    location.reload();
                                }, 2000);
                            }
                        });
                    }
                },
                close: {
                    label: "Cancel",
                    className: "btn-default",
                    callback: function () {
                    }
                }
            }
        });
        return false;
    });
    //Reset password
    $('.act-reset').on('click', function (e) {
        e.preventDefault();
        $('#modal-reset').modal('show');
        var $this = this;
        var intUserId = $($this).attr('data-id');
        var strEmail = $($this).attr('data-email');
        var strName = $($this).attr('data-name');
        $('input[name=intUserId]').val(intUserId);
        $('#strEmail').val(strEmail);
        $('#frm-user-reset').validate({
            errorPlacement: function (error, element) {
                element = element.parents('div.form-group').length == 1 ? element.parents('div.form-group') : element;
                error.insertAfter(element);
            },
            errorElement: "div",
            rules: {
                strPassWord: {
                    required: true
                },
                strConfirmPassWord: {
                    required: true
                }
            },
            messages: {
                strPassWord: {
                    required: 'New password không được để trống'
                },
                strConfirmPassWord: {
                    required: 'Confirm new password không được để trống'
                }
            },
            submitHandler: function (form) {
                var strPassWord = $('#strPassWord').val();
                var strConfirmPassWord = $('#strConfirmPassWord').val();
                $.ajax({
                    type: "POST",
                    url: SITE_URL + "/backend/users/ajax-reset",
                    data: {intUserId: intUserId, strName: strName, strPassWord: strPassWord, strConfirmPassWord: strConfirmPassWord},
                    dataType: "json",
                    beforeSend: function () {
                    },
                    success: function (returnData) {
                        if(returnData.error == 0)
                        {
                            $('#modal-reset').modal('hide');
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
                                    }
                                }
                            }
                        });
                        if (returnData.error == 0) {
                            setTimeout(function () {
                                location.href = SITE_URL + '/backend/users';
                            }, 2000);
                        }
                    }
                });
            }
        });
        return false;
    });
});