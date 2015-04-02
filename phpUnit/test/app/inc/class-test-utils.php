<?php

class Utils_test extends PHPUnit_Framework_TestCase {

    public function test_is_active() {
		$current_value = 'val';
		$request_value = 'val';
	    $this->assertSame( is_active( $current_value, $request_value ), 'active' );

	   	$current_value = 'val';
		$request_value = 'otherVal';
	    $this->assertSame( is_active( $current_value, $request_value ), '' );
	}

    public function test_get_pagination_params() {
		$page = '0';
		$pagination_params = array(
			'row_offset'    => 0,
			'row_max'       => 20,
			'next_page'     => 0,
			'previous_page' => 1,
			'disabled'      => 'disabled'
		);
	    $this->assertSame( get_pagination_params( $page), $pagination_params );

		$page = '13';
		$pagination_params = array(
			'row_offset'    => 12*20,
			'row_max'       => 20,
			'next_page'     => 12,
			'previous_page' => 14,
			'disabled'      => ''
		);
	    $this->assertSame( get_pagination_params( $page), $pagination_params );
	}

}