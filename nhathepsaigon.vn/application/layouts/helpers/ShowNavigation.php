<?php
class Zend_View_Helper_ShowNavigation extends Zend_View_Helper_Abstract
{
    public function ShowNavigation()
    {
        // set active
        $request = Zend_Controller_Front::getInstance()->getRequest();

        $strModuleName = $request->getModuleName();
        $strControllerName = $request->getControllerName();
        $strActionName = $request->getActionName();

        $strKeyActived = $strModuleName.'_'.$strControllerName.'_'.$strActionName;

        $strHTML = '';
        $strLI = '';

        $arrMenuShow = isset($this->view->user['permission']['menu']) ? Zend_Json::decode($this->view->user['permission']['menu']) : '';

        $arrCSSIcon = array(
                '1' => 'icon-user',
                '11' => 'fa fa-desktop',
                '18' => 'fa fa-wrench',
                '25' => 'fa fa-bar-chart',
                '31' => 'fa fa-area-chart ',
                '0' => 'icon-tag'
                );

        if (isset($arrMenuShow[0]) && !empty($arrMenuShow[0]))
        {
            foreach ($arrMenuShow[0] as $arrMenu)
            {
                $active = '';
                $angle = 'icon-angle-right';
                $ulStyle = '';
                if($arrMenu['menu_code'] == $strModuleName 
                    || $arrMenu['menu_code'] == $strModuleName .'_'. $strControllerName)
                {
                    $active = 'active';
                    $angle = 'icon-angle-down';
                    $ulStyle = 'style="display:block"';
                }
                
                $strCSSIcon = isset($arrCSSIcon[$arrMenu['menu_id']]) ? $arrCSSIcon[$arrMenu['menu_id']] : $arrCSSIcon[0];

                $strLI = '<li>';

                $strLI .= '<div class="'.$active.'"><i class="'. $strCSSIcon .'"></i>'.$arrMenu['menu_name'].'</div>';
                if(isset($arrMenuShow[$arrMenu['menu_id']])&&!empty($arrMenuShow[$arrMenu['menu_id']]))
                {
                    $strLI .= '<ul '.$ulStyle.'>';
                    foreach ($arrMenuShow[$arrMenu['menu_id']] as $child)
                    {
                        $active = '';
                        //var_dump($child['menu_code'],'/'. $strControllerName);
                        //exit;
                        if($child['menu_code'] == $strKeyActived)
                        {
                            $active = 'class="active"';
                        }
                        $strLI .= '<li '.$active.'><a href="'.$child['url'].'">'.$child['menu_name'].'</a></li>';
                    }
                    $strLI .= '</ul>';
                }
                $strLI .= '</li>';
                $strHTML .= $strLI;
            }
        }
        echo ('<ul>'.$strHTML.'</ul>');
    }
}

?>