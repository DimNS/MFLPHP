<?php
/**
 * Запрос на восстановление пароля
 *
 * @version 13.09.2016
 * @author  Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Pages\User;

use MFLPHP\Abstracts\ActionModel;

class ActionLost extends ActionModel
{
    /**
     * Выполним действие
     *
     * @param string $user_email Адрес электронной почты
     *
     * @return array
     *
     * @version 13.09.2016
     * @author  Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function run($user_email)
    {
        $result = [
            'error'   => true,
            'message' => 'Неизвестная ошибка.',
        ];

        // Сформируем запрос на смену пароля
        $result_request_reset = $this->di->auth->requestReset($user_email, false);
        if ($result_request_reset['error'] === false) {
            // Найдем ИД пользователя
            $user_info = \ORM::for_table('users')
                ->select('id')
                ->where_equal('email', $user_email)
                ->find_one();
            if (is_object($user_info)) {
                // Найдем в базе ключ для отправки собственного письма
                $request_info = \ORM::for_table($this->di->auth->config->table_requests)
                    ->select('rkey')
                    ->where([
                        'uid'  => $user_info->id,
                        'type' => 'reset',
                    ])
                    ->find_one();
                if (is_object($request_info)) {
                    // Отправим сообщение на почту
                    $send_result = $this->di->mail->send($user_email, 'Запрос на изменение пароля в "' . $this->di->auth->config->site_name . '"', 'USER_LOST', [
                        '[[SITE_NAME]]'  => $this->di->auth->config->site_name,
                        '[[SITE_URL]]'   => $this->di->auth->config->site_url,
                        '[[USER_EMAIL]]' => $user_email,
                        '[[KEY]]'        => $request_info->rkey,
                    ]);
                    if ($send_result) {
                        return [
                            'error'   => false,
                            'message' => 'Запрос успешно отправлен на электронную почту "' . $user_email . '".',
                        ];
                    } else {
                        $result['message'] = 'Произошла ошибка. Пожалуйста, обратитесь к разработчику.';
                    }
                } else {
                    $this->di->log->warning('Для запроса на восстановление пароля не найден ключ восстановления: "' . $user_email . '" (#' . $user_info->id . ', type=reset).');
                    $result['message'] = 'Произошла ошибка. Пожалуйста, обратитесь к разработчику.';
                }
            } else {
                $this->di->log->warning('Для запроса на восстановление пароля не найден пользователь: "' . $user_email . '".');
                $result['message'] = 'Произошла ошибка. Пожалуйста, обратитесь к разработчику.';
            }
        } else {
            $result['message'] = $result_request_reset['message'];
        }

        return $result;
    }
}
