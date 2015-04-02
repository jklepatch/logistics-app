<?php
/**
 * @since 1.0
 *
 * Remarks model
 *
 * Remarks are related to shipments
 * In general a remark describe a problem
 * that happened with a shipment. It could
 * be a problem with the postal code, for example
 */

class Remarks extends Base_model {

	function __construct() {
		$this->connect();

		//Example - need to be changed according to your project parameters
		$this->local_schema = array(
			'headers' => array(
				'Order ID',
				'Code article',
				'Status',
				'Remark',
				'Date',
			),
			'fields' => array (
				'order_id',
				'code_article',
				'status',
				'remark',
				'modified_date',
			),
		);		 

		//Example - need to be changed according to your project parameters
		//If there is no header in ftp files for this model, hence the empty array() for 'fields'
		 $this->ftp_schema = array(
		 	'fields' => array(''),
		 	'fields_separator' => '',
		 	'fields_eol'       => '',
		 );

		 $this->table                 = 'remarks';
		 $this->order_by			  = 'modified_date';
		 $this->order_mode			  = 'DESC';
		 $this->where_field           = 'default';
		 $this->where_value           = 0;
		 $this->ftp_directory         = '/out';
		 $this->ftp_file_regex        ="/^\/out\/ANO.*\.CSV$/";
		 $this->ftp_field_col_indices = array(0, 3, 5, 6, 7);
		 $this->local_directory       = LOCAL_DOWNLOADS_DIR . '\\' .  $this->table;
		 $this->local_tmp_file_path   = $this->local_directory	. '\tmp\tmp.csv';
	}
}