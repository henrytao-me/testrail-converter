<?php

/**
 * Copyright 2015 Vy Yen. All rights reserved.
 * vyyennl@gmail.com
 */

function formatter_filter($csv) {
	return array(
		"prefix" => "/[0-9]*\./",
		"start_at" => 1,
		"id" => 0,
		"rows" => array(15, 16)
	);
}

?>