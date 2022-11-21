<?php

/**
 * @name : Pagination
 * @copyright   : FPT Online
 * @todo    : Helper Pagination: phan trang
 */
class Zend_View_Helper_Paginationfe extends Zend_View_Helper_Abstract
{

    /**
     * Pagination
     * @param <array> $arrParams
     * @return string
     */
    public function Paginationfe($arrParams = array())
    {
        $strReturn = '';
        $arrParamDefault = array(
            'total' => 10,
            'page' => 1,
            'url' => '',
            'showItem' => 2,
            'perpage' => 10,
            'idPagination' => 'pagination',
            'extEnd' => '.html',
            'separate' => '/'
        );
        $arrParams = array_merge($arrParamDefault, (array)$arrParams);

        if ($arrParams['total'] > $arrParams['perpage'])
        {
            //Init Check Param
            $intTotalRecord = $arrParams['total'];
            $record_page = $arrParams['perpage'];
            $intPage = $arrParams['page'];
            $dot = $arrParams['separate'];
            $strLink = $arrParams['url'];
            $intItem = $arrParams['showItem'];
            $idPaging = $arrParams['idPagination'];
            $extraEnd = $arrParams['extEnd'];

            //Get Data Page
            $intTotalPage = ceil($intTotalRecord / $record_page);
            $intStart = (($intPage - 1) * $record_page + 1);
            $intEnd = min($intPage * $record_page, $intTotalRecord);
            $strUrl = $dot;
            $intPage = max($intPage, 1);
            $intPage = min($intTotalPage, $intPage);

            $intPageStart = $intPage - $intItem;
            $intPageEnd = $intPage + $intItem;

            if ($intPageStart < 1 || $intPageStart == 1)
            {
                $intPageStart = 1;
                $intPageEnd = ($intPageStart + 4) <= $intTotalPage ? ($intPageStart + 4) : $intTotalPage;
            }
            if ($intPageEnd > $intTotalPage || $intPageEnd == $intTotalPage)
            {
                $intPageEnd = $intTotalPage;
                $intPageStart = (($intPageEnd - 4) > 1) ? ($intPageEnd - 4) : 1;
            }
            $strReturn = '<div id="' . $idPaging . '">';
            $strtPreviousPageLink = ($intPage > 1) ? 'href="'.$strLink . $strUrl . ($intPage - 1) . $extraEnd.'"': '';
            if ($intPage > 1) $strReturn .='<a class="pagination_btn pa_prev" ' . $strtPreviousPageLink . '><</a>';
            for ($intTemp = $intPageStart; $intTemp <= $intPageEnd; $intTemp++)
            {
                $strTempLink = ($intTemp == $intPage) ? '' : 'href="'.$strLink . $strUrl . $intTemp . $extraEnd.'"';
                $strTempClass = ($intTemp == $intPage) ? ' class="active"' : '';
                $strReturn .= '<a' . $strTempClass . ' ' . $strTempLink . '>' . $intTemp . '</a>';
            }
            $strtNextPageLink = ($intTotalPage > $intPage) ? 'href="'.$strLink . $strUrl . ($intPage + 1) . $extraEnd .'"': 'javascript:;';
            if ($intTotalPage > $intPage) $strReturn .='<a class="pagination_btn pa_next" ' . $strtNextPageLink . '>></a>';
            $strReturn .= '</div>';
        }

        //return Data
        return $strReturn;
    }

}