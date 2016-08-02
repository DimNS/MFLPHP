<?php
/**
 * Очистка данных для защиты от XSS
 *
 * @param string $str Строка для очистки
 *
 * @return string Очищенная строка
 *
 * @version 02.08.2016
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */
function xss($str)
{
    static $anti_xss;

    if (is_object($anti_xss) === false) {
        $anti_xss = new \voku\helper\AntiXSS();
    }

    return $anti_xss->xss_clean($str);
}
