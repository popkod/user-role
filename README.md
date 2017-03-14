providers
PopCode\UserRole\Providers\UserRoleServiceProvider::class,

run
`php artisan vendor:publish --provider="PopCode\UserRole\Providers\UserRoleServiceProvider" --tag=congfig`
then edit configuration file

run
`php artisan vendor:publish --provider="PopCode\UserRole\Providers\UserRoleServiceProvider" --tag=migrations`
then run the migration: `php artisan migrate`



- publish migrations
- publish seed
- edit seeder + composer update
- migrate // calls the seeder

