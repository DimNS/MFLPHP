<?php
/**
 * Класс для отправки электронных писем
 *
 * @version 13.09.2016
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Helpers;

use MFLPHP\Configs\EmailTemplates;
use MFLPHP\Configs\Settings;

class EmailSender
{
    /**
     * Контейнер
     *
     * @var object
     */
    protected $di;

    /**
     * Конструктор
     *
     * @param object $di Объект контейнера зависимостей
     *
     * @return null
     *
     * @version 27.07.2016
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function __construct($di)
    {
        $this->di = $di;
    }

    /**
     * Отправка письма
     *
     * @param string $email            Электронная почта уда отправить письмо
     * @param string $subject          Тема письма
     * @param string $message_template Адрес шаблона
     * @param array  $data             Данные для подстановки в шаблон
     *
     * @return boolean
     *
     * @version 13.09.2016
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function send($email, $subject, $message_template, $data)
    {
        // Проверяем наличие шаблона
        if (constant('MFLPHP\Configs\EmailTemplates::'. $message_template) !== null) {
            if (Settings::PRODUCTION === false AND $this->di->auth->config->smtp === '0') {
                return true;
            } else {
                // Очищаемся от старых данных
                $this->di->phpmailer->ClearAllRecipients();
                $this->di->phpmailer->ClearAttachments();
                $this->di->phpmailer->ClearCustomHeaders();
                $this->di->phpmailer->ClearReplyTos();

                // Прикрепляем логотип для письма
                $logo_mail = $this->di->cfg->abs_root_path . 'assets/img/logo-mail.png';
                if (is_readable($logo_mail)) {
                    $this->di->phpmailer->AddEmbeddedImage($logo_mail, 'logotype', 'logo-mail.png', 'base64', 'image/png');
                }

                // Обрезаем конечную косую в адресе сайта (если вдруг она там есть)
                if (isset($data['[[SITE_URL]]'])) {
                    $data['[[SITE_URL]]'] = rtrim($data['[[SITE_URL]]'], '/');
                }

                // Связываем данные с шаблоном
                $message  = EmailTemplates::HEADER;
                $message .= strtr(constant('\MFLPHP\Configs\EmailTemplates::' . $message_template), $data);
                $message .= EmailTemplates::FOOTER;

                $this->di->phpmailer->Subject = iconv('utf-8', 'windows-1251', $subject);
                $this->di->phpmailer->MsgHTML(iconv('utf-8', 'windows-1251', $message));
                $this->di->phpmailer->AddAddress($email);

                if (!$this->di->phpmailer->Send()) {
                    $this->di->log->warning('При отправке письма произошла ошибка: "' . $this->di->phpmailer->ErrorInfo . '".');
                    return false;
                } else {
                    return true;
                }
            }
        } else {
            $this->di->log->warning('При отправке письма не найден шаблон: "' . $message_template . '".');
            return false;
        }
    }
}
