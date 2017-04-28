<?php
/**
 * Отображение профиля пользователя
 *
 * @version 28.04.2017
 * @author  Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Pages\User;

use MFLPHP\Abstracts\PageControllerUser;
use MFLPHP\Helpers\Middleware;

class ControllerIndex extends PageControllerUser
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
     * @version 28.04.2017
     * @author  Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function start($request, $response, $service, $di)
    {
        $middleware = Middleware::start($request, $response, $service, $di, [
            'auth',
        ]);
        if ($middleware) {
            $service->title = 'Мой профиль | ' . $di->auth->config->site_name;
            $service->userinfo = $di->userinfo;

            $service->render($service->app_root_path . '/' . $this->view_prefix . 'index.php');
        }

        return;
    }
}
