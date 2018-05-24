<?php
/**
 * Plugin Name: Users List
 * Plugin URI: 
 * Description: 
 * Author: Kevin Carwile
 * Author URI: 
 * Version: 0.0.2
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

/* Load Only Once */
if ( class_exists( 'MillerMediaUsersListPlugin' ) ) {
	return;
}

/* Autoloaders */
include_once 'includes/plugin-bootstrap.php';

use MillerMedia\UsersList\Models\User;

/**
 * This plugin uses the MWP Application Framework to init.
 *
 * @return void
 */
add_action( 'mwp_framework_init', function() 
{
	/**
	 * Initialize
	 */
	$framework = MWP\Framework\Framework::instance();
	$plugin	= MillerMedia\UsersList\Plugin::instance();
	$framework->attach( $plugin );
	
	/**
	 * Create a controller to manage our users
	 *
	 * - Add it to an admin page
	 * - Make it ajax
	 * - Include a role selection filter
	 * - Allow sorting by user login or display name
	 */
	User::createController( 'admin', array(
		'adminPage' => [
			'title' => 'Users List',
			'type' => 'menu',
			'icon' => 'dashicons-admin-users',
			'position' => 71,
		],
		'tableConfig' => [
			'constructor' => [
				'ajax' => true,
			],
			'columns' => [
				'display_name' => 'Display Name',
				'user_login' => 'Login Name',
				'user_email' => 'Email Address',
				'roles' => 'User Roles',
			],
			'extras' => [
				'role_filter' => [
					'init' => function( $table ) {
						if ( isset( $_REQUEST['role'] ) and $role = $_REQUEST['role'] ) {
							$db = MWP\Framework\Framework::instance()->db();
							$table->addFilter( array( "(SELECT COUNT(*) FROM {$db->base_prefix}usermeta AS meta WHERE meta.user_id=ID AND meta.meta_key='{$db->prefix}capabilities' AND meta.meta_value LIKE %s) > 0", "%\"{$role}\"%" ) );
						}
					},
					'output' => function( $table ) {
						$role = isset( $_REQUEST['role'] ) ? $_REQUEST['role'] : NULL;
						$roles = array_merge( [ '' => 'All Roles' ], wp_roles()->role_names );
						$options = array_map( function( $val, $title ) use ( $role ) { return sprintf( '<option value="%s" %s>%s</option>', $val, $role == $val ? 'selected' : '', $title ); }, array_keys( $roles ), $roles );
						echo 'Filter By Role: <select name="role" onchange="jQuery(this).closest(\'form\').submit()">' . implode( '', $options ) . '</select>';
					},
				],
			],
			'sortable' => [
				'display_name' => [ 'display_name', false ],
				'user_login' => [ 'user_login', false ],
			],
			'handlers' => [
				'roles' => function( $row ) {
					$user = get_user_by('id', $row['ID']);
					$roles = wp_roles();
					return implode(', ', array_map( function( $role ) use ( $roles ) { return $roles->roles[ $role ]['name']; }, $user->roles ) );
				},
			],
			'bulkActions' => [],
		],
	));
	
} );
