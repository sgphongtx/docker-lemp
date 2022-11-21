/**
 * Created by phongtx on 14/04/2015.
 */
"use strict"
$(document).ready(function () {
    if($('input[name=intType]').val() == 1) {
        CKEDITOR.replace('ckeditor');
    }
    articlesAdd.checkboxall();
    //articlesAdd.uploadImage();
    articlesAdd.valid();
    articlesAdd.tags();
    $('#strDate').datetimepicker({
        //defaultDate: "05/08/1985 08:15:30",
        format: 'DD/MM/YYYY HH:mm:ss'
    });
    //Cropit
    var imageCropper = $('.image-editor');
    imageCropper.cropit();
});
var articlesAdd = {
    checkbox : function () {
        //iCheck for checkbox and radio inputs
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
        //Red color scheme for iCheck
        $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
            checkboxClass: 'icheckbox_minimal-red',
            radioClass: 'iradio_minimal-red'
        });
        //Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });
    },
    checkboxall : function () {
        $('#checkall').on('click',function(){
            if($('#checkall').prop('checked')){
                $('input[name="arrCate[]"]').prop('checked',true);
            }else{
                $('input[name="arrCate[]"]').prop('checked',false);
            }
        });
    },
    valid : function(){
        $('#add').validate({
            rules: {
                strImage: {
                    required: true
                },
                intCate: {
                    required: true
                },
                strTitle : {
                    required: true
                },
                strDesc : {
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
                strImage: {
                    required: 'Vui lòng chọn hình đại diện.'
                },
                intCate: {
                    required: 'Vui lòng chọn danh mục.'
                },
                strTitle : {
                    required: 'Vui lòng nhập title.'
                },
                strDesc : {
                    required: 'Vui lòng nhập description.'
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
                var imgCroped = $('.image-editor');
                imgCroped.cropit();
                var w = imgCroped.cropit('previewSize').width;
                var h = imgCroped.cropit('previewSize').height;
                var x = imgCroped.cropit('offset').x;
                var y = imgCroped.cropit('offset').y;
                var z = imgCroped.cropit('zoom');
                $('input[name=intImgW]').val(w);
                $('input[name=intImgH]').val(h);
                $('input[name=intCropX]').val(x);
                $('input[name=intCropY]').val(y);
                $('input[name=intZoom]').val(z);
                form.submit();
            }
        });
    },
    tags: function(){
        $('#strTags').select2({
            placeholder: "Tìm kiếm",
            minimumInputLength: 2,
            multiple: true,
            dataType: 'jsonp',
            quietMillis: 100,
            ajax: {
                url: SITE_URL + "/backend/tags/ajax-search-tags",
                type: "GET",
                dataType: 'json',
                data: function (term, page) {
                    return {q: term}
                },
                results: function (data, page) {
                    return { results: data }
                }
            }
        });
        //Load list tags select
        if(list_tags != '')
        {
            var data = [];
            $.each(list_tags, function( index, value ) {
                if($('#hd_tag_id_'+value).val() > 0){
                    data.push({id: $('#hd_tag_id_'+value).val(), text:$('#hd_tag_name_'+value).val()});
                }
            });
        }
        $("#strTags").select2("data",data);
        //End load list user select
    }
};