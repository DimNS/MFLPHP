<?php
/**
 * Смена адреса электронной почты
 *
 * @version ===
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Pages\User;

class ActionChangeEmail
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
     * @param integer $uid       Идентификатор пользователя
     * @param string  $new_email Новый адрес электронной почты
     * @param string  $password  Текущий пароль
     *
     * @return array
     *
     * @version ===
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function run($uid, $new_email, $password)
    {
        return $this->di->auth->changeEmail($uid, $new_email, $password);
    }
}
