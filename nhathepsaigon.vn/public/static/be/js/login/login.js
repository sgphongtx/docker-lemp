/**
 * @name        :   login.js
 * @author      :   PhongTX
 * @copyright   :   Fpt Online
 * Date: 10/04/2015
 * Time: 08:45
 */
$(document).ready(function () {
    // set focus
    $('#email').focus();
    // validate
    $('#form-login').validate({
        errorPlacement: function (error, element) {
            element = element.parents('div.inputgroup').length == 1 ? element.parents('div.inputgroup') : element;
            error.insertAfter(element);
        },
        errorElement: "div",
        rules: {
            email: {
                required: true,
                email: true,
                maxlength: 100
            },
            password: {
                required: true,
                maxlength: 30,
                minlength: 6
            }
        },
        messages: {
            email: {
                required: 'Nhập vào địa chỉ email.',
                email: 'Địa chỉ email không hợp lệ.',
                maxlength: 'Chiều dài tối đa của email là 100 ký tự.'
            },
            password: {
                required: 'Nhập vào password.',
                maxlength: 'Chiều dài tối đa 30 ký tự.',
                minlength: 'Chiều dài tối thiểu 6 ký tự.'
            }
        }
    });
});


