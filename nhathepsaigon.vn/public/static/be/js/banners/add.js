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
        var intPositionId = $(this).attr('data-position-id');
        var intCategoryId = $(this).attr('data-category-id');
        $.ajax({
            type: "POST",
            url: SITE_URL + '/backend/banners/ajax-get-detail-banner',
            data: {intPositionId:intPositionId, intCategoryId:intCategoryId},
            dataType: "json",
            beforeSend: function () {
            },
            success: function (returnData) {
                $('#modal-add .modal-content').html(returnData.html);
                //$(".select2").select2();
                $('.modal-dialog').css('width','700px');
                CKEDITOR.replace('strContent',{
                    width :'100%',
                    height: '300px'
                });
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
                strName: {
                    required: true
                }
            },
            messages: {
                strName: {
                    required: 'Tên quảng cáo không được để trống'
                }
            },
            submitHandler: function (form) {
                var intCategoryId = parseInt($('select[name="intCategoryId"] option:selected').val());
                $.ajax({
                    type: "POST",
                    url: SITE_URL + "/backend/banners/ajax-add",
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
                                location.href = SITE_URL + '/backend/banners/index?categoryid='+returnData.intCategoryId;
                            }, 2000);
                        }
                    }
                });
                return false;
            }
        });
    });
    //Inactive banner
    $('.act-update-status').on('click', function () {
        var $this = this;
        var intPositionId = $($this).attr('data-position-id');
        var intCategoryId = $($this).attr('data-category-id');
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
            message: 'Bạn có chắc chắn muốn ' + label + ' quảng cáo [' + strName + '] không?',
            title: 'Xác nhận',
            title_className: 'bg-primary',
            buttons: {
                success: {
                    label: "Yes",
                    className: "btn-primary",
                    callback: function () {
                        $.ajax({
                            type: "POST",
                            url: SITE_URL + "/backend/banners/ajax-update",
                            data: {intPositionId: intPositionId, intCategoryId: intCategoryId, intStatus: intStatus, strName: strName},
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
        var intPositionId = $($this).attr('data-position-id');
        var intCategoryId = $($this).attr('data-category-id');
        var strName = $($this).attr('data-name');
        bootbox.dialog({
            message: 'Bạn có chắc chắn muốn xóa vị trí quảng cáo [' + strName + '] không?',
            title: 'Xác nhận',
            title_className: 'bg-primary',
            buttons: {
                success: {
                    label: "Yes",
                    className: "btn-primary",
                    callback: function () {
                        $.ajax({
                            type: "POST",
                            url: SITE_URL + "/backend/banners/ajax-del",
                            data: {intPositionId: intPositionId, intCategoryId: intCategoryId, strName: strName},
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