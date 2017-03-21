providers
PopCode\UserRole\Providers\UserRoleServiceProvider::class,

run
`php artisan vendor:publish --provider="PopCode\UserRole\Providers\UserRoleServiceProvider" --tag=congfig`
then edit configuration file

run
`php artisan vendor:publish --provider="PopCode\UserRole\Providers\UserRoleServiceProvider" --tag=migrations`
then run the migration: `php artisan migrate`



- publish migrations, config
- publish seed
- edit seeder, config + composer update
- migrate // calls the seeder

In app/Http/Kernel.php add `'pcauth' => \App\Http\Middlewares\PCAuthenticate::class,` to the $routeMiddleware array

In the $middlewareGroups add `\PopCode\UserRole\Middlewares\RoleMiddleware::class,` to web, and `'pcrole',` to api


