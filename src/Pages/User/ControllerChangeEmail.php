<?php
/**
 * Запрос на смену адреса электронной почты
 *
 * @version 09.09.2016
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Pages\User;

use MFLPHP\Helpers\Middleware;

class ControllerChangeEmail extends \MFLPHP\Abstracts\PageControllerUser
{
    //
    //
    //               mm                       mm
    //               MM                       MM
    //     ,pP"Ybd mmMMmm  ,6"Yb.  `7Mb,od8 mmMMmm
    //     8I   `"   MM   8)   MM    MM' "'   MM
    //     `YMMMa.   MM    ,pm9MM    MM       MM
    //     L.   I8   MM   8M   MM    MM       MM
    //     M9mmmP'   `Mbmo`Moo9^Yo..JMML.     `Mbmo
    //
    //

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
    public function start($request, $response, $service, $di)
    {
        $middleware = Middleware::start($request, $response, $service, $di, [
            'auth',
            'token',
        ]);
        if ($middleware) {
            $result = $di->auth->changeEmail($di->userinfo->uid, $request->param('new_email'), $request->param('password'));
            $response->json($result);
        }
    }
}
