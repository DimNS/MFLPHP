<?php
/**
 * Срабатывает при неверном CSRF-токене
 *
 * @version 02.08.2016
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Helpers;

class InvalidToken
{
    /**
     * В зависимости от запроса вернуть ответ или показать страницу
     *
     * @param object $request  Объект запроса
     * @param object $response Объект ответа
     * @param object $service  Объект сервисов
     *
     * @return array|redirect
     *
     * @version 02.08.2016
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public static function getResponse($request, $response, $service)
    {
        if ($request->headers()->get('X-Requested-With', '') === 'XMLHttpRequest') {
            $response->json([
                'error'   => true,
                'message' => 'Неверный защитный токен. Пожалуйста, перезагрузите страницу, чтобы получить новый токен.',
            ]);
        } else {
            $service->title         = 'Неверный защитный токен';
            $service->external_page = true;
            $service->back_url      = $request->server()->get('HTTP_REFERER', getenv('PATH_SHORT_ROOT') . '/');

            $service->render($service->app_root_path . '/Views/invalid-token.php');
        }
    }
}
