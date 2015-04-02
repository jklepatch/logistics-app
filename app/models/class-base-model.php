<?php
/**
 * @since 1.0
 *
 * Common ancestor for all models (Shipments, Stock and Remarks class)
 *
 * Provides wrapper funtions to  access the database via Scorpio_orm class
 * Typically these functions define sensible defaults and merge them with arguments given to the function
 * This provides for an easy way to use these functions without having to redefine all parameters everytime
 * 
 * When models are instantiated, they will assign a value to all variables defined in this class
 */

class Base_model extends Scorpio_orm {

	protected $local_schema = array(); //Info about table structure in database
	protected $ftp_schema = array();   //Info about ftp file structure
	protected $table;                  //Name of table
	protected $ftp_directory;          //Remote directory on ftp servers
	protected $ftp_file_regex;         //Pattern ftp file have to match
	protected $ftp_field_col_indices;  //indices of columns to download in ftp file (1st column will be mapped to first field in local db and so on...)
	protected $local_directory;        //Local directory where ftp files will be downloaded
	protected $local_tmp_file_path;    //Local directory where tmp files will be stored (created during data update)
	protected $where_field;            //Where field name in SELECT ... WHERE where.field = where.value SQL queries
	protected $where_value;            //Where field value in SELECT ... WHERE where.field = where.value SQL queries
	protected $search_field;	       //Search field name in SELECT ... WHERE search.field LIKE search.value SQL queries
	protected $search_value;	       //Search field value in SELECT ... WHERE search.field LIKE search.value SQL queries

	/**
	 * @since 1.0
	 *
	 * Handles SELECT statements in the detabase
	 *
	 * If where_field is not given, will produce this mysql query
	 * SELECT `fields` FROM `table` ORDER BY `order_by` `order_mode` LIMIT `row_offset` `row_max`
     *
     * If where_field given, will produce instead:
     * SELECT `fields` FROM `table` WHERE `where_field` = `where_value` ORDER BY `order_by` `order_mode` LIMIT `row_offset` `row_max`
	 *
	 * @param  (array)    $args Hash of arguments - Optional
	 * @param  (string)   $args['table']          - Name of the table for SELECT statement
	 * @param  (string)   $args['fields']         - Name of the fields for SELECT statement
	 * @param  (int)      $args['row_offset']     - value of `offset` in LIMIT `offset` `count`
	 * @param  (int)      $args['row_max']        - value of `count`  in LIMIT `offset` `count`
	 * @param  (string)   $args['where_field']    - value of `table.column in WHERE `table.column` = `value`
	 * @param  (string)   $args['where_value']    - value of `value`       in WHERE `table.column` = `value`
	 * @param  (string)   $args['order_by']       - value of `table.column` in ORDER BY `table.column` `ASC/DESC`
	 * @param  (string)   $args['order_mode']     - value of `ASC/DESC`     in ORDER BY `table.column` `ASC/DESC`
	 * @return (resource) $results - Result of the query
	 */
	function get_data( $args ) {
		$default = array(
			'table'         => $this->table,
			'fields'        => $this->local_schema['fields'],
			'row_offset'    => $GLOBALS['pagination_params']['row_offset'],
			'row_max'       => $GLOBALS['pagination_params']['row_max'],
			'where_field'   => $this->where_field,
			'where_value'   => $this->where_value,
			'order_by'      => $this->order_by,
			'order_mode'    => $this->order_mode,
		);
		$query = array_merge( $default, $args );

		return $this->get_results( $query );
	}

	/**
	 * @since 1.0
	 *
	 * Search for specific rows in the database 
	 *
	 * Will produce this mysql query: 
	 * SELECT `fields` FROM `table` WHERE `search_field` LIKE `search_value`
	 * 
	 * @param  (array)    $args Hash of arguments   - Optional
	 * @param  (string)   $args['search_field']     - value of `search_field`     in WHERE `search_field` LIKE `search_value`
	 * @param  (string)   $args['search_value']     - value of `search_value`     in WHERE `search_field` LIKE `search_value`
	 * @return (resource) $results                  - Result of the query 
	 */
	function search_data( $args ) {
		$default = array(
			'table'          => $this->table,
			'fields'         => $this->local_schema['fields'],
			'search_field'   => $this->search_field,
			'search_value'   => $this->search_value,
		);
		$query = array_merge( $default, $args );

		return $this->search( $query );
	}

	/**
	 * @since 1.0
	 *
	 * Update database data based on ftp files
	 *
	 * This functions has 2 steps:
	 *    - Step #1 : download new ftp files from ftp server and create a tmp file will the concatenanted data of all new files
	 *    - Step #2 : Grab data found in tmp file and insert it into database
	 *
	 * During Step #1, after each file is downloaded its data is in some cases completed by new data (like modified_date field for example)
	 * This is to account for different formats of ftp files. In the future I might add a new step to specifically handle all these formatting
	 *    
	 * @param  (array)    $args Hash of arguments         - Optional
	 * @param  (array)    $args['ftp_file_fields']        - list of all fields found in the ftp files
	 * @param  (string)   $args['ftp_file_regex']         - Regular Expression matching ftp filename pattern
	 * @param  (string)   $args['ftp_directory']          - Remote directory from which to search the ftp files
	 * @param  (string)   $args['local_tmp_file_path']    - Local directory where the tmp file containing concatenated new ftp files will be stored
	 * @param  (array)    $args['local_field_names']      - list of all fields found in the mysql table where data will be stored
	 * @param  (array)    $args['ftp_field_col_indices']  - Positions of required fields in each line of the ftp files
	 *                                                      Example: 
     *                                                          $args['local_field_names']      = array( 'col1', 'col2', 'col3' );
     *                                                          $args['ftp_field_col_indices']  = array( 0, 2, 4 );
     *                                                          'col1' will be mapped with the first column of the ftp file (index 0)
     *                                                          'col2; will be mapped with the second column of the ftp file (index 2)
     *                                                           and so on ...  
	 * @return (bool) $result                             - Result of the query 
	 */
	function update_data( $args = array() ) {
		//Step #1: download ftp files from ftp server
		$default = array (
			'ftp_file_fields' 		=> $this->ftp_schema['fields'],
			'ftp_file_regex' 		=> $this->ftp_file_regex,       
			'ftp_directory' 		=> $this->ftp_directory,
			'local_directory' 		=> $this->local_directory,
			'local_tmp_file_path' 	=> $this->local_tmp_file_path,
		);
		$query = array_merge( $default, $args );
		
		download_ftp_files( $query );

		//Step #2: Insert new data (located in tmp file) in local database
		$default = array(
			'table'                 => $this->table,
			'local_field_names' 	=> $this->local_schema['fields'],  
		 	'local_tmp_file_path'   => $this->local_tmp_file_path,
			'ftp_field_col_indices' => $this->ftp_field_col_indices,    
		);

		$query = array_merge( $default, $args );
		$this->insert( $query );
	}

	/**
	 * @since 1.0
	 *
	 * Return an array with a description of the the model's table 
	 * 
	 * @return (array) $local_schema - description of model's table
	 */
	function get_schema() {
		return $this->local_schema;
	}

	/**
	 * @since 1.0
	 *
	 * Check if database is up-to-date (need to be implemented)
	 *
	 * It compares the remote ftp files with the already downloaded ftp files
	 * If there are some new ftp files not yet downloaded, it will return true 
	 * 
	 * @return (bool) $need_update - if true database needs an update
	 */
	function need_update() {
		return true;
	}
}