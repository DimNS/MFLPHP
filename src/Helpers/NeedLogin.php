<?php
/**
 * Отображение формы входа
 *
 * @version 27.07.2016
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Helpers;

class NeedLogin
{
    /**
     * В зависимости от запроса вернуть ответ или показать страницу
     *
     * @param object $request  Объект запроса
     * @param object $response Объект ответа
     * @param object $service  Объект сервисов
     * @param object $di       Контейнер
     *
     * @return array|redirect
     *
     * @version 27.07.2016
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public static function getResponse($request, $response, $service, $di)
    {
        if ($request->headers()->get('X-Requested-With', '') === 'XMLHttpRequest') {
            $response->json([
                'error'   => true,
                'message' => 'Необходимо войти в систему.',
            ]);
        } else {
            $service->uri           = $request->uri();
            $service->title         = $di->auth->config->site_name;
            $service->external_page = true;
            $service->message_code  = 'primary';
            $service->message_text  = 'Необходимо войти в систему.';

            $service->render($service->app_root_path . '/Pages/User/view_auth.php');
        }
    }
}
