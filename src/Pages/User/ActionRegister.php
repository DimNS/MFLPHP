<?php
/**
 * Регистрация
 *
 * @version 22.04.2017
 * @author  Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Pages\User;

use MFLPHP\Abstracts\ActionModel;

class ActionRegister extends ActionModel
{
    /**
     * Выполним действие
     *
     * @param string $user_name  Имя пользователя
     * @param string $user_email Адрес электронной почты
     *
     * @return array
     *
     * @version 22.04.2017
     * @author  Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function run($user_name, $user_email)
    {
        $result = [
            'error'   => true,
            'message' => 'Неизвестная ошибка.',
        ];

        // Определим длину пароля
        $length = 10;

        // Создадим временный пароль
        $zxcvbn = new \ZxcvbnPhp\Zxcvbn();
        $password = $this->di->auth->getRandomKey($length);
        while ($zxcvbn->passwordStrength($password)['score'] < intval($this->di->auth->config->password_min_score)) {
            $password = $this->di->auth->getRandomKey($length);
        }

        // Добавим пользователя
        $registerResult = $this->di->auth->register($user_email, $password, $password);

        if ($registerResult['error'] === false) {
            $user_id = $this->di->auth->getUID($user_email);
            if ($user_id !== false) {
                $user_info = \ORM::for_table('users_info')->create();
                $user_info->uid = $user_id;
                $user_info->name = $user_name;
                $user_info->access = 'user';
                $user_info->created_at = $this->di->carbon->toDateTimeString();
                $user_info->save();

                if (is_object($user_info) AND isset($user_info->id)) {
                    // Отправим сообщение на почту
                    $this->di->mail->send($user_email, $user_name . ', добро пожаловать в "' . $this->di->auth->config->site_name . '"', 'USER_REGISTER', [
                        '[[SITE_NAME]]'     => $this->di->auth->config->site_name,
                        '[[SITE_URL]]'      => $this->di->auth->config->site_url,
                        '[[USER_EMAIL]]'    => $user_email,
                        '[[USER_PASSWORD]]' => $password,
                    ]);

                    // Войдем под этим пользователем
                    $login = new ActionLogin($this->di);
                    $result = $login->run($user_email, $password);
                } else {
                    \ORM::for_table('users')
                        ->where_equal('id', $user_id)
                        ->delete();

                    $result['message'] = 'Произошла ошибка при добавлении пользователя. Попробуйте ещё раз.';
                }
            } else {
                \ORM::for_table('users')
                    ->where_equal('email', $user_email)
                    ->delete();

                $result['message'] = 'Пользователь не зарегистрирован. Попробуйте ещё раз.';
            }
        } else {
            $result['message'] = $registerResult['message'];
        }

        return $result;
    }
}
