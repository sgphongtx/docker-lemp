/**
 * Created by phongtx on 15/05/2016.
 */
$(document).ready(function () {
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var checkin = $('#form_date').datepicker({
        format: 'dd/mm/yyyy',
        onRender: function (date) {
            return date.valueOf() > now.valueOf() ? 'disabled' : '';
        }
    }).on('changeDate',function (ev) {
        var newDate = new Date(ev.date.valueOf() < checkout.date.valueOf() ? checkout.date : ev.date)
        newDate.setDate(newDate.getDate());
        checkout.setValue(newDate);
        checkin.hide();
        $('#to_date')[0].focus();
    }).data('datepicker');
    var checkout = $('#to_date').datepicker({
        format: 'dd/mm/yyyy',
        onRender: function (date) {
            return date.valueOf() < checkin.date.valueOf() || date.valueOf() > now.valueOf() ? 'disabled' : '';
        }
    }).on('changeDate',function (ev) {
        checkout.hide();
    }).data('datepicker');
    //update status
    $('.act-update-status').on('click', function () {
        var $this = this;
        var intId = $($this).attr('data-id');
        var intStatus = $($this).attr('data-status');
        var strName = $($this).attr('data-name');
        var label = '';
        if (intStatus == 1) {
            label = 'duyệt';
        }
        if (intStatus == 3) {
            label = 'từ chối duyệt';
        }
        bootbox.dialog({
            message: 'Bạn có chắc chắn muốn ' + label + ' bài viết [' + strName + '] không?',
            title: 'Xác nhận',
            title_className: 'bg-primary',
            buttons: {
                success: {
                    label: "Yes",
                    className: "btn-primary",
                    callback: function () {
                        $.ajax({
                            type: "POST",
                            url: SITE_URL + "/backend/articles/ajax-update",
                            data: {intId: intId, intStatus: intStatus, strName: strName},
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
    //Del tag
    $('.act-del').on('click', function () {
        var $this = this;
        var intId = $($this).attr('data-id');
        var strName = $($this).attr('data-name');
        bootbox.dialog({
            message: 'Bạn có chắc chắn muốn xóa bài viết [' + strName + '] không?',
            title: 'Xác nhận',
            title_className: 'bg-primary',
            buttons: {
                success: {
                    label: "Yes",
                    className: "btn-primary",
                    callback: function () {
                        $.ajax({
                            type: "POST",
                            url: SITE_URL + "/backend/articles/ajax-update",
                            data: {intId: intId, strName: strName, intStatus: 0},
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
    /*$('#userid').select2({
        placeholder: "Tìm kiếm",
        minimumInputLength: 2,
        multiple: false,
        dataType: 'jsonp',
        quietMillis: 100,
        ajax: {
            url: SITE_URL + "/backend/users/ajax-search-user-by-email",
            type: "GET",
            dataType: 'json',
            data: function (term, page) {
                return {q: term}
            },
            results: function (data, page) {
                return { results: data }
            }
        }
    });*/
});