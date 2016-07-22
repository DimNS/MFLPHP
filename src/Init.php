<?php
/**
 * Инициализация и запуск приложения
 *
 * @version ===
 * @author Дмитрий Щербаков <atomcms@ya.ru>
 */

namespace MFLPHP;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Init
{
    /**
     * Путь до файла .env
     *
     * @var string
     */
    private $path_to_env;

    /**
     * CSRF-токен
     *
     * @var string
     */
    private $csrf_token;

    /**
     * Конструктор
     *
     * @param string $path_to_env    Путь до файла .env
     */
    public function __construct($path_to_env)
    {
        $this->path_to_env = $path_to_env;
    }

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
     * @version ===
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
        date_default_timezone_set('UTC');

        // Подключим файл с настройками
        $dotenv = new \Dotenv\Dotenv($this->path_to_env, '.env');
        $dotenv->load();

        // Включим страницу с ошибками, если включен режим DEBUG
        if (getenv('DEBUG') === '1') {
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
        }

        // Настраиваем соединение с БД
        \ORM::configure([
            'connection_string' => 'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
            'username' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'driver_options' => [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            ],
        ]);

        // Инициализируем CSRF-токен
        $csrf = new \DimNS\SimpleCSRF(uniqid());
        $this->csrf_token = $csrf->generateToken();

        // Определим корневую папку, если переменная не пустая
        if (getenv('PATH_SHORT_ROOT') !== '') {
            // Проверяем чтобы в начале была косая
            if (getenv('PATH_SHORT_ROOT')[0] !== '/') {
                putenv('PATH_SHORT_ROOT=/' . getenv('PATH_SHORT_ROOT'));
            }

            // Проверяем чтобы в конце не было косой
            putenv('PATH_SHORT_ROOT=' . rtrim(getenv('PATH_SHORT_ROOT'), '/'));

            $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen(getenv('PATH_SHORT_ROOT')));
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
        $klein->respond(function ($request, $response, $service, $di) {
            // Регистрируем доступ к настройкам
            $di->register('cfg', function() {
                return new \MFLPHP\Configs\Config();
            });

            // Регистрируем доступ к управлению пользователем
            $di->register('auth', function() {
                $dbh = new \PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
                    getenv('DB_USER'),
                    getenv('DB_PASSWORD'),
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

                $phpmailer->setLanguage('ru', $di->cfg->abs_root_path . '/vendor/phpmailer/phpmailer/language/');
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
                $log->pushHandler(new \Monolog\Handler\StreamHandler($di->cfg->abs_root_path . '/errors.log', \Monolog\Logger::WARNING));
                return $log;
            });

            $service->layout($_SERVER['DOCUMENT_ROOT'] . getenv('PATH_SHORT_ROOT') . '/app/Views/layout-default.php');

            $service->csrf_token = $this->csrf_token;
            $service->path       = getenv('PATH_SHORT_ROOT');
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
        require_once $_SERVER['DOCUMENT_ROOT'] . getenv('PATH_SHORT_ROOT') . '/app/Routes.php';

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
