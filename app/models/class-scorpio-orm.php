<?php
/**
 * @since 1.0
 *
 * Basic ORM used for Abstracting interaction with a MySQL DB. 
 * 
 * This class is extended by class-base-model, which is itself extended my models
 * This classes uses PDO to carry out prepared-statements queries 
 */

class Scorpio_orm {

	protected $dbh; //Database handler

	/**
	 * @since 1.0
	 * 
	 * Connects to local database
	 *
	 * Uses credentials given in config.php at project root
	 * Will catch error if any (i.e if cannot connect)
	 *
	 * @return (void)
	 */
	protected function connect() {
		try {
			$this->dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
			$this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE); //correct mysql bug with prepared statement containing integers

		} catch(PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * @since 1.0
	 * 
	 * Get a single row in a SELECT statement
	 *
	 * Uses PDO prepared statements to prevent against sql injection
	 * 
	 * @param  (array)     $args Parameters of the database request
	 * @return (resource)  $sth  Handle pointing to results of database query
	 */
	protected function get_var( $args ) {
		$order_mode = ( $args['order_mode'] === 'ASC' )  ?  'ASC' :  'DESC';
		$sql = "SELECT {$args['field']} FROM {$args['table']} WHERE {$args['where_field']} = :where_value ORDER BY `{$args['order_by']}` {$order_mode} LIMIT 1";
		$sth = $this->dbh->prepare( $sql );
		$sth->execute( array( ':where_value' => $args['where_value'] ) );

		return $sth;
	}

	/**
	 * @since 1.0
	 * Get results from a SELECT statement
	 *
	 * If $args['where-field'] is set, query will have a WHERE clause
	 * In any case, query will have a LIMIT and ORDER BY clauses
	 * Uses PDO prepared statements to prevent against sql injection
	 *
	 * @param  (array)     $args Parameters of the database query
	 * @return (resource)  $sth  Handle pointing to results of database query
	 */
	protected function get_results( $args ) {
		//Format fields array into a string with required format for sql request
		$fields       = implode( ',', $args['fields'] );

		//Setup sensible default if missing some required arguments
		$order_mode   = ( $args['order_mode'] === 'ASC' )  ?  'ASC'                     :  'DESC';
		$row_offset   = ( $args['row_offset'] <> '' )      ? abs( $args['row_offset'] ) : 0;
		$row_max      = ( $args['row_max'] <> '' )         ? abs( $args['row_max'] )    : 20;

		//If parameters required for a where clause request are provided, do this request, otherwise do a simple request without a where clause
		if( $args['where_field'] <> 'default' ) {
			$sql = "SELECT {$fields} FROM {$args['table']} WHERE {$args['where_field']} = :where_value ORDER BY `{$args['order_by']}`  DESC LIMIT :row_offset , :row_max";
			$sth = $this->dbh->prepare( $sql );
			$values = array(
				':where_value' => $args['where_value'],
				':row_offset'  => $row_offset,
				':row_max'     => $row_max,
			);
		} else {
			$sql = "SELECT {$fields} FROM {$args['table']} ORDER BY `{$args['order_by']}`  DESC LIMIT :row_offset , :row_max";
			$sth = $this->dbh->prepare( $sql );
			$values = array(
				 ':row_offset'       => $row_offset,
				 ':row_max'          => $row_max,
			);
		}
		$sth->execute( $values );

		return $sth;	
	}

	/**
	 * @since 1.0
	 *
	 * Search rows where a field is equal to a search value
	 *
	 * Is used currently only for shipments but could also be used on other tables
	 * Uses PDO prepared statements to prevent against sql injection
	 * 
	 * @param  (array)     $args Parameters of the database query
	 * @return (resource)  $sth  Handle pointing to results of database query
	 */
	function search( $args ) {
		$fields = implode( ',', $args['fields'] );

		$sql = "SELECT {$fields} FROM {$args['table']} WHERE {$args['search_field']} LIKE :search_value";
		$sth = $this->dbh->prepare( $sql );
		$values = array(
			':search_value' => $args['search_value'],
		);
		$sth->execute( $values );

		return $sth;
	}

	/**
	 * @since 1.0
	 * 
	 * Insert data from downloaded ftp files to local database
	 * 
	 * Needs description of data structure for ftp files and local database
	 * The mapping between the ftp files and the local database explains the complexity of this function
	 * Uses PDO prepared statements to prevent against sql injection
	 *
	 * @param  (array) $args Parameter for the insert query
	 * @return (void)
	 */
	protected function insert( $args ) {
		$handle = fopen( $args['local_tmp_file_path'], "r");
		ini_set( 'max_execution_time', 300 );

		if ( $handle ) {
			//Prepare sql insert with PDO
			$stmt = $this->prepare_insert( $args['table'], $args['local_field_names'], $args['ftp_field_col_indices'] );
			//While there are non-blank line in the tmp file, we insert a new row in the database
		    while ( ($line = fgets( $handle ) ) !== false ) {
		    	if($line <> '') {
			    	$line_array = explode(';', $line); //Line of the tmp file
			    	$i = 0;
			    	foreach( $args['ftp_field_col_indices'] as $index ) {
			    		$stmt->bindValue( ':field_value_' . $index . '_a', $line_array[ (int) $index ] );
			    		$i = $i + 1;    		
					}
					$stmt->execute();
				}
		    }

		    //create feedback message
		    create_message( "Database updated successfully", 'success' );
		} else {
			create_message( "Could not open " . $local_tmp_file_path, 'danger' );
		}

		//Free file and database resource objects 
		fclose( $handle );
		$this->dbh = null;
	}

	/**
	 * @since 1.0
	 * 
	 * Helper function for insert() above
	 *
	 * Creates a PDO prepared statement, according to local database and ftp files data structure
	 *
	 * @param  (string) $table                 Name of table in databas
	 * @param  (array)  $local_field_names     name of fields in local database
	 * @param  (array)  $ftp_field_col_indices indices of fields we are looking for in ftp file
	 *                                         Example:
	 *                                           if  $local_field_names  = array('id', 'name')
	 *                                           and $local_field_names  = array(2, 3)
	 *                                           the 2nd column of the ftp file will be extracted and inserted in 'id' column in local db
	 *                                           the 3rd column of the ftp file will be extracted and inserted in 'name' column in local db
	 * @return (resource)                      Resource handler pointing to the PDO prepared statement
	 */
	private function prepare_insert( $table, $local_field_names, $ftp_field_col_indices ) {
		$field_values      = array_map( array( $this, 'format_params_callback' ), $ftp_field_col_indices );
		$field_values      = implode( ',', $field_values );
		$local_field_names = implode( ',', $local_field_names );
		$stmt = $this->dbh->prepare( "INSERT IGNORE INTO $table ({$local_field_names}) VALUES ({$field_values})" );
		return $stmt;
	}

	private function format_params_callback( $val ) {
		return ':field_value_' . trim( $val ) . '_a';
	}

}