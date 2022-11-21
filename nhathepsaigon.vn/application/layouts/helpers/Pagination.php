<?php
/**
 * @author      :   PhongTX
 * @name        :   Zend_View_Helper_Pagination
 * @copyright   :   Fpt Online
 * @todo        :   Pagination Helper
 */
class Zend_View_Helper_Pagination extends Zend_View_Helper_Abstract
{
    public function Pagination($total, $page, $limit,$showTotal=0,$isAjax = false)
    {
        if($total < 1){
            return false;
        }

        $strTempl = $showTotal== 1 ? '/scripts/backend/common/pagination_control.phtml' : '/scripts/backend/common/pagination.phtml';

        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($total));
        Zend_View_Helper_PaginationControl::setDefaultViewPartial(array($strTempl, 'default'));
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($limit);
        $paginator->setDefaultPageRange(7);
        return $paginator;
    }
}

?>