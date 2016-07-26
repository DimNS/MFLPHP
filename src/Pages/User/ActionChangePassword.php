<?php
/**
 * Смена пароля
 *
 * @version ===
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Pages\User;

class ActionChangePassword
{
    /**
     * Контейнер
     *
     * @var object
     */
    private $di;

    /**
     * Конструктор
     *
     * @param object $di Контейнер
     *
     * @return null
     *
     * @version ===
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function __construct($di)
    {
        $this->di = $di;
    }

    /**
     * Выполним действие
     *
     * @param integer $uid          Идентификатор пользователя
     * @param string  $new_password Новый пароль
     * @param string  $old_password Старый пароль
     *
     * @return array
     *
     * @version ===
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function run($uid, $new_password, $old_password)
    {
        return $this->di->auth->changePassword($uid, $old_password, $new_password, $new_password);
    }
}
