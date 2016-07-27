<?php
/**
 * Контроллер главной страницы
 *
 * @version 27.07.2016
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Abstracts;

abstract class PageController
{
    /**
     * Объект запроса
     *
     * @var object
     */
    protected $request;

    /**
     * Объект ответа
     *
     * @var object
     */
    protected $response;

    /**
     * Объект сервисов
     *
     * @var object
     */
    protected $service;

    /**
     * Контейнер
     *
     * @var object
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
     * @version 27.07.2016
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
