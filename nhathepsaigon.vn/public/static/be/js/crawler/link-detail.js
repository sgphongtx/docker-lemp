/**
 * @name        :   crawler.js
 * @author      :   PhongTX
 * @copyright   :   Fpt Online
 * Date: 10/04/2015
 * Time: 08:45
 */
$(document).ready(function () {
    $("#crawler").on('click', 'button[type=submit]', function (e) {
        $('#crawler').validate({
            rules: {
                strLink: {
                    required: true
                }
            },
            messages: {
                strLink: {
                    required: 'Hãy nhập link crawler data. (VD: http://giaitri.vnexpress.net/photo/sao-dep-sao-xau/tu-anh-khoe-chan-voi-dam-ngan-xuyen-thau-3490550.html)'
                }
            },
            submitHandler: function (form) {
                var strLink = $('#strLink').val();
                bootbox.confirm("Bạn muốn crawler data từ ["+strLink+"]?", function (result) {
                    if (result == true) {
                        location.href = SITE_URL + '/backend/articles/add?link=' + encodeURIComponent(strLink);
                    }
                });
                return false;
            }
        });
    });
});