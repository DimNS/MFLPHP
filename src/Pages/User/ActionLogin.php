<?php
/**
 * Аутентификация
 *
 * @version 22.04.2017
 * @author  Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Pages\User;

use MFLPHP\Abstracts\ActionModel;

class ActionLogin extends ActionModel
{
    /**
     * Выполним действие
     *
     * @param string $user_email Адрес электронной почты
     * @param string $user_pass  Пароль пользователя
     *
     * @return array
     *
     * @version 22.04.2017
     * @author  Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function run($user_email, $user_pass)
    {
        $result = [
            'error'   => true,
            'message' => 'Неизвестная ошибка.',
        ];

        $loginResult = $this->di->auth->login($user_email, $user_pass, false);

        if ($loginResult['error'] === false) {
            $user_id = $this->di->auth->getUID($user_email);
            if ($user_id !== false) {
                $user_info = \ORM::for_table('users_info')
                    ->where_equal('uid', $user_id)
                    ->find_one();
                if (is_object($user_info)) {
                    $user_info->last_login = $this->di->carbon->toDateTimeString();
                    $user_info->save();

                    setcookie($this->di->auth->config->cookie_name, $loginResult['hash'], $loginResult['expire'], $this->di->auth->config->cookie_path, $this->di->auth->config->cookie_domain, $this->di->auth->config->cookie_secure, $this->di->auth->config->cookie_http);

                    return [
                        'error'   => false,
                        'message' => 'Добро пожаловать!',
                    ];
                } else {
                    $result['message'] = 'Произошла ошибка при изменении данных. Попробуйте войти ещё раз.';
                }
            } else {
                $result['message'] = 'Данные пользователя не найдены. Попробуйте войти ещё раз.';
            }
        } else {
            $result['message'] = $loginResult['message'];
        }

        return $result;
    }
}
