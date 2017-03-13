<?php

use Illuminate\Database\Seeder;

class PCUserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'id'    => 1,
                'label' => 'registered',
                'title' => 'Registered',
            ],
            [
                'id'    => 2,
                'label' => 'admin',
                'title' => 'Administrator',
            ],
        ];

        $model = Config::get('popcode-userrole.model', 'PopCode\\UserRole\\Models\\Role');

        if (!class_exists($model)) {
            $this->command->info('No model found in ' . get_class() . '!');
            return;
        }

        foreach ($roles as $role) {
            /* @var PopCode\UserRole\Models\Role $instance */
            $instance = new $model;
            $item = $instance->findOrNew($role['id'], $role);
            $item->id = $role['id'];
            $item->save();
        }
    }
}
