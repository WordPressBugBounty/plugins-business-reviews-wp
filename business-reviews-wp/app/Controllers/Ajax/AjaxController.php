<?php

namespace Rtbr\Controllers\Ajax;
if ( ! defined( 'ABSPATH' ) ) exit;
class AjaxController {

	public function __construct() {
		new Facebook();
		new Shortcode();
	}
}
