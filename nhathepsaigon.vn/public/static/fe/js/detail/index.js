/**
 * Created by phongtx on 19/05/2016.
 */
$(document).ready(function () {
    detail.commentClick();
    //detail.lazyLoad();
    ArticleShare.init();
});
var detail = {
    lazyLoad: function(){
        if($("img.lazy").length > 0){
            $("img.lazy").lazyload({
                effect : "fadeIn"
            });
        }
    },
    printPopup: function () {
        var urlRight = window.location.href;
        urlRight = urlRight.replace('/tin-tuc/', '/print/');
        window.open(urlRight, "_blank", "left=300,top=0,width=550,height=600,toolbar=0,scrollbars=1,status=0");
    },
    commentClick: function () {
        $('#comment_post_button').on('click', function (e) {
            e.preventDefault();
            var strContent = $('#txtComment').val();
            if(strContent == '' || strContent == 'Ý kiến của bạn'){
                bootbox.dialog({
                    message: 'Bạn chưa nhập nội dung bình luận.',
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
            }else{
                $('#modal-comment').modal('show');
                // set focus
                $('#modal-comment input[name=strEmail]').focus();
                detail.loadCaptcha();
                $('#loadCaptcha').on('click',function(){
                    detail.loadCaptcha();
                });
                $("#frm-comment-add").on('click', 'button[type=submit]', function (e) {
                    $.validator.addMethod("isemail", function (value, element) {
                        return /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/.test(value);
                    });
                    $('#frm-comment-add').validate({
                        ignore: [],
                        errorPlacement: function (error, element) {
                            element = element.parents('div.form-group').length == 1 ? element.parents('div.form-group') : element;
                            error.insertAfter(element);
                        },
                        errorElement: "div",
                        rules: {
                            strEmail: {
                                required: true,
                                isemail: true,
                                maxlength: 100,
                                minlength: 6
                            },
                            strName: {
                                required: true
                            },
                            strCode: {
                                required: true
                            }
                        },
                        messages: {
                            strEmail: {
                                required: 'Hãy nhập email',
                                isemail: 'Email không hợp lệ',
                                maxlength: 'Chiều dài tối đa của email là 100 ký tự',
                                minlength: 'Chiều dài tối thiểu của email là 6 ký tự'
                            },
                            strName: {
                                required: 'Hãy nhập họ và tên'
                            },
                            strCode: {
                                required: 'Hãy nhập mã xác nhận'
                            }
                        },
                        submitHandler: function (form) {
                            var intArticleId = parseInt($('input[name=intArticleId]').val());
                            var strName = $('input[name=strName]').val();
                            var strEmail = $('input[name=strEmail]').val();
                            var strCode = $('input[name=strCode]').val();
                            var captchaID = $('input[name=captchaID]').val();
                            $.ajax({
                                type: "POST",
                                url: SITE_URL + "/detail/ajax-add-comment",
                                data: {intArticleId:intArticleId, strName:strName, strEmail:strEmail, strContent:strContent, strCode:strCode, captchaID:captchaID},
                                dataType: "json",
                                beforeSend: function () {
                                },
                                success: function (returnData) {
                                    if (returnData.error == 2) {
                                        detail.loadCaptcha();
                                    }
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
                                        $('#modal-comment').modal('hide');
                                        setTimeout(function () {
                                            location.reload();
                                        }, 2000);
                                    }
                                }
                            });
                            return false;
                        }
                    });
                });
            }
        });
    },
    loadCaptcha: function() {
        $.ajax({
            type: 'GET',
            url: SITE_URL + '/captcha/show',
            dataType: 'json',
            beforeSend: function() {
            },
            success: function(response) {
                if (response != false) {
                    $('#showCaptcha').html(response.html);
                }
            }
        });
    }
}
/**
 * ArticleShare feature
 */
var ArticleShare = {
    init: function() {
        this.renderSocialPlugins();
        this.SocialButtonLike();
    },
    urlEncode: function(str) {
        // %        note 1: This reflects PHP 5.3/6.0+ behavior
        // %        note 2: Please be aware that this function expects to encode into UTF-8 encoded strings, as found on
        // %        note 2: pages served as UTF-8
        // *     example 1: urlencode('Kevin van Zonneveld!');
        // *     returns 1: 'Kevin+van+Zonneveld%21'
        // *     example 2: urlencode('http://kevin.vanzonneveld.net/');
        // *     returns 2: 'http%3A%2F%2Fkevin.vanzonneveld.net%2F'
        // *     example 3: urlencode('http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a');
        // *     returns 3: 'http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a'
        str = (str + '').toString();
        // Tilde should be allowed unescaped in future versions of PHP (as reflected below), but if you want to reflect current
        // PHP behavior, you would need to add ".replace(/~/g, '%7E');" to the following.
        return encodeURIComponent(str).replace(/#!/g,'%23').replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
    },
    // setup link for social
    renderSocialPlugins: function() {
        var article = {
            url: typeof(article_url) != 'undefined' ? article_url : window.location.href.replace(window.location.hash, ''),
            title: typeof(article_title) != 'undefined' ? article_title : '',
            description: typeof(article_description) != 'undefined' ? article_description : '',
            image: typeof(article_image) != 'undefined' ? article_image : ''
        };
        //facebook
        $('a.btn_facebook').click(function(e) {
            var url = 'https://www.facebook.com/sharer/sharer.php?u=' + ArticleShare.urlEncode(article.url) + '&p[images][0]=' + article.image;
            var newwindow = window.open(url, '_blank', 'menubar=no,toolbar=no,resizable=no,scrollbars=no,height=450,width=710');
            if (window.focus) {
                newwindow.focus();
            }
            e.preventDefault();
        });

        //google+
        $('a.btn_google').click(function(e) {
            var url = 'https://plus.google.com/share?url=' + ArticleShare.urlEncode(article.url);
            var newwindow = window.open(url, '_blank', 'menubar=no,toolbar=no,resizable=no,scrollbars=no,height=450,width=520');
            if (window.focus) {
                newwindow.focus();
            }
            e.preventDefault();
        });
        //twitter
        $('a.btn_twitter').click(function(e) {
            var url = 'https://twitter.com/intent/tweet?source=webclient&text=' + article.title + '+' + ArticleShare.urlEncode(article.url);
            var newwindow = window.open(url, '_blank', 'menubar=no,toolbar=no,resizable=no,scrollbars=no,height=450,width=710');
            if (window.focus) {
                newwindow.focus();
            }
            e.preventDefault();
        });
    },
    SocialButtonLike: function(){
        var article = {
            url: typeof(article_url) != 'undefined' ? article_url : window.location.href.replace(window.location.hash, ''),
            title: typeof(article_title) != 'undefined' ? article_title : '',
            description: typeof(article_description) != 'undefined' ? article_description : '',
            image: typeof(article_image) != 'undefined' ? article_image : ''
        };
        html = '<div class="item_social social_fb hidden_320" style="overflow: hidden;"><div class="fb-like" data-send="false" data-layout="button_count" data-width="450" data-show-faces="true"  data-href="' + article.url + '"></div></div>';
        html += '<div class="item_social social_fb hidden_320" style="overflow: hidden;"><div class="fb-share-button" data-href="' + article.url + '" data-layout="button_count" data-size="small" data-mobile-iframe="true"><a class="fb-xfbml-parse-ignore" target="_blank" href="' + article.url + '&amp;src=sdkpreparse">Chia sẻ</a></div></div>';
        html += '<div class="item_social social_fb hidden_320 fb-send" data-href="' + article.url + '"></div>';
        html += '<div class="item_social social_twitter hidden_320"><a href="https://twitter.com/share" class="twitter-share-button" data-url="' + article.url + '">Tweet</a></div>';
        html += '<div class="item_social social_plus hidden_320"><div class="g-plusone" data-size="medium" data-href="' + article.url + '"></div></div>';
        $("#social_like").append(html);
        ////// FACEBOOK //////////
        var image = new Image();
        image.src = "http://www.facebook.com/favicon.ico";
        image.onload = function(){
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
            $(".social_fb").css("overflow","visible");
        };
        // google
        window.___gcfg = {lang: 'vi'};
        (function() {
            var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
            po.src = 'https://apis.google.com/js/plusone.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
        })();
        // update like tweet
        !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
    }
};
/*End share link FB,G+,Twitter*/