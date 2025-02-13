<?php

namespace App\Console\Commands;

use App\Services\UserService;
use App\Services\RoleService;
use App\Services\SystemService;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installation';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting App Installation...');
        $this->info('Review this installation process in \App\Console\Commands\Install.php');

        sleep(3);

        if (!File::exists('.env')) {
            $this->error('File Not Found: .env');
            $this->error('Aborted');
            return false;
        }

        if (env('DB_PASSWORD', '') == '') {
            $this->error('Database not configured properly');
            $this->error('Aborted');
            return false;
        }

        $container = Container::getInstance();

        $systemService = $container->make(SystemService::class);

        if (!$systemService->checkDBConnection()) {
            $this->error('Database Connection Fail');
            $this->error('Aborted');
            return false;
        }

        if ($systemService->isExistTable('users')) {
            $this->error('Table User Found, Please DROP first');
            $this->error('Aborted');
            return false;
        }

        $this->info('Generating App Key...');
        Artisan::call('key:generate');

        if (App::environment('prod', 'production')) {
            $this->info('[PROD] Database Migrating...');
            Artisan::call('migrate', ['--force' => true,]);
        } else {
            $this->info('Database Migrating...');
            Artisan::call('migrate');
        }

        if (App::environment('prod', 'production')) {
            $this->info('[PROD] Seeding ...');
            exec('php artisan db:seed --force=true');
        } else {
            $this->info('Seeding ...');
            exec('php artisan db:seed');
        }

        $this->info('Storage Linking ...');
        if (is_link(public_path().'/storage')) {
            $this->info('Found Storage Link, Skipping ...');
        } else {
            Artisan::call('storage:link');
        }

        $this->info('Starting NPM Install');
        exec('npm install');

        $this->info('Starting Mix');
        if (App::environment('prod', 'production')) {
            $this->info('Executing for production enviroment');
            exec('npm run prod');
        } else {
            exec('npm run dev');
        }

        $this->info('Creating Admin/Dev Account ...');
        $is_dev = $this->confirm("Are you a developer?", false);

        $userName = 'GitzJoey';
        $userEmail = 'gitzjoey@yahoo.com';
        $userPassword = 'thepassword';

        $valid = false;

        while (!$valid) {
            $userName = $this->ask('Name:', $userName);
            $userEmail = $this->ask('Email:', $userEmail);
            $userPassword = $this->secret('Password:', $userPassword);

            $validator = Validator::make([
                'name' => $userName,
                'email' => $userEmail,
                'password' => $userPassword
            ], [
                'name' => 'required|min:3|max:50',
                'email' => 'required|max:255|email|unique:users,email',
                'password' => 'required|min:7'
            ]);

            if (!$validator->fails()) {
                $valid = true;
            } else {
                foreach ($validator->errors()->all() as $errorMessage) {
                    $this->error($errorMessage);
                }
            }
        }

        $confirmed = $this->confirm("Everything's OK? Do you wish to continue?");

        if (!$confirmed) {
            $this->error('Aborted');
            return false;
        }

        $userService = $container->make(UserService::class);
        $roleService = $container->make(RoleService::class);

        $rolesId = [];
        if ($is_dev) {
            array_push($rolesId, $roleService->getRoleByName(Config::get('const.DEFAULT.ROLE.DEV'))->id);
        } else {
            array_push($rolesId, $roleService->getRoleByName(Config::get('const.DEFAULT.ROLE.ADMIN'))->id);
        }

        $profile = [
            'first_name' => $userName,
            'status' => 1
        ];

        $userService->create(
            $userName,
            $userEmail,
            $userPassword,
            $rolesId,
            $profile,
        );

        sleep(3);

        $this->info('Done!');
    }
}
