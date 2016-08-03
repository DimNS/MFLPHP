<?php
/**
 * Смена адреса электронной почты
 *
 * @version 02.08.2016
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Pages\User;

class ActionChangeEmail extends \MFLPHP\Abstracts\ActionModel
{
    /**
     * Выполним действие
     *
     * @param integer $uid       Идентификатор пользователя
     * @param string  $new_email Новый адрес электронной почты
     * @param string  $password  Текущий пароль
     *
     * @return array
     *
     * @version 27.07.2016
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function run($uid, $new_email, $password)
    {
        return $this->di->auth->changeEmail($uid, $new_email, $password);
    }
}
