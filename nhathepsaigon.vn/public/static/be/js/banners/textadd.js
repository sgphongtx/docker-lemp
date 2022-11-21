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
        var intId = $(this).attr('data-id');
        $.ajax({
            type: "POST",
            url: SITE_URL + '/backend/banners/ajax-get-detail-text-banner',
            data: {intId:intId},
            dataType: "json",
            beforeSend: function () {
            },
            success: function (returnData) {
                $('#modal-add .modal-content').html(returnData.html);
            }
        });
        return false;
    });
    $("#modal-add").on('click', 'button[type=submit]', function (e) {
        $('#frm-banner-add').validate({
            errorPlacement: function (error, element) {
                element = element.parents('div.form-group').length == 1 ? element.parents('div.form-group') : element;
                error.insertAfter(element);
            },
            errorElement: "div",
            rules: {
                strImage: {
                    required: {
                        depends: function () {
                            if(parseInt($('input[name=intId]').val()) == 0){
                                return true;
                            }else{
                                return false;
                            }
                        }
                    }
                },
                strTitle: {
                    required: true
                },
                strShareUrl: {
                    required: true
                }
            },
            messages: {
                strImage: {
                    required: 'Vui lòng chọn hình đại diện.'
                },
                strTitle: {
                    required: 'Tiêu đề không được để trống'
                },
                strShareUrl: {
                    required: 'Link không được để trống'
                }
            }
            /*submitHandler: function (form) {
                $.ajax({
                    type: "POST",
                    url: SITE_URL + "/backend/banners/ajax-add-text-banner",
                    data: $("#frm-banner-add").serialize(),
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
                            $('#modal-add').modal('hide');
                            setTimeout(function () {
                                location.href = SITE_URL + '/backend/banners/text';
                            }, 2000);
                        }
                    }
                });
                return false;
            }*/
        });
    });
    //Inactive banner
    $('.act-update-status').on('click', function () {
        var $this = this;
        var intId = $($this).attr('data-id');
        var intStatus = $($this).attr('data-status');
        var strTitle = $($this).attr('data-title');
        var label = '';
        if (intStatus == 1) {
            intStatus = 2;
            label = 'inactive';
        } else {
            intStatus = 1;
            label = 'active';
        }
        bootbox.dialog({
            message: 'Bạn có chắc chắn muốn ' + label + ' bài viết [' + strTitle + '] không?',
            title: 'Xác nhận',
            title_className: 'bg-primary',
            buttons: {
                success: {
                    label: "Yes",
                    className: "btn-primary",
                    callback: function () {
                        $.ajax({
                            type: "POST",
                            url: SITE_URL + "/backend/banners/ajax-update-text-banner",
                            data: {intId: intId, intStatus: intStatus, strTitle: strTitle},
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
    //Del banner
    $('.act-del').on('click', function () {
        var $this = this;
        var intId = $($this).attr('data-id');
        var strTitle = $($this).attr('data-title');
        bootbox.dialog({
            message: 'Bạn có chắc chắn muốn xóa bài viết [' + strTitle + '] không?',
            title: 'Xác nhận',
            title_className: 'bg-primary',
            buttons: {
                success: {
                    label: "Yes",
                    className: "btn-primary",
                    callback: function () {
                        $.ajax({
                            type: "POST",
                            url: SITE_URL + "/backend/banners/ajax-del-text-banner",
                            data: {intId: intId, strTitle: strTitle},
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
});