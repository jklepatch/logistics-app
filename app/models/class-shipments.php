<?php
/**
 * @since 1.0
 *
 * Shipments model
 *
 * a shipment is created for each order
 * that has been sent to a customer
 * most important information is 'Tracking'
 * which is the tracking number of this 
 * shipment
 */

class Shipments extends Base_model {

	function __construct() {
		$this->connect();

		//Example - need to be changed according to your project parameters
		$this->local_schema = array(
			'headers' => array(
				'Order ID',
				'Date',
				'Code article',
				'Libelle',
				'Quantity',
				'Tracking'
			),
			'fields' => array (
				'order_id',
				'shipping_date',
				'code_article',
				'libelle',
				'quantity',
				'tracking'
			)
		);

		//Example - need to be changed according to your project parameters
		$this->ftp_schema = array(
			'fields' => array(
				'Cmde',
				'Reference',
				'Date remise',
				'Code client',
				'code article',
				'Libelle',
				'Quantite',
				'B.L.',
				'Livr',
				'Transporteur',
				'Nb colis',
				'Poids cmde',
				'Volume cmde',
				'Tracking',
				'S/N;N.Ligne',
			),
			'fields_separator' => ';',
			'fields_eol'       => PHP_EOL,
		);

		//Example - need to be changed according to your project parameters
		$this->table                 = 'shipments';
		$this->order_by			     = 'shipping_date';
		$this->order_mode		     = 'DESC';
		$this->where_field           = 'default';
		$this->where_value           = 0;
		$this->search_field          = 'order_id';		
		$this->search_value          = 1;		
		$this->ftp_directory         = '/out';
		$this->ftp_file_regex        ="/^\/out\/BLC.*\.CSV$/";
		$this->ftp_field_col_indices = array(0, 2, 4, 5, 6, 13);
		$this->local_directory       = LOCAL_DOWNLOADS_DIR . '\\' .  $this->table;
		$this->local_tmp_file_path   = $this->local_directory	. '\tmp\tmp.csv';
	}
}