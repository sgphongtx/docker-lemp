<?php

/**
 * @name    : DateTimeFormat
 * @todo    : Helper DateTimeFormat
 * @param   : $dateSeparator - dau ngan cach giua ngay, thang, nam
 * @param   : $timeSeparator - dau ngan cach giua gio, phut, giay
 * @param   : $timeDateSeparator - dau ngan cach giua cum gio va ngay
 * @return  : String
 */
class Zend_View_Helper_DateTimeFormat extends Zend_View_Helper_Abstract
{

    public function DateTimeFormat($dateTime, $formatType = 'custom', $dateSeparator = '/', $timeSeparator = ':', $timeDateSeparator = ' | ')
    {
        if (empty($dateTime))
        {
            return '';
        }

        switch ($formatType)
        {
            case 'date': // 3/9/2012
                $year = date('Y', $dateTime);
                $month = date('n', $dateTime);
                $day = date('j', $dateTime);
                $strDate = "{$day}{$dateSeparator}{$month}{$dateSeparator}{$year}";
                break;
            case 'long': //Thứ sáu, 4/10/2013 | 10:21 GMT+7         
                $arrDate = getdate($dateTime);
                $arrWeekay = array('Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy');
                $strDate = $arrWeekay["$arrDate[wday]"] . ', ' . date('j/n/Y', $dateTime) . '<span class="drash"> | </span>' . date('H:i', $dateTime) . ' GMT+7';
                break;
            case 'long_short': //4/10/2013 | 10:21 GMT+7
                $arrDate = getdate($dateTime);
                $strDate = date('j/n/Y', $dateTime) . '<span class="drash"> | </span>' . date('H:i', $dateTime) . ' GMT+7';
                break;
            case 'short': // 3/9
                $month = date('n', $dateTime);
                $day = date('j', $dateTime);
                $strDate = "{$day}{$dateSeparator}{$month}";
                break;
            case 'profile':
                $arrDate = explode('-', $dateTime);
                $strDate = '';
                if ($arrDate[2] && $arrDate[2] != '00')
                {
                    $strDate = (int) $arrDate[2] . $dateSeparator;
                }
                if ($arrDate[1] && $arrDate[1] != '00')
                {
                    $strDate .= (int) $arrDate[1] . $dateSeparator;
                }
                if ($arrDate[0] && $arrDate[0] != '0000')
                {
                    $strDate .= $arrDate[0];
                }
                break;
            case 'interview': //15h00. Thứ tư, 24/5/2015
                $year = strftime('%Y', $dateTime);
                $month = intval(strftime('%m', $dateTime));
                $day = strftime('%e', $dateTime);
                $hour = strftime('%H', $dateTime);
                $minute = strftime('%M', $dateTime);
                $arrDate = getdate($dateTime);
                $arrWeekay = array('Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy');
                $strDate = "{$hour}{$timeSeparator}{$minute}. " . $arrWeekay["$arrDate[wday]"] . ", {$day}{$dateSeparator}{$month}{$dateSeparator}{$year}";
                break;
            case 'short_article': // 08:10 | 5/10/2012 -> co rut gon
                //time of article
                $year = date('Y', $dateTime);
                $month = date('n', $dateTime);
                $day = date('j', $dateTime);
                $hour = date('H', $dateTime);
                $minute = date('i', $dateTime);
                //current time
                $yearC = date('Y');
                $monthC = date('n');
                $dayC = date('j');
                if ($day == $dayC && $month == $monthC && $year == $yearC)
                {
                    $strDate = "<span class=\"txt_vne\">{$hour}{$timeSeparator}{$minute}</span>";
                }
                elseif ($year == $yearC)
                {
                    $strDate = "<span class=\"txt_vne\">{$hour}{$timeSeparator}{$minute}</span>{$timeDateSeparator}{$day}{$dateSeparator}{$month}";
                }
                else
                {
                    $strDate = "<span class=\"txt_vne\">{$hour}{$timeSeparator}{$minute}</span>{$timeDateSeparator}{$day}{$dateSeparator}{$month}{$dateSeparator}{$year}";
                }
                break;
            case 'common': // 08:10 | 2/4/2013
            case 'default':
            default :
                $year = date('Y', $dateTime);
                $month = date('n', $dateTime);
                $day = date('j', $dateTime);
                $hour = date('H', $dateTime);
                $minute = date('i', $dateTime);
                $strDate = "{$hour}{$timeSeparator}{$minute}{$timeDateSeparator}{$day}{$dateSeparator}{$month}{$dateSeparator}{$year}";
                break;
        }

        //return
        return trim($strDate);
    }

}
