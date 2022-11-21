/**
 * @name        :   index.js
 * @author      :   PhongTX
 * @copyright   :   Fpt Online
 * Date: 10/04/2015
 * Time: 08:45
 */
$(document).ready(function () {
    //jQuery UI sortable for the todo list
    $(".todo-list").sortable({
        placeholder: "sort-highlight",
        handle: ".handle",
        forcePlaceholderSize: true,
        zIndex: 999999,
        update: function (event, ui) {
            var data = $('.todo-list').sortable('serialize');
            var intCategoryId = parseInt($('select[name="intCategoryId"] option:selected').val());
            console.log(data);
            $.ajax({
                type: "POST",
                url: SITE_URL + '/backend/index/ajax-update',
                data: data,
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
                    if (returnData.error == 0) {
                        setTimeout(function () {
                            location.href = SITE_URL + '/backend/index/index?intCategoryId='+intCategoryId;
                        }, 2000);
                    }
                }
            });
            return false;
        }
    });
    $('.act-add').on('click', function (e) {
        e.preventDefault();
        $('#modal-add').modal('show');
        var total = parseInt($(this).attr('data-total'));
        var strId = $('input[name="strId"]').val();
        var intCategoryId = parseInt($('select[name="intCategoryId"] option:selected').val());
        $.ajax({
            type: "POST",
            url: SITE_URL + '/backend/index/ajax-get-list-articles',
            data: {total:total,strId:strId,intCategoryId:intCategoryId},
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
        var total = parseInt($('input[name="intTotal"]').val());
        var intCategoryId = parseInt($('select[name="intCategoryId"] option:selected').val());
        $.validator.addMethod("checktotal", function (value, element) {
            var totalCheck = $('input[name="articleid[]"]:checked').length;
            if (totalCheck + total > 15) {
                return false;
            }
            else {
                return true;
            }
        });
        $('#frm-topstory-add').validate({
            errorPlacement: function (error, element) {
                element = element.parents('div.form-group').length == 1 ? element.parents('div.form-group') : element;
                error.insertAfter(element);
            },
            rules: {
                'articleid[]': {
                    required: true,
                    checktotal: true
                }
            },
            messages: {
                'articleid[]': {
                    required: 'Hãy chọn bài viết cần thêm vào topstory',
                    checktotal: 'Số lượng bài viết được chọn phải <= 15'
                }
            },
            submitHandler: function (form) {
                $.ajax({
                    type: "POST",
                    url: SITE_URL + "/backend/index/ajax-add-topstory",
                    data: $("#frm-topstory-add").serialize(),
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
                                location.href = SITE_URL + '/backend/index/index?intCategoryId='+intCategoryId;
                            }, 2000);
                        }
                    }
                });
                return false;
            }
        });
    });
    //Del tag
    $('.act-del').on('click', function () {
        var $this = this;
        var intId = $($this).attr('data-id');
        var intCategoryId = parseInt($('select[name="intCategoryId"] option:selected').val());
        var strName = $($this).attr('data-name');
        bootbox.dialog({
            message: 'Bạn có chắc chắn muốn xóa bài viết [' + strName + '] khỏi danh sách topstory không?',
            title: 'Xác nhận',
            title_className: 'bg-primary',
            buttons: {
                success: {
                    label: "Yes",
                    className: "btn-primary",
                    callback: function () {
                        $.ajax({
                            type: "POST",
                            url: SITE_URL + "/backend/index/ajax-del",
                            data: {intId: intId, intCategoryId:intCategoryId, strName: strName},
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