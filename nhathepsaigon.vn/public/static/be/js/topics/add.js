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
        var intTopicId = $(this).attr('data-topic-id');
        $.ajax({
            type: "POST",
            url: SITE_URL + '/backend/topics/ajax-get-detail-topic',
            data: {intTopicId:intTopicId},
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
        $('#frm-topic-add').validate({
            errorPlacement: function (error, element) {
                element = element.parents('div.form-group').length == 1 ? element.parents('div.form-group') : element;
                error.insertAfter(element);
            },
            errorElement: "div",
            rules: {
                strTopicName: {
                    required: true
                }/*,
                 intCategoryId: {
                 required: {
                 depends: function () {
                 if ($("select[name=intCategory]:selected").val() != 0) {
                 return true;
                 }
                 else {
                 return false;
                 }
                 }
                 }
                 }*/
            },
            messages: {
                strTopicName: {
                    required: 'Topic name kh??ng ???????c ????? tr???ng'
                }/*,
                 intCategoryId: {
                 required: 'Category name kh??ng ???????c ????? tr???ng'
                 }*/
            },
            submitHandler: function (form) {
                $.ajax({
                    type: "POST",
                    url: SITE_URL + "/backend/topics/ajax-add",
                    data: $("#frm-topic-add").serialize(),
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
                                location.href = SITE_URL + '/backend/topics';
                            }, 2000);
                        }
                    }
                });
                return false;
            }
        });
    });
    //Inactive topic
    $('.act-update-status').on('click', function () {
        var $this = this;
        var intTopicId = $($this).attr('data-id');
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
            message: 'B???n c?? ch???c ch???n mu???n ' + label + ' topic [' + strName + '] kh??ng?',
            title: 'X??c nh???n',
            title_className: 'bg-primary',
            buttons: {
                success: {
                    label: "Yes",
                    className: "btn-primary",
                    callback: function () {
                        $.ajax({
                            type: "POST",
                            url: SITE_URL + "/backend/topics/ajax-update",
                            data: {intTopicId: intTopicId, intStatus: intStatus, strName: strName},
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
    //Del topic
    $('.act-del').on('click', function () {
        var $this = this;
        var intTopicId = $($this).attr('data-id');
        var strName = $($this).attr('data-name');
        bootbox.dialog({
            message: 'B???n c?? ch???c ch???n mu???n x??a topic [' + strName + '] kh??ng?',
            title: 'X??c nh???n',
            title_className: 'bg-primary',
            buttons: {
                success: {
                    label: "Yes",
                    className: "btn-primary",
                    callback: function () {
                        $.ajax({
                            type: "POST",
                            url: SITE_URL + "/backend/topics/ajax-del",
                            data: {intTopicId: intTopicId, strName: strName},
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
});