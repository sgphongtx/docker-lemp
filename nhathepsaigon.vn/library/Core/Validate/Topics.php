<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 17:27
 */
class Core_Validate_Topics
{
    public static function checkTopic($arrData = array())
    {
        $arrReturn = array(
            'error' => 1,
            'msg' => 'Có lỗi xảy ra'
        );
        if ($arrData['strTopicName'] == '') {
            $arrReturn['msg'] = 'Topic name không được để trống';
        } else {
            $arrParams = array(
                'strTopicName' => $arrData['strTopicName']
            );
            $check = Core_Model_Topics::checkTopic($arrParams);
            if (!empty($check)) {
                if($check['topic_id'] != $arrData['intTopicId']) {
                    $arrReturn['msg'] = 'Topic name này đã tồn tại trong hệ thống';
                }else{
                    $arrReturn['error'] = 0;
                    $arrReturn['msg'] = 'Ok';
                }
            } else {
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = 'Ok';
            }
        }
        return $arrReturn;
    }
}

?>