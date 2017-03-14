<?php

namespace PopCode\UserRole\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class UserRoleServiceProvider extends ServiceProvider
{
    protected $root;

    /**
     * @var \Carbon\Carbon
     */
    protected $datePrefix;

    public function register() {
        $this->root = __DIR__ . '/../../../';

        $this->config();

        $this->migrations();

        $this->routes();
    }

    protected function config() {
        $this->publishes(
            [
                $this->root . 'config/popcode-userrole.php' => config_path('popcode-userrole.php'),
            ],
            'config'
        );

        // merge configuration
        //$this->mergeConfigFrom($this->root . 'config/popcode-userrole.php', 'popcode-userrole');
    }

    protected function migrations() {
        if (!class_exists('CreateRolesTable')) {
            $this->publishes(
                [
                    $this->root . 'migrations/2017_03_13_141819_create_roles_table.php'    => database_path('migrations/' . $this->getDatePrefix() . '_create_roles_table.php'),
                    $this->root . 'migrations/2017_03_13_150435_call_user_role_seeder.php' => database_path('migrations/' . $this->getDatePrefix() . '_call_user_role_seeder.php'),
                    $this->root . 'migrations/2017_03_14_153037_create_route_x_role_table.php' => database_path('migrations/' . $this->getDatePrefix() . '_create_route_x_role_table.php'),
                ],
                'migrations'
            );
        }
        if (!class_exists('CreateRoleXUserTable')) {
            $this->publishes(
                [
                    $this->root . 'migrations/2017_03_13_155021_create_role_x_user_table.php' => database_path('migrations/' . $this->getDatePrefix() . '_create_role_x_user_table.php'),
                ],
                'migrations'
            );
        }
        $this->publishes(
            [
                $this->root . 'migrations/2017_03_13_155021_create_role_x_user_table.php' => database_path('migrations/' . $this->getDatePrefix() . '_create_role_x_user_table---asd.php'),
            ],
            'migrations'
        );
    }

    protected function routes() {
        if ($this->getConfig('register_default_routes')) {
            // register routes
            $this->loadRoutesFrom($this->root . 'routes/popcode-userrole-routes.php');
        }
    }

    /**
     * Get migration timestamp and add a second to keep their order
     *
     * @return string
     */
    protected function getDatePrefix() {
        if (!$this->datePrefix) {
            $this->datePrefix = Carbon::now();
        }

        $formatted = $this->datePrefix->format('Y_m_d_His');
        $this->datePrefix->addSecond();
        return $formatted;
    }

    protected function getConfig($key, $default = null) {
        if (class_exists('\\Config')) {
            return \Config::get('popcode-userrole.' . $key, $default);
        }
        return $default;
    }

}
