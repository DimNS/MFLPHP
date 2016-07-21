<?php
/**
 * Класс для отправки электронных писем
 *
 * @version ===
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Helpers;

class EmailSender
{
    /**
    * @var object $di Объект контейнера зависимостей
    */
    protected $di;

    /**
     * Конструктор
     *
     * @param object $di Объект контейнера зависимостей
     *
     * @return null
     *
     * @version ===
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
     */
    public function send($email, $subject, $message_template, $data)
    {
        // Проверяем наличие шаблона
        if (isset($this->di->cfg->email_templates[$message_template])) {
            if ('0' === getenv('PRODUCTION') AND '0' === $this->di->auth->config->smtp) {
                return true;
            } else {
                // Очищаемся от старых данных
                $this->di->phpmailer->ClearAllRecipients();
                $this->di->phpmailer->ClearAttachments();
                $this->di->phpmailer->ClearCustomHeaders();
                $this->di->phpmailer->ClearReplyTos();

                // Связываем данные с шаблоном
                $message  = $this->di->cfg->email_templates['header'];
                $message .= strtr($this->di->cfg->email_templates[$message_template], $data);
                $message .= $this->di->cfg->email_templates['footer'];

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
