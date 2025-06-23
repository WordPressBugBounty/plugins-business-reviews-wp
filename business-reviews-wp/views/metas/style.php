<?php

$helper       = new Rtbr\Helpers\Functions();
$meta_options = new Rtbr\Controllers\Admin\Meta\MetaOptions();
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $helper->fieldGenerator( $meta_options->sectionStyleFields(), true );
