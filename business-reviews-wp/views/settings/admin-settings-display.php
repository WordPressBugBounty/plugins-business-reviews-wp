<div class="wrap rtbr-settings">
    <?php
        settings_errors();
        self::show_messages();
    ?> 
    <h2 class="nav-tab-wrapper">
        <?php
        foreach ( $this->tabs as $slug => $title ) {
            $class = "nav-tab nav-".$slug;
            if ( $this->active_tab == $slug ) {
                $class .= ' nav-tab-active';
            }
            echo '<a href="?post_type=' . rtbr()->getPostType() . '&page=rtbr-settings&tab=' . $slug . '" class="' . $class . '">' . $title . '</a>';
        }
        ?>
    </h2> 

    <form method="post" action="">
        <?php
        do_action( 'rtbr_admin_settings_groups', $this->active_tab, $this->current_section );
        wp_nonce_field( 'rtbr-settings' ); 
        if ( $this->active_tab != 'support' ) {
            submit_button();
        } 
        ?>
    </form>  
</div>