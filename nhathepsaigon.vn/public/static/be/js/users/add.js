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
                            required: 'Email kh??ng ???????c ????? tr???ng',
                            email: 'Email kh??ng ????ng ?????nh d???ng'
                        },
                        intGroupId: {
                            required: 'Vui l??ng ch???n nh??m quy???n'
                        },
                        strPassWord: {
                            required: 'M???t kh???u kh??ng ???????c ????? tr???ng'
                        },
                        strConfirmPassWord: {
                            required: 'X??c nh???n m???t kh???u kh??ng ???????c ????? tr???ng'
                        },
                        strFullname: {
                            required: 'H??? t??n kh??ng ???????c ????? tr???ng'
                        },
                        strBirthday: {
                            required: 'Ng??y sinh kh??ng ???????c ????? tr???ng'
                        },
                        strAddress: {
                            required: '?????a ch??? kh??ng ???????c ????? tr???ng'
                        },
                        strPhone: {
                            required: 'S??? ??i???n tho???i kh??ng ???????c ????? tr???ng'
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
                                    title: 'Th??ng b??o',
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
            message: 'B???n c?? ch???c ch???n mu???n ' + label + ' user [' + strName + '] kh??ng?',
            title: 'X??c nh???n',
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
                                    title: 'Th??ng b??o',
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
            message: 'B???n c?? ch???c ch???n mu???n x??a user [' + strName + '] kh??ng?',
            title: 'X??c nh???n',
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
                                    title: 'Th??ng b??o',
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
                    required: 'New password kh??ng ???????c ????? tr???ng'
                },
                strConfirmPassWord: {
                    required: 'Confirm new password kh??ng ???????c ????? tr???ng'
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
                            title: 'Th??ng b??o',
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