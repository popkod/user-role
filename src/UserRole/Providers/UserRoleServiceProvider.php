<?php

namespace PopCode\UserRole\Providers;

use Illuminate\Support\ServiceProvider;
use Config;
use Carbon\Carbon;

class UserRoleServiceProvider extends ServiceProvider
{
    protected $root;

    /**
     * @var \Carbon\Carbon
     */
    protected $datePrefix;

    public function boot() {

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
        $this->mergeConfigFrom($this->root . 'config/popcode-userrole.php', 'popcode-userrole');
    }

    protected function migrations() {
        if (!class_exists('CreateRolesTable')) {
            $this->publishes(
                [
                    $this->root . 'migrations/2017_03_13_141819_create_roles_table.php'    => database_path('migrations/' . $this->getDatePrefix() . '_create_roles_table.php'),
                    $this->root . 'migrations/2017_03_13_150435_call_user_role_seeder.php' => database_path('migrations/' . $this->getDatePrefix() . '_call_user_role_seeder.php'),
                ],
                'migrations'
            );
        }
    }

    protected function routes() {
        if (Config::get('popcode-userrole.register_default_routes')) {
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
}
