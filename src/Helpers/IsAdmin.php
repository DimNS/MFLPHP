<?php
/**
 * Проверка на права админа
 *
 * @version 03.08.2016
 * @author  Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Helpers;

class IsAdmin
{
    /**
     * Проверяем права пользователя
     *
     * @param string $access Текущие права пользователя
     *
     * @return boolean
     *
     * @version 03.08.2016
     * @author  Дмитрий Щербаков <atomcms@ya.ru>
     */
    public static function check($access)
    {
        if ($access === 'admin') {
            return true;
        } else {
            return false;
        }
    }
}
