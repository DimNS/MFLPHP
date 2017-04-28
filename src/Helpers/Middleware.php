<?php
/**
 * Промежуточный слой для проверки пользователь в системе, валидный токен, есть права доступа
 *
 * @version 05.08.2016
 * @author  Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Helpers;

class Middleware
{
    /**
     * Проверяем права пользователя
     *
     * @param object $request  Объект запроса
     * @param object $response Объект ответа
     * @param object $service  Объект сервисов
     * @param object $di       Объект контейнера зависимостей
     * @param array  $actions  Массив проверок (порядок важен)
     *
     * @return null
     *
     * @version 05.08.2016
     * @author  Дмитрий Щербаков <atomcms@ya.ru>
     */
    public static function start($request, $response, $service, $di, $actions)
    {
        if (is_array($actions) AND count($actions) > 0) {
            foreach ($actions as $action) {
                switch ($action) {
                    case 'auth':
                        if ($di->auth->isLogged() === false) {
                            NeedLogin::getResponse($request, $response, $service, $di);

                            return false;
                        }
                        break;

                    case 'token':
                        if ($request->server()->get('HTTP_X_CSRFTOKEN', '') === '') {
                            if ($di->csrf->validateToken($request->param('_token')) === false) {
                                InvalidToken::getResponse($request, $response, $service);

                                return false;
                            }
                        } else {
                            if ($di->csrf->validateToken($request->server()->get('HTTP_X_CSRFTOKEN', '')) === false) {
                                InvalidToken::getResponse($request, $response, $service);

                                return false;
                            }
                        }
                        break;

                    case 'access-admin':
                        if ($di->userinfo->access !== 'admin') {
                            AccessDenied::getResponse($request, $response, $service);

                            return false;
                        }
                        break;
                }
            }
        }

        return true;
    }
}
