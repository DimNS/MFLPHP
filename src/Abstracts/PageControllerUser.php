<?php
/**
 * Шаблон контроллера страниц для работы с пользователем
 *
 * @version 09.09.2016
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Abstracts;

abstract class PageControllerUser extends \MFLPHP\Abstracts\PageController
{
    /**
     * Префикс для подстановки в путь до представления
     *
     * @var string
     */
    protected $view_prefix = 'Pages/User/view_';
}
