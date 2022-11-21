/**
 * Created by phongtx on 06/06/2016.
 */
$(document).ready(function () {
    $("#import").on('click', 'button[type=submit]', function (e) {
        $.ajax({
            type: "POST",
            url: SITE_URL + "/backend/crawler/ajax-import",
            data: {},
            dataType: "json",
            beforeSend: function () {
                $('#modal-loading').modal('show');
            },
            success: function (returnData) {
                $('#modal-loading').modal('hide');
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
                                setTimeout(function () {
                                    var modal_count = $('.modal-dialog').length;
                                    if (modal_count > 0) {
                                        $('body').addClass('modal-open');
                                    }
                                }, 1000);
                            }
                        }
                    }
                });
                if (returnData.error == 0) {
                    setTimeout(function () {
                        location.href = SITE_URL + '/backend/articles/index/status/3';
                    }, 2000);
                }
            }
        });
        return false;
    });
});