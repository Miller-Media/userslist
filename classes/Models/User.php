<?php
/**
 * Plugin Class File
 *
 * Created:   May 21, 2018
 *
 * @package:  Users List
 * @author:   Kevin Carwile
 * @since:    0.0.1
 */
namespace MillerMedia\UsersList\Models;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

use MWP\Framework\Pattern\ActiveRecord;

/**
 * User Class
 */
class _User extends ActiveRecord
{
    /**
     * @var    array        Required for all active record classes
     */
    protected static $multitons = array();

    /**
     * @var    string        Table name
     */
    public static $table = "users";

    /**
     * @var    array        Table columns
     */
    public static $columns = array(
        'ID' => [ 'type' => 'bigint', 'length' => 20 ],
        'user_login' => [ 'type' => 'varchar', 'length' => 60 ],
        'user_pass' => [ 'type' => 'varchar', 'length' => 255, 'edit' => false ],
        'user_nicename' => [ 'type' => 'varchar', 'length' => 50, 'edit' => false ],
		'user_email' => [ 'type' => 'varchar', 'length' => 100 ],
		'user_url' => [ 'type' => 'varchar', 'length' => 100 ],
		'user_registered' => [ 'type' => 'datetime' ],
		'user_activation_key' => [ 'type' => 'varchar', 'length' => 255, 'edit' => false ],
		'user_status' => [ 'type' => 'int', 'length' => 11, 'edit' => false ],
		'display_name' => [ 'type' => 'varchar', 'length' => 250 ],
    );

    /**
     * @var    string        Table primary key
     */
    public static $key = 'ID';

    /**
     * @var    string        Table column prefix
     */
    public static $prefix = '';

    /**
     * @var bool        Separate table per site?
     */
    public static $site_specific = FALSE;

    /**
     * @var string      The class of the managing plugin
     */
    public static $plugin_class = 'MillerMedia\UsersList\Plugin';
	
	public static $lang_singular = 'User';
	public static $lang_plural = 'Users';
	
	/**
	 * Save record
	 * 
	 * @return	bool|WP_Error
	 */
	public function save()
	{
		if ( $this->ID ) {
			if ( ! is_wp_error( $result = wp_update_user( $this->dataArray() ) ) ) {
				return true;
			}
			
			return $result;
			
		} else {
			if ( ! is_wp_error( $id = wp_insert_user( $this->dataArray() ) ) ) {
				$this->ID = $id;
				return true;
			}
			
			return $id;
		}
		
		return false;
	}
	
	/**
	 * Save record
	 * 
	 * @return	bool|WP_Error
	 */
	public function delete()
	{
		if ( $this->ID ) {
			return wp_delete_user( $this->ID );
		} 
		
		return false;
	}
	
}
