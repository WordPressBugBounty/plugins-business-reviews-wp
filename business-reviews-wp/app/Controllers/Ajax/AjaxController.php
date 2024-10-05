<?php

namespace Rtbr\Controllers\Ajax;

class AjaxController {

    public function __construct() {
        new Facebook(); 
        new Shortcode(); 
    }
}