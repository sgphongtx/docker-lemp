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
                'title' => 'Nh?? Th??p S??i G??n - Y??u c???u k??ch ho???t t??i kho???n',
                'content' => '<table style="width: 700px; position: relative; display: block; margin: 0 auto;"><tr><td>Ch??o <strong>#fullname#</strong> </td></tr>
                <tr><td>C???m ??n b???n ???? ????ng k?? th??nh vi??n t???i <a href="'.SITE_URL.'" target="_blank">'.SITE_URL.'</a></td></tr>
                <tr><td>????? ho??n t???t ????ng k??, b???n vui l??ng k??ch ho???t t??i kho???n b???ng c??ch nh???n v??o ???????ng link sau: <a href="#link#" target="_blank">#link#</a>.</td></tr>
                <tr><td>B???n c??ng c?? th??? nh???p ???????ng d???n tr??n v??o tr??nh duy???t ????? k??ch ho???t t??i kho???n.</td></tr>
                <tr><td>Th??ng tin ????ng nh???p c???a b???n nh?? sau:</a></td></tr>
                <tr><td>Email: #email#</a></td></tr>
                <tr><td>M???t kh???u: #password#</a></td></tr></table>'
            ),
            'verify-email-register' => array(
                'title' => 'Nh?? Th??p S??i G??n - T??i kho???n c???a b???n ???? ???????c k??ch ho???t th??nh c??ng',
                'content' => '<table style="width: 700px; position: relative; display: block; margin: 0 auto;"><tr><td>Ch??o <strong>#fullname#</strong> </td></tr>
                <tr><td>Ch??c m???ng b???n ???? k??ch ho???t t??i kho???n th??nh c??ng t???i <a href="'.SITE_URL.'" target="_blank">'.SITE_URL.'</a>.</td></tr></table>'
            ),
            'forgot-pass' => array(
                'title' => 'Nh?? Th??p S??i G??n - H?????ng d???n l???y l???i m???t kh???u t??i kho???n',
                'content' => '<table style="width: 700px; position: relative; display: block; margin: 0 auto;"><tr><td>Ch??o <strong>#fullname#</strong> </td></tr>
                <tr><td>Ch??ng t??i v???a nh???n ???????c y??u c???u kh??i ph???c m???t kh???u t??? ph??a b???n. Vui l??ng nh???n v??o ???????ng link sau ????? kh??i ph???c: <a href="#link#" target="_blank">#link#</a>.</td></tr>
                <tr><td>Sau ng??y ' . date("d/m/Y", strtotime("+2 day")) . ', n???u b???n kh??ng k??ch ho???t link n??y th?? m???t kh???u v???n s??? d???ng nh?? c??.</td></tr>
                </table>'
            ),
            'ads_contact' => array(
                'title' => 'Nh?? Th??p S??i G??n - Li??n h??? qu???ng c??o',
                'content' => '<table style="width: 700px; position: relative; display: block; margin: 0 auto;"><tr><td>Ch??o <strong>Nh?? Th??p S??i G??n</strong> </td></tr>
                <tr><td>T??n: <strong>#fullname#</strong></td></tr>
                <tr><td>Email: #email#</a></td></tr>
                <tr><td>??i???n tho???i: #phone#</a></td></tr>
                <tr><td>N???i dung: #content#</a></td></tr></table>'
            )
        );
        $temp_footer = '<table style="width: 700px; position: relative; display: block; margin: 0 auto;">
                        <tr><td><p style="margin: 0 0 15px 0;">C???m ??n b???n ???? ?????c tin t???c t???i website Nh?? Th??p S??i G??n c???a ch??ng t??i.</p></td></tr>
						<tr><td><p style="margin: 0 0 15px 0;">Tr??n tr???ng.</p></td></tr>
                        <tr><td><p style="margin: 0 0 15px 0;"><a href="'.SITE_URL.'" target="_blank"><img src="cid:#ecl_footer_logo#" alt="" title="Nh?? Th??p S??i G??n" /></a></p></td></tr>
                        <tr><td style="width: 100%; border-bottom:1px solid #ccc;"></td></tr>
                        <tr><td style="color: #999; font-size:11px;">Email ???????c g???i t??? ?????ng t??? h??? th???ng, vui l??ng kh??ng tr??? l???i email n??y.</td></tr>
                        </table>';
        if (isset($arrTempl[$strTempl])) {
            $arrTempl[$strTempl]['content'] .= $temp_footer;
            return $arrTempl[$strTempl];
        } else {
            return false;
        }
    }

}
