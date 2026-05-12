<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
	/**
	 * The directory that holds the Migrations
	 * and Seeds directories.
	 *
	 * @var string
	 */
	public $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

	/**
	 * Lets you choose which connection group to
	 * use if no other is specified.
	 *
	 * @var string
	 */
	public $defaultGroup = 'default';

	/**
	 * The default database connection.
	 *
	 * @var array
	 */
	 public function __construct()
	{
		parent::__construct();

		$host = $_SERVER['HTTP_HOST'] ?? '';

		$this->default = [
			'DSN'      => '',
			'hostname' => 'localhost',
			'hostname' => env('database.default.hostname', 'localhost'),
			'username' => env('database.default.username', 'u124366273_mahakal_blogUS'),
			'password' => env('database.default.password', ']tR9b?p0w'),
			'database' => env('database.default.database', 'u124366273_mahakal_blogDb'),
			'DBDriver' => 'MySQLi',
			'DBPrefix' => '',
			'pConnect' => false,
			'DBDebug'  => (ENVIRONMENT !== 'production'),
			'charset'  => 'utf8',
			'DBCollat' => 'utf8_general_ci',
			'swapPre'  => '',
			'encrypt'  => false,
			'compress' => false,
			'strictOn' => false,
			'failover' => [],
			'port'     => 3306,
		];
	}
	
// 	public $default = [
// 		'DSN'      => '',
// 		'hostname' => 'localhost',
		
// 		'username' => (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'mahakal.com') ? 'u419491130_blog_user' : 'u124366273_mahakal_blogUS',

// 		'password' => (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'mahakal.com') ? '12!@Mahakal' : ']tR9b?p0w',

// 		'database' => (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'mahakal.com')  ? 'u419491130_blog_db'  : 'u124366273_mahakal_blogDb',
			
// // 		'username' => 'u124366273_mahakal_blogUS',
// // 		'password' => ']tR9b?p0w',
// // 		'database' => 'u124366273_mahakal_blogDb',

// 		// 'username' => 'root',
// 		// 'password' => '',
// 		// 'database' => 'mahakal_blog',

// 		'DBDriver' => 'MySQLi',
// 		'DBPrefix' => '',
// 		'pConnect' => false,
// 		'DBDebug'  => (ENVIRONMENT !== 'production'),
// 		'charset'  => 'utf8',
// 		'DBCollat' => 'utf8_general_ci',
// 		'swapPre'  => '',
// 		'encrypt'  => false,
// 		'compress' => false,
// 		'strictOn' => false,
// 		'failover' => [],
// 		'port'     => 3306,
// 	];
}
