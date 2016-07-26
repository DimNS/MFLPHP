<?php
/**
 * Выход
 *
 * @version ===
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Pages\User;

class ActionLogout
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
     * @param string $hash $_COOKIE['authID']
     *
     * @version ===
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function run($hash)
    {
        if ($this->di->auth->logout($hash)) {
            unset($_COOKIE[$this->di->auth->config->cookie_name]);
        }
    }
}
