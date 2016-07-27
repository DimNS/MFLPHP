<?php
/**
 * Проверка ключа на восстановление пароля
 *
 * @version 27.07.2016
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Pages\User;

class ActionReset
{
    /**
     * Контейнер
     *
     * @var object
     */
    private $di;

    /**
     * Ключ для восстановления пароля
     *
     * @var string
     */
    private $key;

    /**
     * Конструктор
     *
     * @param object $di  Контейнер
     * @param string $key Ключ для восстановления пароля
     *
     * @return null
     *
     * @version 27.07.2016
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function __construct($di, $key)
    {
        $this->di  = $di;
        $this->key = $key;
    }

    /**
     * Выполним проверку ключа, чтобы показать форму на изменение пароля
     *
     * @return array
     *
     * @version 27.07.2016
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function showForm()
    {
        return $this->di->auth->getRequest($this->key, 'reset');
    }

    /**
     * Установим пользователю новый пароль
     *
     * @param string $password Новый пароль
     *
     * @return array
     *
     * @version 27.07.2016
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function resetPassword($password)
    {
        return $this->di->auth->resetPass($this->key, $password, $password);
    }
}
