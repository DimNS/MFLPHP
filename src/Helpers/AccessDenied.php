<?php
/**
 * Срабатывает при отсутствии определенных прав
 *
 * @version 22.04.2017
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Helpers;

class AccessDenied
{
    /**
     * В зависимости от запроса вернуть ответ или сделать редирект
     *
     * @param object $request  Объект запроса
     * @param object $response Объект ответа
     * @param object $service  Объект сервисов
     *
     * @return array|redirect
     *
     * @version 22.04.2017
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public static function getResponse($request, $response, $service)
    {
        if ($request->headers()->get('X-Requested-With', '') === 'XMLHttpRequest') {
            $response->json([
                'error'   => true,
                'message' => 'Недостаточно прав.',
            ]);
        } else {
            $service->title = 'Недостаточно прав';
            $service->external_page = true;
            $service->render($service->app_root_path . '/Views/access-denied.php');
        }
    }
}
