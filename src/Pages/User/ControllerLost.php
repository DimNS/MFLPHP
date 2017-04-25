<?php
/**
 * Запрос на восстановление пароля
 *
 * @version 25.04.2017
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Pages\User;

use MFLPHP\Helpers\Middleware;

class ControllerLost extends \MFLPHP\Abstracts\PageControllerUser
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
     * @version 25.04.2017
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function start($request, $response, $service, $di)
    {
        if ($request->method('post') === true) {
            $middleware = Middleware::start($request, $response, $service, $di, [
                'token',
            ]);
            if ($middleware) {
                $lost   = new ActionLost($di);
                $result = $lost->run($request->param('email'));

                if ($result['error'] === false) {
                    $service->message_code = 'success';
                } else {
                    $service->message_code = 'danger';
                }

                $service->message_text = $result['message'];
            }
        }

        $service->title         = $di->auth->config->site_name;
        $service->external_page = true;

        $service->render($service->app_root_path . '/' . $this->view_prefix . 'lost.php');
    }
}
