<div class="wrap">

    <h2>Save Google Analytics Locally</h2>
    <p><?php _e('Door '); ?><strong><a href="http://dev.daanvandenbergh.com/buy-me-a-beer/" title="Click here to buy me a beer!">Daan van den Bergh</a></strong></p>

    <form method="post" action="options.php">
    
    <?php
		settings_fields('save_ga_locally_options_group');
		do_settings_sections('save_ga_locally_options');
		submit_button();
    ?>
    
    </form>
</div>