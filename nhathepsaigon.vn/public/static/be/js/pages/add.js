/**
 * Created by phongtx on 14/04/2015.
 */
"use strict"
$(document).ready(function () {
    CKEDITOR.replace('ckeditor');
    pagesAdd.valid();
    $('#strDate').datetimepicker({
        //defaultDate: "05/08/1985 08:15:30",
        format: 'DD/MM/YYYY HH:mm:ss'
    });
});
var pagesAdd = {
    valid : function(){
        $('#add').validate({
            rules: {
                /*strPageKey: {
                    required: true
                },*/
                strName : {
                    required: true
                },
                strMetaTitle : {
                    required: true
                },
                strMetaDesc : {
                    required: true
                },
                strMetaKeyword : {
                    required: true
                },
                strDate : {
                    required: true
                }
            },
            messages: {
                /*strPageKey: {
                    required: 'Vui lòng nhập key page.'
                },*/
                strName : {
                    required: 'Vui lòng nhập name.'
                },
                strMetaTitle : {
                    required: 'Vui lòng nhập meta title.'
                },
                strMetaDesc : {
                    required: 'Vui lòng nhập meta description.'
                },
                strMetaKeyword : {
                    required: 'Vui lòng nhập meta keyword.'
                },
                strDate : {
                    required: 'Vui lòng nhập ngày đăng.'
                }
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
    }
};