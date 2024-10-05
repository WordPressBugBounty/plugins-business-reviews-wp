<?php

namespace Rtbr\Controllers\Admin; 

use Rtbr\Controllers\Admin\Meta\MetaController; 

class AdminController {

    public function __construct() { 
        new RegisterPostType();
        new MetaController();
        new ScriptLoader(); 
        new AdminSettings();
    }

}