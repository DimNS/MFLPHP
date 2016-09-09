<?php
/**
 * Шаблон контроллера страниц
 *
 * @version 09.09.2016
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Abstracts;

abstract class PageController
{
    /**
     * Стартовый метод
     *
     * @param object $request  Объект запроса
     * @param object $response Объект ответа
     * @param object $service  Объект сервисов
     * @param object $di       Объект контейнера зависимостей
     *
     * @return null
     *
     * @version 09.09.2016
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    abstract public function start($request, $response, $service, $di);
}
