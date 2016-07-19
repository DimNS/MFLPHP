<?php
/**
 * Контроллер главной страницы
 *
 * @version ===
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Abstracts;

abstract class PageController
{
    /**
    * @var object $request Объект запроса
    */
    protected $request;

    /**
    * @var object $response Объект ответа
    */
    protected $response;

    /**
    * @var object $service Объект сервисов
    */
    protected $service;

    /**
    * @var object $di Объект контейнера зависимостей
    */
    protected $di;

    /**
     * Конструктор
     *
     * @param object $request  Объект запроса
     * @param object $response Объект ответа
     * @param object $service  Объект сервисов
     * @param object $di       Объект контейнера зависимостей
     *
     * @return null
     *
     * @version ===
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function __construct($request, $response, $service, $di)
    {
        $this->request  = $request;
        $this->response = $response;
        $this->service  = $service;
        $this->di       = $di;
    }
}
