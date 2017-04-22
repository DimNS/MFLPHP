<?php
/**
 * Инициализация и запуск приложения
 *
 * @version 22.04.2017
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP;

use MFLPHP\Configs\Settings;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Init
{
    /**
     * CSRF-токен
     *
     * @var string
     */
    protected $csrf_token;

    //
    //
    //               mm                       mm
    //               MM                       MM
    //     ,pP"Ybd mmMMmm  ,6"Yb.  `7Mb,od8 mmMMmm
    //     8I   `"   MM   8)   MM    MM' "'   MM
    //     `YMMMa.   MM    ,pm9MM    MM       MM
    //     L.   I8   MM   8M   MM    MM       MM
    //     M9mmmP'   `Mbmo`Moo9^Yo..JMML.     `Mbmo
    //
    //

    /**
     * Старт приложения
     *
     * @return null
     *
     * @version 22.04.2017
     * @author Дмитрий Щербаков <atomcms@ya.ru>
     */
    public function start()
    {
        //
        //                                     ,,
        //                       mm     mm     db
        //                       MM     MM
        //     ,pP"Ybd  .gP"Ya mmMMmm mmMMmm `7MM  `7MMpMMMb.  .P"Ybmmm ,pP"Ybd
        //     8I   `" ,M'   Yb  MM     MM     MM    MM    MM :MI  I8   8I   `"
        //     `YMMMa. 8M""""""  MM     MM     MM    MM    MM  WmmmP"   `YMMMa.
        //     L.   I8 YM.    ,  MM     MM     MM    MM    MM 8M        L.   I8
        //     M9mmmP'  `Mbmmd'  `Mbmo  `Mbmo.JMML..JMML  JMML.YMMMMMb  M9mmmP'
        //                                                    6'     dP
        //                                                    Ybmmmd'

        // Устанавливаем часовой пояс по Гринвичу
        date_default_timezone_set(Settings::TIMEZONE);

        // Где будут хранится php сессии (в файлах или в БД)
        if (Settings::PHP_SESSION === 'DB') {
            $session = new \Zebra_Session(
                mysqli_connect(
                    Settings::DB_HOST,
                    Settings::DB_USER,
                    Settings::DB_PASSWORD,
                    Settings::DB_DATABASE,
                    Settings::DB_PORT
                ), 'AVuVqYR6uwgEuhV79tln0tlKk'
            );
        } else {
            session_start();
        }

        // Включим страницу с ошибками, если включен режим DEBUG
        if (Settings::DEBUG === true) {
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
        }

        // Настраиваем соединение с БД
        \ORM::configure([
            'connection_string' => 'mysql:host=' . Settings::DB_HOST . ';port=' . Settings::DB_PORT . ';dbname=' . Settings::DB_DATABASE,
            'username'          => Settings::DB_USER,
            'password'          => Settings::DB_PASSWORD,
            'logging'           => Settings::DEBUG,
            'driver_options'    => [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            ],
        ]);

        // Инициализируем CSRF-токен
        $csrf = new \DimNS\SimpleCSRF();
        $this->csrf_token = $csrf->getToken();

        // Определим корневую папку, если переменная не пустая
        if (Settings::PATH_SHORT_ROOT != '/') {
            $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen(Settings::PATH_SHORT_ROOT));
        }

        // Инициируем роутер
        $klein = new \Klein\Klein();

        //
        //
        //     `7MM"""Yb. `7MMF'
        //       MM    `Yb. MM
        //       MM     `Mb MM
        //       MM      MM MM
        //       MM     ,MP MM
        //       MM    ,dP' MM
        //     .JMMmmmdP' .JMML.
        //
        //

        // Создаем DI
        $klein->respond(function ($request, $response, $service, $di) use ($csrf) {
            // Регистрируем доступ к Carbon
            $di->register('carbon', function () use ($di) {
                $carbon = Carbon::now(Settings::TIMEZONE);
                $carbon->setLocale('ru');

                return $carbon;
            });

            // Регистрируем доступ к настройкам
            $di->register('cfg', function() {
                return new \MFLPHP\Configs\Config();
            });

            // Регистрируем доступ к управлению пользователем
            $di->register('auth', function() {
                $dbh = new \PDO('mysql:host=' . Settings::DB_HOST . ';port=' . Settings::DB_PORT . ';dbname=' . Settings::DB_DATABASE,
                    Settings::DB_USER,
                    Settings::DB_PASSWORD,
                    [
                        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                        \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                    ]
                );
                return new \PHPAuth\Auth($dbh, new \PHPAuth\Config($dbh, 'phpauth_config'), 'ru_RU');
            });

            // Регистрируем доступ к информации о пользователе
            $di->register('userinfo', function() use ($di) {
                if ($di->auth->isLogged()) {
                    $user_id = $di->auth->getSessionUID($di->auth->getSessionHash());

                    $user_info = \ORM::for_table('users')
                        ->join('users_info', array('users.id', '=', 'users_info.uid'))
                        ->where_equal('id', $user_id)
                        ->find_one();
                    if (is_object($user_info)) {
                        return $user_info;
                    }
                }

                return false;
            });

            // Регистрируем доступ к PHPMailer
            $di->register('phpmailer', function() use ($di) {
                $phpmailer = new \PHPMailer();

                $phpmailer->setLanguage('ru', $di->cfg->abs_root_path . 'vendor/phpmailer/phpmailer/language/');
                $phpmailer->IsHTML(true);
                $phpmailer->CharSet = 'windows-1251';
                $phpmailer->From = $di->auth->config->site_email;
                $phpmailer->FromName = iconv('utf-8', 'windows-1251', $di->auth->config->site_name);

                if ('1' == $di->auth->config->smtp) {
                    $phpmailer->IsSMTP();
                    $phpmailer->SMTPDebug  = 0;
                    $phpmailer->SMTPAuth   = true;
                    $phpmailer->SMTPSecure = $di->auth->config->smtp_security;
                    $phpmailer->Host       = $di->auth->config->smtp_host;
                    $phpmailer->Port       = $di->auth->config->smtp_port;
                    $phpmailer->Username   = $di->auth->config->smtp_username;
                    $phpmailer->Password   = $di->auth->config->smtp_password;
                }

                return $phpmailer;
            });

            // Регистрируем доступ к отправке почты
            $di->register('mail', function() use ($di) {
                return new \MFLPHP\Helpers\EmailSender($di);
            });

            // Регистрируем доступ к логгеру Monolog
            $di->register('log', function() use ($di) {
                $log = new \Monolog\Logger('MainLog');
                $log->pushHandler(new \Monolog\Handler\StreamHandler($di->cfg->abs_root_path . 'errors.log', \Monolog\Logger::DEBUG));
                return $log;
            });

            // Регистрируем доступ к проверке CSRF-токена
            $di->register('csrf', function() use ($csrf) {
                return $csrf;
            });

            $views_path = $_SERVER['DOCUMENT_ROOT'] . Settings::PATH_SHORT_ROOT . 'app/Views/';

            $service->layout($views_path . 'layout-default.php');

            $service->csrf_token    = $this->csrf_token;
            $service->path          = Settings::PATH_SHORT_ROOT;
            $service->app_root_path = $_SERVER['DOCUMENT_ROOT'] . Settings::PATH_SHORT_ROOT . 'app';
            $service->uri           = $request->uri();
        });

        //
        //
        //                                  mm
        //                                  MM
        //     `7Mb,od8 ,pW"Wq.`7MM  `7MM mmMMmm .gP"Ya  ,pP"Ybd
        //       MM' "'6W'   `Wb MM    MM   MM  ,M'   Yb 8I   `"
        //       MM    8M     M8 MM    MM   MM  8M"""""" `YMMMa.
        //       MM    YA.   ,A9 MM    MM   MM  YM.    , L.   I8
        //     .JMML.   `Ybmd9'  `Mbod"YML. `Mbmo`Mbmmd' M9mmmP'
        //
        //
        require_once $_SERVER['DOCUMENT_ROOT'] . Settings::PATH_SHORT_ROOT . 'app/Routes.php';

        //
        //
        //
        //
        //     `7Mb,od8 `7MM  `7MM  `7MMpMMMb.
        //       MM' "'   MM    MM    MM    MM
        //       MM       MM    MM    MM    MM
        //       MM       MM    MM    MM    MM
        //     .JMML.     `Mbod"YML..JMML  JMML.
        //
        //
        $klein->dispatch();
    }
}
