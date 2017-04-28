<?php
/**
 * Модель действия
 *
 * @version 02.08.2016
 * @author  Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Abstracts;

abstract class ActionModel
{
    /**
     * Контейнер
     *
     * @var object
     */
    protected $di;

    /**
     * Конструктор
     *
     * @param object $di Контейнер
     *
     * @version 02.08.2016
     * @author  Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function __construct($di)
    {
        $this->di = $di;
    }
}
