<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class PermissionsAndRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        app()['cache']->forget( 'spatie.permission.cache' );

		// create permissions
		$readPermissions  = [
			'read_permission'
		];
		$writePermissions = [
			'write_permission'
		];

		$permissions = array_merge( $readPermissions, $writePermissions );

		foreach ( $permissions as $permission ) {
			Permission::create( [ 'name' => $permission ] );
		}

		// create roles and assign created permissions

		$role = Role::create( [ 'name' => 'user' ] );
		$role->givePermissionTo( array_merge($readPermissions , [

			] ));


		$role = Role::create( [ 'name' => 'admin' ] );
		$role->givePermissionTo( Permission::all() );
	} 
}
