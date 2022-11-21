<?php
/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 08/04/2015
 * Time: 12:16 SA
 */
class Core_Email
{
    /**
     * @param $p_param
     * @throws Exception
     */
    public static function send($p_param)
    {
        $arrConfig = Core_Global::getConfig('application');
        if (isset($arrConfig['mail']['smtp'])) {
            $arrConfig = $arrConfig['mail']['smtp'];
        } else {
            return false;
        }
        // valid data
        if (empty($p_param['e_template']) || empty($p_param['email'])) {
            return false;
        }
        $arrMailtempl = self::_getTemplate($p_param['e_template']);
        if (empty($arrMailtempl)) {
            return false;
        }

        foreach ($p_param as $key => $data) {
            $arrMailtempl['title'] = str_replace("#$key#", $data, $arrMailtempl['title']);
            $arrMailtempl['content'] = str_replace("#$key#", $data, $arrMailtempl['content']);
        }
        // send mail
        if (isset($arrConfig['receiver']['email']) && $arrConfig['receiver']['email']) {
            $to = trim($arrConfig['receiver']['email']);
        } else {
            if($p_param['e_template'] == 'ads_contact'){
                $to = $arrConfig['sender']['email'];
            }else{
                $to = trim($p_param['email']);
            }
        }
        // unset param
        unset($p_param['e_template']);
        try {
            $transport = new Zend_Mail_Transport_Smtp($arrConfig['server'], $arrConfig['config']);
            //init zend mail
            $mail = new Zend_Mail('UTF-8');
            //set from
            $mail->setFrom($arrConfig['sender']['email'], $arrConfig['sender']['name']);
            //receivers
            $mail->addTo($to, '');
            if(isset($p_param['cc']) && !empty($p_param['cc'])){
                $mail->addCc($p_param['cc']);
            }
            //set content mail
            $mail->setSubject($arrMailtempl['title']);
            $fileAt = STATIC_URL.'/fe/images/logo.png';
            // attach file
            $pathfile = '/home/www/nhathepsaigon.vn/public/static/fe/images/logo.png';
            $at = new Zend_Mime_Part(file_get_contents($pathfile));
            $at->id = 'cid_' . md5($fileAt);
            //$at->id = $fileAt;
            $at->type = 'image/png';
            $at->filename = basename($pathfile);
            $at->disposition = Zend_Mime::DISPOSITION_INLINE;
            $at->encoding = Zend_Mime::ENCODING_BASE64;
            $arrMailtempl['content'] = str_replace("#ecl_footer_logo#", $at->id, $arrMailtempl['content']);
            // set body
            $mail->setBodyHtml($arrMailtempl['content']);
            $mail->addAttachment($at);
            //send mail
            $mail->send($transport);
            return true;
        } catch (exception $ex) {
            Core_Global::sendLog($ex->getMessage());
        }
    }

    /**
     * @param $strTempl
     * @return bool
     */
    private static function _getTemplate($strTempl)
    {
        $arrTempl = array(
            'register' => array(
                'title' => 'Nhà Thép Sài Gòn - Yêu cầu kích hoạt tài khoản',
                'content' => '<table style="width: 700px; position: relative; display: block; margin: 0 auto;"><tr><td>Chào <strong>#fullname#</strong> </td></tr>
                <tr><td>Cảm ơn bạn đã đăng ký thành viên tại <a href="'.SITE_URL.'" target="_blank">'.SITE_URL.'</a></td></tr>
                <tr><td>Để hoàn tất đăng ký, bạn vui lòng kích hoạt tài khoản bằng cách nhấn vào đường link sau: <a href="#link#" target="_blank">#link#</a>.</td></tr>
                <tr><td>Bạn cũng có thể nhập đường dẫn trên vào trình duyệt để kích hoạt tài khoản.</td></tr>
                <tr><td>Thông tin đăng nhập của bạn như sau:</a></td></tr>
                <tr><td>Email: #email#</a></td></tr>
                <tr><td>Mật khẩu: #password#</a></td></tr></table>'
            ),
            'verify-email-register' => array(
                'title' => 'Nhà Thép Sài Gòn - Tài khoản của bạn đã được kích hoạt thành công',
                'content' => '<table style="width: 700px; position: relative; display: block; margin: 0 auto;"><tr><td>Chào <strong>#fullname#</strong> </td></tr>
                <tr><td>Chúc mừng bạn đã kích hoạt tài khoản thành công tại <a href="'.SITE_URL.'" target="_blank">'.SITE_URL.'</a>.</td></tr></table>'
            ),
            'forgot-pass' => array(
                'title' => 'Nhà Thép Sài Gòn - Hướng dẫn lấy lại mật khẩu tài khoản',
                'content' => '<table style="width: 700px; position: relative; display: block; margin: 0 auto;"><tr><td>Chào <strong>#fullname#</strong> </td></tr>
                <tr><td>Chúng tôi vừa nhận được yêu cầu khôi phục mật khẩu từ phía bạn. Vui lòng nhấn vào đường link sau để khôi phục: <a href="#link#" target="_blank">#link#</a>.</td></tr>
                <tr><td>Sau ngày ' . date("d/m/Y", strtotime("+2 day")) . ', nếu bạn không kích hoạt link này thì mật khẩu vẫn sử dụng như cũ.</td></tr>
                </table>'
            ),
            'ads_contact' => array(
                'title' => 'Nhà Thép Sài Gòn - Liên hệ quảng cáo',
                'content' => '<table style="width: 700px; position: relative; display: block; margin: 0 auto;"><tr><td>Chào <strong>Nhà Thép Sài Gòn</strong> </td></tr>
                <tr><td>Tên: <strong>#fullname#</strong></td></tr>
                <tr><td>Email: #email#</a></td></tr>
                <tr><td>Điện thoại: #phone#</a></td></tr>
                <tr><td>Nội dung: #content#</a></td></tr></table>'
            )
        );
        $temp_footer = '<table style="width: 700px; position: relative; display: block; margin: 0 auto;">
                        <tr><td><p style="margin: 0 0 15px 0;">Cảm ơn bạn đã đọc tin tức tại website Nhà Thép Sài Gòn của chúng tôi.</p></td></tr>
						<tr><td><p style="margin: 0 0 15px 0;">Trân trọng.</p></td></tr>
                        <tr><td><p style="margin: 0 0 15px 0;"><a href="'.SITE_URL.'" target="_blank"><img src="cid:#ecl_footer_logo#" alt="" title="Nhà Thép Sài Gòn" /></a></p></td></tr>
                        <tr><td style="width: 100%; border-bottom:1px solid #ccc;"></td></tr>
                        <tr><td style="color: #999; font-size:11px;">Email được gửi tự động từ hệ thống, vui lòng không trả lời email này.</td></tr>
                        </table>';
        if (isset($arrTempl[$strTempl])) {
            $arrTempl[$strTempl]['content'] .= $temp_footer;
            return $arrTempl[$strTempl];
        } else {
            return false;
        }
    }

}
