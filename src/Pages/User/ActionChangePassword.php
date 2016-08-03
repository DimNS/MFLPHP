<?php
/**
 * Смена пароля
 *
 * @version 02.08.2016
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Pages\User;

class ActionChangePassword extends \MFLPHP\Abstracts\ActionModel
{
    /**
     * Выполним действие
     *
     * @param integer $uid          Идентификатор пользователя
     * @param string  $new_password Новый пароль
     * @param string  $old_password Старый пароль
     *
     * @return array
     *
     * @version 27.07.2016
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function run($uid, $new_password, $old_password)
    {
        return $this->di->auth->changePassword($uid, $old_password, $new_password, $new_password);
    }
}