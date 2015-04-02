<?php
/**
 * @since 1.0
 *
 * Stock Model
 *
 * Last row in table is most up-to-date information
 */


class Stock extends Base_model {

	function __construct() {
		$this->connect();

		//Example - need to be changed according to your project parameters
		$this->local_schema = array(
			'headers' => array(
				'Stock ID',
				'Code',
				'Libelle',
				'Stock',
				'Pcb',
				'Date Modified',
			),
			'fields' => array (
				'stock_id',
				'code',
				'libelle',
				'stock',
				'pcb',
				'modified_date',
			),
		);

		//Example - need to be changed according to your project parameters
		$this->ftp_schema = array(
			'fields' => array(
				'Code',
				'Libelle',
				'Stock',
				'En Prepa',
				'Gele',
				'En Cmde',
				'Pcb',
				'Item',
				'Codbar',
			),
			'fields_separator' => ';',
			'fields_eol'       => PHP_EOL,
		);

		//Example - need to be changed according to your project parameters
		$this->table                 = 'stock';
		$this->order_by			     = 'modified_date';
		$this->order_mode		     = 'DESC';
		$this->where_field           = 'default';
		$this->where_value           = 0;
		$this->ftp_directory         = '/out';
		$this->ftp_file_regex        ="/^\/out\/STK.*\.CSV$/";
		$this->ftp_field_col_indices = array(0, 1, 2, 3, 7, 10);
		$this->local_directory       = LOCAL_DOWNLOADS_DIR . '\\' .  $this->table;
		$this->local_tmp_file_path   = $this->local_directory . '\tmp\tmp.csv';

	}
}