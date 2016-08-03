<?php
/**
 * Выход
 *
 * @version 02.08.2016
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Pages\User;

class ActionLogout extends \MFLPHP\Abstracts\ActionModel
{
    /**
     * Выполним действие
     *
     * @param string $hash $_COOKIE['authID']
     *
     * @version 27.07.2016
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function run($hash)
    {
        if ($this->di->auth->logout($hash)) {
            unset($_COOKIE[$this->di->auth->config->cookie_name]);
        }
    }
}
