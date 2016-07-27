<?php
/**
 * Срабатывает при неверном CSRF-токене
 *
 * @version 27.07.2016
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Helpers;

class InvalidToken
{
    /**
     * В зависимости от запроса вернуть ответ или сделать редирект
     *
     * @param object $request  Объект запроса
     * @param object $response Объект ответа
     *
     * @return array|redirect
     *
     * @version 27.07.2016
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public static function getResponse($request, $response)
    {
        if ($request->headers()->get('X-Requested-With', '') === 'XMLHttpRequest') {
            $response->json([
                'error'   => true,
                'message' => 'Неверный защитный токен. Пожалуйста, перезагрузите страницу, чтобы получить новый токен.',
            ]);
        } else {
            $response->redirect(getenv('PATH_SHORT_ROOT'), 403);
        }
    }
}
