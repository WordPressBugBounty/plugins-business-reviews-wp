<?php
 
$helper = new Rtbr\Helpers\Functions;
$meta_options = new Rtbr\Controllers\Admin\Meta\MetaOptions;

echo $helper->fieldGenerator($meta_options->sectionSelectionFields(), true);
