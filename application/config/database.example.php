<?php
defined('BASEPATH') or exit('No direct script access allowed');

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'myapp'; // database akan otomatis dibuat

$db_host_sipp = 'localhost';
$db_user_sipp = 'root';
$db_pass_sipp = '';
$db_name_sipp = 'sipp';




// LOGIC CREATE DB & TABLES
// BISA DI-COMMENT KALAU DATABASE DAN TABEL SUDAH DIBUAT

$mysqli = new mysqli($db_host, $db_user, $db_pass);
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}
$result = $mysqli->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db_name'");
if ($result && $result->num_rows === 0) {
	$mysqli->query("CREATE DATABASE `$db_name` CHARACTER SET utf8 COLLATE utf8_general_ci");
}
$mysqli->select_db($db_name);

$tables_result = $mysqli->query("SHOW TABLES");
$existing_tables = [];
while ($row = $tables_result->fetch_array()) {
    $existing_tables[] = $row[0];
}

if (!in_array('ci_sessions', $existing_tables)) {
	$mysqli->query("
        CREATE TABLE `ci_sessions` (
          `id` VARCHAR(128) NOT NULL,
          `ip_address` VARCHAR(45) NOT NULL,
          `timestamp` INT(10) UNSIGNED NOT NULL DEFAULT '0',
          `data` BLOB NOT NULL,
          PRIMARY KEY (`id`, `ip_address`),
          INDEX `ci_sessions_timestamp` (`timestamp`)
        )
        COLLATE='latin1_swedish_ci'
        ENGINE=InnoDB
    ");
}

if (!in_array('users', $existing_tables)) {
	$mysqli->query("
        CREATE TABLE `users` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `username` VARCHAR(100) NULL DEFAULT NULL,
            `password` VARCHAR(255) NOT NULL,
            `email` VARCHAR(254) NOT NULL,
            `created_on` INT(11) UNSIGNED NOT NULL,
            `active` TINYINT(1) UNSIGNED NULL DEFAULT NULL,
            `nama_lengkap` VARCHAR(100) NULL DEFAULT NULL,
            `gelar_depan` VARCHAR(50) NULL DEFAULT NULL,
            `gelar_belakang` VARCHAR(50) NULL DEFAULT NULL,
            `nip` VARCHAR(18) NULL DEFAULT NULL,
            `nik` VARCHAR(16) NULL DEFAULT NULL,
            `birth_place_code` VARCHAR(13) NULL DEFAULT NULL,
            `birth_date` DATE NULL DEFAULT NULL,
            `jenis_kelamin` ENUM('L','P') NULL DEFAULT NULL,
            `tingkat_pendidikan_id` INT(4) NULL DEFAULT NULL,
            `jabatan_id` INT(4) NULL DEFAULT NULL,
            `golongan_ruang_id` INT(4) NULL DEFAULT NULL,
            `phone` VARCHAR(20) NULL DEFAULT NULL,
            `photo` VARCHAR(50) NULL DEFAULT NULL,
            `start_date` DATE NULL DEFAULT NULL,
            `ttd` VARCHAR(50) NULL DEFAULT NULL,
            `struktur_organisasi_id` INT(4) NULL DEFAULT NULL,
            `ip_address` VARCHAR(45) NULL DEFAULT NULL,
            `activation_selector` VARCHAR(255) NULL DEFAULT NULL,
            `activation_code` VARCHAR(255) NULL DEFAULT NULL,
            `forgotten_password_selector` VARCHAR(255) NULL DEFAULT NULL,
            `forgotten_password_code` VARCHAR(255) NULL DEFAULT NULL,
            `forgotten_password_time` INT(11) UNSIGNED NULL DEFAULT NULL,
            `remember_selector` VARCHAR(255) NULL DEFAULT NULL,
            `remember_code` VARCHAR(255) NULL DEFAULT NULL,
            `last_login` INT(11) UNSIGNED NULL DEFAULT NULL,
            PRIMARY KEY (`id`) USING BTREE,
            UNIQUE INDEX `uc_email` (`email`) USING BTREE,
            UNIQUE INDEX `uc_activation_selector` (`activation_selector`) USING BTREE,
            UNIQUE INDEX `uc_forgotten_password_selector` (`forgotten_password_selector`) USING BTREE,
            UNIQUE INDEX `uc_remember_selector` (`remember_selector`) USING BTREE,
            UNIQUE INDEX `uc_username` (`username`) USING BTREE,
            INDEX `FK_users_tref_jabatan` (`jabatan_id`) USING BTREE,
            INDEX `FK_users_tref_tingkat_pendidikan` (`tingkat_pendidikan_id`) USING BTREE,
            INDEX `FK_users_tref_golongan_ruang` (`golongan_ruang_id`) USING BTREE,
            INDEX `FK_users_tmst_struktur_organisasi` (`struktur_organisasi_id`)
        )
        COLLATE='utf8_general_ci'
        ENGINE=InnoDB
    ");
}

if (!in_array('tmst_configs', $existing_tables)) {
	$mysqli->query("
        CREATE TABLE `tmst_configs` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `key` varchar(50) DEFAULT NULL,
          `value` varchar(250) DEFAULT NULL,
          `category` tinyint(4) DEFAULT NULL,
          `note` varchar(250) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1
    ");
	$mysqli->query("DELETE FROM `tmst_configs`");
	$mysqli->query("INSERT INTO `tmst_configs` (`id`, `key`, `value`, `category`, `note`) VALUES (1, 'APP_VERSION', '1', 5, 'string. versi aplikasi')");
	$mysqli->query("INSERT INTO `tmst_configs` (`id`, `key`, `value`, `category`, `note`) VALUES (2, 'APP_NAME', 'Aplikasiku', 5, 'string. nama aplikasi')");
	$mysqli->query("INSERT INTO `tmst_configs` (`id`, `key`, `value`, `category`, `note`) VALUES (3, 'APP_SHORT_NAME', 'MY APP', 5, 'string. nama pendek aplikasi')");
	$mysqli->query("INSERT INTO `tmst_configs` (`id`, `key`, `value`, `category`, `note`) VALUES (4, 'SATKER_NAME', 'Pengadilan ...', 1, 'string. nama satker')");
	$mysqli->query("INSERT INTO `tmst_configs` (`id`, `key`, `value`, `category`, `note`) VALUES (5, 'SATKER_ADDRESS', 'Jl. ...', 5, 'string. alamat kantor')");
	$mysqli->query("INSERT INTO `tmst_configs` (`id`, `key`, `value`, `category`, `note`) VALUES (6, 'DIALOGWA_API_URL', 'https://dialogwa.web.id/api', 5, 'string. url api dialogwa.id')");
	$mysqli->query("INSERT INTO `tmst_configs` (`id`, `key`, `value`, `category`, `note`) VALUES (7, 'DIALOGWA_TOKEN', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjY1ZjNiMjIyZWY1MmJjMzc4MDYxM2U1OSIsInVzZXJuYW1lIjoiY2hhbmRyYSIsImlhdCI6MTcxNzc0Nzc4NywiZXhwIjo0ODczNTA3Nzg3fQ.KIqEs7rELJzVj2hk6WJqCiYy0T0Mz7G5vbiy4gFLRQ0', 5, 'string. token dialogwa.id')");
	$mysqli->query("INSERT INTO `tmst_configs` (`id`, `key`, `value`, `category`, `note`) VALUES (8, 'DIALOGWA_SESSION', 'demo', 5, 'string. sesi dialogwa.id')");
	$mysqli->query("INSERT INTO `tmst_configs` (`id`, `key`, `value`, `category`, `note`) VALUES (9, 'WA_TEST_TARGET', '', 5, 'string. no whatsapp untuk tes menerima notifikasi')");
}

if (!in_array('trans_whatsapp_message', $existing_tables)) {
	$mysqli->query("
        CREATE TABLE `trans_whatsapp_message` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `sent_time` DATETIME NOT NULL,
            `sent_by` VARCHAR(50) NOT NULL,
            `phone_number` VARCHAR(20) NOT NULL,
            `type` VARCHAR(50) NOT NULL,
            `reference` VARCHAR(50) NOT NULL,
            `perkara_id` VARCHAR(50) NOT NULL,
            `callback` VARCHAR(50) NOT NULL,
            `text` TEXT NOT NULL,
            `success` TINYINT(1) NOT NULL DEFAULT '0',
            `note` VARCHAR(100) NOT NULL,
            PRIMARY KEY (`id`) USING BTREE
        )
        COLLATE='latin1_swedish_ci'
        ENGINE=InnoDB
        AUTO_INCREMENT=3052
    ");
}
$mysqli->close();

// END OF LOGIC CREATE DB & TABLES

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['dsn']      The full DSN string describe a connection to the database.
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database driver. e.g.: mysqli.
|			Currently supported:
|				 cubrid, ibase, mssql, mysql, mysqli, oci8,
|				 odbc, pdo, postgre, sqlite, sqlite3, sqlsrv
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Query Builder class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['encrypt']  Whether or not to use an encrypted connection.
|
|			'mysql' (deprecated), 'sqlsrv' and 'pdo/sqlsrv' drivers accept TRUE/FALSE
|			'mysqli' and 'pdo/mysql' drivers accept an array with the following options:
|
|				'ssl_key'    - Path to the private key file
|				'ssl_cert'   - Path to the public key certificate file
|				'ssl_ca'     - Path to the certificate authority file
|				'ssl_capath' - Path to a directory containing trusted CA certificats in PEM format
|				'ssl_cipher' - List of *allowed* ciphers to be used for the encryption, separated by colons (':')
|				'ssl_verify' - TRUE/FALSE; Whether verify the server certificate or not ('mysqli' only)
|
|	['compress'] Whether or not to use client compression (MySQL only)
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|	['ssl_options']	Used to set various SSL options that can be used when making SSL connections.
|	['failover'] array - A array with 0 or more data for connections if the main should fail.
|	['save_queries'] TRUE/FALSE - Whether to "save" all executed queries.
| 				NOTE: Disabling this will also effectively disable both
| 				$this->db->last_query() and profiling of DB queries.
| 				When you run a query, with this setting set to TRUE (default),
| 				CodeIgniter will store the SQL statement for debugging purposes.
| 				However, this may cause high memory usage, especially if you run
| 				a lot of SQL queries ... disable this to avoid that problem.
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $query_builder variables lets you determine whether or not to load
| the query builder class.
*/
$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => $db_host, // JANGAN DIUBAH
	'database' => $db_name, // JANGAN DIUBAH
	'username' => $db_user, // JANGAN DIUBAH
	'password' => $db_pass, // JANGAN DIUBAH
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => FALSE,
	// 'db_debug' => (ENVIRONMENT !== 'development'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => (ENVIRONMENT === 'development')
);

$db['db_sipp'] = array(
	'dsn'	=> '',
	'hostname' => $db_host_sipp, // JANGAN DIUBAH
	'database' => $db_name_sipp, // JANGAN DIUBAH
	'username' => $db_user_sipp, // JANGAN DIUBAH
	'password' => $db_pass_sipp, // JANGAN DIUBAH
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'development'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => (ENVIRONMENT === 'development')
);
