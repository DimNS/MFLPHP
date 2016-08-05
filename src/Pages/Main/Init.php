<?php
/**
 * Контроллер главной страницы
 *
 * @version 05.08.2016
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
    protected $view_prefix = 'Pages/Main/view_';

    /**
     * Запуск главной страницы
     *
     * @return null
     *
     * @version 05.08.2016
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function start()
    {
        $this->service->uri   = $this->request->uri();
        $this->service->title = $this->di->auth->config->site_name;
        $this->service->userinfo = $this->di->userinfo;

        $this->service->render($this->service->app_root_path . '/' . $this->view_prefix . 'main.php');
    }
}
