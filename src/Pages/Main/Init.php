<?php
/**
 * Контроллер главной страницы
 *
 * @version ===
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP\Pages\Main;

class Init extends \MFLPHP\Abstracts\PageController
{
    /**
     * Префикс для подстановки в путь до представления
     *
     * @var string
     */
    private $view_prefix = 'Pages/Main/view_';

    /**
     * Запуск главной страницы
     *
     * @return null
     *
     * @version ===
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function start()
    {
        if ($this->di->auth->isLogged()) {
            $this->service->userinfo = $this->di->userinfo;

            $template_file = $this->view_prefix . 'main';
        } else {
            $this->service->external_page = true;
            $this->service->message_code  = 'info';
            $this->service->message_text  = 'Необходимо войти в систему.';

            $template_file = 'Pages/User/view_auth';
        }

        $this->service->uri   = $this->request->uri();
        $this->service->title = $this->di->auth->config->site_name;

        $this->service->render($this->service->app_root_path . '/' . $template_file . '.php');
    }
}
