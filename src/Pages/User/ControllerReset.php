<?php
/**
 * Проверка ключа на восстановление пароля
 *
 * @version 09.09.2016
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Pages\User;

use MFLPHP\Helpers\Middleware;

class ControllerReset extends \MFLPHP\Abstracts\PageControllerUser
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
        if ($request->method('post') === true) {
            $middleware = Middleware::start($request, $response, $service, $di, [
                'token',
            ]);
            if ($middleware) {
                $result = $di->auth->resetPass($request->param('key'), $request->param('password'), $request->param('password'));
            }
        } else {
            $result = $di->auth->getRequest($request->param('key'), 'reset');
        }

        if ($result['error'] === false) {
            if ($request->method('post') == true) {
                $service->message_code = 'success';
                $service->message_text = $result['message'];
                $template = 'auth';
            } else {
                $service->message_code = 'primary';
                $service->message_text = 'Восстановление пароля';
                $template = 'reset';
            }
        } else {
            $service->message_code = 'danger';
            $service->message_text = $result['message'];

            if ($request->method('post') === true) {
                $template = 'reset';
            } else {
                $template = 'auth';
            }
        }

        $service->title         = $di->auth->config->site_name;
        $service->uri           = $request->uri();
        $service->external_page = true;
        $service->key           = $request->param('key');

        $service->render($service->app_root_path . '/' . $this->view_prefix . $template . '.php');
    }
}
