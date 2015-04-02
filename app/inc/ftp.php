<?php
/**
  * @since 1.0
  * 
  * Handles file downloads from ftp server
  *
  * Local files path
  * 	LOCAL_UPDATE_FILES_PATH => 	Where updated files from ftp will be downloaded.
  *									Updates are based on filename. Only file whose 
  *									ftp filename are not found in FTP_UPDATE_FILES_PATH
  *									will be downloaded to FTP_UPDATE_FILES_PATH
  *
  *		LOCAL_TMP_FILE_PATH =>		Where the concatenated content of the last
  *									updated file is. The content of this file
  *									is cleared at every new update
  *
  *		LOCAL_GLOBAL_FILE_PATH => 	Where the concatenated content of all updates
  *									is. New content is added at each new update.
  *									Content of this file should be same to 
  *									database. The content of this file
  *									is never cleared.									
  */

	
/**
  * @since 1.0
  * 
  * @param (array) $args - 
  * @return void
  */
function download_ftp_files( $args ) {
	
	if( !isset( $args ) ) {
		return;
	}

	//Check and setup arguments
	$ftp_conn_id			= 'ftp_conn_id';
	$ftp_directory			= isset( $args['ftp_directory'] ) 		     ? $args['ftp_directory'] 		 : '';
	$ftp_file_regex 		= isset( $args['ftp_file_regex'] ) 		     ? $args['ftp_file_regex'] 		 : '';
	$ftp_file_fields        = isset( $args['ftp_file_fields'] )     ? $args['ftp_file_fields'] : '';
	$ftp_file_fields_eol    = isset( $args['ftp_fields_eol'] ) ? $args['ftp_fields_eol'] : PHP_EOL;
	$ftp_file_fields        = implode( ';', $ftp_file_fields);
	$ftp_file_fields        = $ftp_file_fields. $ftp_file_fields_eol;
	$local_dir 				= isset( $args['local_directory'] ) 	     ? $args['local_directory']  	 : '';
	$local_tmp_file_path 	= isset( $args['local_tmp_file_path'] )      ? $args['local_tmp_file_path']  : '';
	$add_modified_date		= isset( $args['add_modified_date'] ) 	     ? $args['add_modified_date'] 	 : FALSE ;
	$add_hash_id		    = isset( $args['add_hash_id'] ) 	         ? $args['add_hash_id'] 	     : FALSE ;
	$no_ftp_header		    = isset( $args['no_ftp_header'] ) 	         ? $args['no_ftp_header'] 	     : FALSE ;
	//dd( $add_modified_date, 'add' );
	//dd( $args, 'args' );

	// set up basic connection
	$ftp_conn_id  = ftp_connect( FTP_SERVER )                             or die("Couldn't connect to $ftp_server - Check out ftp_server variable");
	$login_result = ftp_login( $ftp_conn_id, FTP_USERNAME, FTP_PASSWORD ) or die("Couldn't connect to $ftp_server - Check out ftp_username / ftp_password variables");

	//Setup passive mode
	ftp_pasv( $ftp_conn_id, true ); 

	//Clear the tmp file
	file_put_contents( $local_tmp_file_path, '');

	//Download list of file names from ftp as an array
	$ftp_dir_list = ftp_nlist( $ftp_conn_id, $ftp_directory );

	/**
	 * If non empty, download each file of the array
	 *
	 * For each file, if :
	 *   a) it matches the regex ($ftp_file_regex),
	 *   b) AND the file does not exist in $local_dir
	 *      (i.e it is a new file not yet downloaded)
	 * The ftp file will be downloaded to $local_dir and
	 * its content will be appended to a local tmp file
	 * (i.e tmp file will contain all latest files,
	 * ready to be used for database update
	 *
	 * Finally the headers from the local tmp file will
	 * be removed (because of all the copy paste from
	 * downloaded file, it will contain several headers,
	 * need to be corrected)
	 */
	if( !empty( $ftp_dir_list ) && count( $ftp_dir_list ) > 0 ) {

		//Loop through each file in $ftp_dir_list
		foreach($ftp_dir_list as $ftp_file_path) {

			//Get the name of the current ftp file (only filename, no path)
	  		$ftp_file_path_array = explode( '/', $ftp_file_path );
	  		$local_file_path     =  $local_dir . '\\' . end( $ftp_file_path_array );
			
		  	if( preg_match( $ftp_file_regex, $ftp_file_path ) && !file_exists( $local_file_path ) ) {

	  			//Download current ftp file in $local_dir and append content to tmp file
	  			ftp_get( $ftp_conn_id, $local_file_path, $ftp_file_path, FTP_BINARY );

			    //Add modified date if demanded
			    if( TRUE === $add_modified_date ) {
			    	add_file_modified_date( $ftp_conn_id, $ftp_file_path, $ftp_file_fields, $no_ftp_header, $local_file_path );
			    }

			    //Add id if demanded
			    if( TRUE === $add_hash_id ) {
			    	add_hash_id( $ftp_conn_id, $ftp_file_path, $ftp_file_fields, $no_ftp_header, $local_file_path );
			    }  

			    //Append content to tmp file
			    $local_file = file_get_contents( $local_file_path );
			    // if( 
			    file_put_contents( $local_tmp_file_path, $local_file, FILE_APPEND );

			    //Remove header line added, if ftp update files contain headers
			    if( $ftp_file_fields<> '' && FALSE === $no_ftp_header) {
			    	file_put_contents( $local_tmp_file_path, str_replace( $ftp_file_fields, '', file_get_contents( $local_tmp_file_path ) ) );
			    } 
		    }
		}
	}

	ftp_close( $ftp_conn_id );
}

