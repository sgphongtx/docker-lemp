/**
 * Created by phongtx on 15/04/2015.
 */
$(document).ready(function () {
    $('.act-add').on('click', function (e) {
        e.preventDefault();
        $('#modal-add').modal('show');
        var intCategoryId = $(this).attr('data-id');
        var intCategoryType = $(this).attr('data-type');
        $.ajax({
            type: "POST",
            url: SITE_URL + '/backend/categories/ajax-get-detail-category',
            data: {intCategoryId: intCategoryId, intCategoryType: intCategoryType},
            dataType: "json",
            beforeSend: function () {
            },
            success: function (returnData) {
                $('#modal-add .modal-content').html(returnData.html);
                //block selectbox
                var $select = $('.site_id_list .selected_data'),
                    $items_select = $('.site_id_list ul li a');
                $('.site_id_list ul li').each(function(){
                    if($(this).hasClass('selected_data')){
                        $select.find('span').text($(this).text());
                        if($(this).parents('li').hasClass('has_sub_menu')){
                            $(this).parent('ul').show();
                        }
                        return false;
                    }
                });
                $select.unbind('click').on('click', function(event){
                    event.stopPropagation();
                    $('.site_id_list ul').hide();
                    if($(this).hasClass('clicked')){
                        $(this).parent().find('ul').hide();
                        $select.removeClass('clicked');
                    }else{
                        $(this).parent().find('ul.list_menu').show();
                        $(this).addClass('clicked');
                    }
                });
                $items_select.unbind('click').on('click', function(event){
                    event.stopPropagation();
                    var $this = $(this);
                    $('.site_id_list ul li.has_sub_menu span.colspan').removeClass('show_sub_folder');
                    $('.site_id_list ul li').removeClass('selected');
                    $this.parent('li').addClass('selected');
                    $this.parents('.site_id_list').find($select).find('span').text($this.text());
                    $this.parents('.site_id_list').find($select).attr('data-value',$this.attr('data-value'));
                    $('.selected_data_hidden').val($this.attr('data-value'));
                    $select.removeClass('clicked');
                    $('.site_id_list ul').hide();
                    //end
                    return false;
                });
                $('.site_id_list ul li.has_sub_menu span.colspan').unbind('click').on('click', function(event){
                    event.stopPropagation();
                    if($(this).hasClass('show_sub_folder')){
                        $(this).parent().find('ul').hide();
                        $(this).removeClass('show_sub_folder');
                        if($(this).parent().find('.colspan').hasClass('show_sub_folder')){
                            $(this).parent().find('ul').hide();
                            $(this).parent().find('.colspan').removeClass('show_sub_folder');
                        }
                    }else{
                        $(this).parent().find('> ul').show();
                        $(this).addClass('show_sub_folder');
                    }
                });
            }
        });
        return false;
    });
    $("#modal-add").on('click', 'button[type=submit]', function (e) {
        $('#frm-cate-add').validate({
            errorPlacement: function (error, element) {
                element = element.parents('div.form-group').length == 1 ? element.parents('div.form-group') : element;
                error.insertAfter(element);
            },
            errorElement: "div",
            rules: {
                strCateName: {
                    required: true
                },
                strCateLink: {
                    required: true
                },
                strMetaTitle: {
                    required: true
                },
                strMetaDesc: {
                    required: true
                },
                strMetaKeyword: {
                    required: true
                }
            },
            messages: {
                strCateName: {
                    required: 'Name không được để trống'
                },
                strCateLink: {
                    required: 'Slug không được để trống'
                },
                strMetaTitle: {
                    required: 'Meta title không được để trống'
                },
                strMetaDesc: {
                    required: 'Meta description không được để trống'
                },
                strMetaKeyword: {
                    required: 'Meta keyword không được để trống'
                }
            },
            submitHandler: function (form) {
                $.ajax({
                    type: "POST",
                    url: SITE_URL + "/backend/categories/ajax-add",
                    data: $("#frm-cate-add").serialize(),
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
                                location.href = SITE_URL + '/backend/categories/index/type/'+returnData.intCategoryType;
                            }, 2000);
                        }
                    }
                });
                return false;
            }
        });
    });
    //Inactive cate
    $('.act-update-status').on('click', function () {
        var $this = this;
        var intCateId = $($this).attr('data-id');
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
            message: 'Bạn có chắc chắn muốn ' + label + ' category [' + strName + '] không?',
            title: 'Xác nhận',
            title_className: 'bg-primary',
            buttons: {
                success: {
                    label: "Yes",
                    className: "btn-primary",
                    callback: function () {
                        $.ajax({
                            type: "POST",
                            url: SITE_URL + "/backend/categories/ajax-update",
                            data: {intCateId: intCateId, intStatus: intStatus, strName: strName},
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
    //Del cate
    $('.act-del').on('click', function () {
        var $this = this;
        var intCateId = $($this).attr('data-id');
        var strName = $($this).attr('data-name');
        bootbox.dialog({
            message: 'Bạn có chắc chắn muốn xóa category [' + strName + '] không?',
            title: 'Xác nhận',
            title_className: 'bg-primary',
            buttons: {
                success: {
                    label: "Yes",
                    className: "btn-primary",
                    callback: function () {
                        $.ajax({
                            type: "POST",
                            url: SITE_URL + "/backend/categories/ajax-del",
                            data: {intCateId: intCateId, strName: strName},
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