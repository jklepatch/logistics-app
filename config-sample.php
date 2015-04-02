<?php
/**
 * @since 1.0
 *
 * Define constants
 *
 * Includes:
 *   - Database credentials
 *   - FTP credentials
 *   - file directories
 */

//Local Database credentials
define( 'DB_HOST', 		'');
define( 'DB_NAME', 		'');
define( 'DB_USERNAME', 	'');
define( 'DB_PASSWORD', 	'');

//FTP credentials
define( 'FTP_SERVER', 	'' );
define( 'FTP_USERNAME', '' );
define( 'FTP_PASSWORD', '' );

//File directories (suggested file structure, but can be modified)
define( 'LOCAL_ROOT_DIR',       dirname( __FILE__ ) ); 						// __DIR__ gives same result, but only available in php 5.3 >= 
define( 'LOCAL_DOWNLOADS_DIR',  LOCAL_ROOT_DIR . '\ftp-files' );			//Root directory of ftp files downloads
define( 'LOCAL_EXPORT_DIR',     LOCAL_ROOT_DIR . '\ftp-files\export' ); 	//Unused
define( 'LOCAL_APP_DIR',        LOCAL_ROOT_DIR . '\app' );					//Root directory of the app
define( 'LOCAL_APP_URI',        dirname( $_SERVER['SCRIPT_NAME'] ) . '/' ); //Root URI of the app
define( 'LOCAL_INC_DIR',        LOCAL_APP_DIR . '\inc' );					//Directory where controllers load most of the required php files
define( 'LOCAL_VIEWS_DIR',      LOCAL_APP_DIR . '\views' );					//Directory where app views are stored
define( 'LOCAL_MODELS_DIR',     LOCAL_APP_DIR . '\models' );				//Directory where app models are stored, as well as databases classes and functions

//Constant used to check if the app was started the normal way (calling index.php in project root)
define( 'NORMAL_LAUNCH', true );

//Change max execution time if you have problems during data update
ini_set( 'max_execution_time', 300 );