/**
  * @since 1.0
  *
  * Add a field 'modified_date' to downloaded ftp files
  *
  * Is used for Remarks and Stock ftp files who misses this field
  * Is called if download_ftp_files() if called with $add_modified_date === TRUE
  * Will add a header if $no_ftp_header === FALSE
  * 
  * @param (string) $ftp_conn_id     - handler for ftp connextion
  * @param (string) $ftp_file_path   - path to remote ftp file
  * @param (string) $ftp_file_fields - header of the remote ftp file
  * @param (string) $local_file_path - where the remote ftp file will be downloaded on local
  * @return void
  */
function add_file_modified_date( $ftp_conn_id, $ftp_file_path, $no_ftp_header, $local_file_path ) {

	//get modified date from ftp and format it for mysql
	$modified_date = ftp_mdtm( $ftp_conn_id, $ftp_file_path);
	if ( $modified_date == -1) {
		$modified_date = 'error';
	} else {
		$modified_date = date( 'Y-m-d', $modified_date );
	}

	//append modified date column to $local_file_path 
	$local_file = file( $local_file_path ); 
	foreach ( $local_file as $lineNum => $line ) {
		$line                 = str_replace( PHP_EOL, '', $line );
		$line_array           = explode( ';', $line );
		$line_array[]     = ( ( 0 === $lineNum )  &&  ( FALSE === $no_ftp_header ) ) ? 'modified_date' : $modified_date;
       	$line                 = implode(';', $line_array);
       	$local_file[$lineNum] = $line;
    }

	$updated_contents = implode( PHP_EOL, $local_file ); // put the read lines back together (remember $Read as been updated) using "\n" or "\r\n" whichever is best for the OS you're running on
	file_put_contents( $local_file_path, $updated_contents . PHP_EOL); // overwrite the file
}

function add_hash_id( $ftp_conn_id, $ftp_file_path, $ftp_file_header, $no_ftp_header, $local_file_path ) {
	//Create id from 
	$hash_id = md5( microtime() + rand() );

	//append modified date column to $local_file_path 
	$local_file = file( $local_file_path ); 
	foreach ( $local_file as $lineNum => $line ) {
		$line                 = str_replace( PHP_EOL, '', $line );
		$line_array           = explode( ';', $line );
		array_unshift( $line_array, ( ( 0 === $lineNum ) && ( FALSE === $no_ftp_header ) ) ? 'id' : $hash_id );
       	$line                 = implode(';', $line_array);
       	$local_file[$lineNum] = $line;
    }

	$updated_contents = implode( PHP_EOL, $local_file ); // put the read lines back together (remember $Read as been updated) using "\n" or "\r\n" whichever is best for the OS you're running on
	file_put_contents( $local_file_path, $updated_contents . PHP_EOL); // overwrite the file
}

/**
 * Refactoring to do with previous function, same code
 */
function get_ftp_files( $table ) {
}


//Append tmp file to global file
// $local_tmp_file = file_get_contents(  LOCAL_TMP_FILE_PATH  );
//     file_put_contents( LOCAL_GLOBAL_FILE_PATH , $local_tmp_file, FILE_APPEND );
//  }






