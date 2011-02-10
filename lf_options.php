<h2>Music Label WP (Loudfeed Plugin for WordPress) Options</h2>

Make sure you installed <A href="http://lud.icro.us/wordpress-plugin-magpierss-hotfix/">MagpieRSS Hotfix</a> and you've put content into <a href="http://loudfeed.com">Loud Feed</a>.

<div class="wrap" style="width:560px;">
    <form action="options.php" method="post">
		<?php settings_fields( 'lf_options' );?>

    	
        <p><label>Enter your artists feed <i>(http://yourname.loudfeed.com/artists.rss)</i></label>
        <input type="text" name="lf_artists_feed" value="<?php echo get_option('lf_artists_feed'); ?>" size="60"/></p>
        <p><label>Enter your albums feed <i>(http://yourname.loudfeed.com/albums.rss)</i></label>
         <input type="text" name="lf_albums_feed" value="<?php echo get_option('lf_albums_feed'); ?>" size="60"/></p>
        <!--<label>Give your page a title</label>
        <input type="text" name="title" value="lf_title" value="" /><br/>-->

        <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes'); ?>" />
        </p>
        <p><a href="<?php $root = get_option('siteurl');
						 $path = $root . "/wp-content/plugins/music-label-wp/lf_styles.css";
						 echo $path;?>">Download Stylesheet</a> <span style="font-size:7pt;">(right click, "save link as")</span></p>
        <h3>Editing the stylesheet (CSS)</h3>
        <p>Edit within the classes and ids provided, or within ones that you have created and uniquely named. Global declarations (like <span style="font-family:Courier New, Courier, monospace;">p {padding:0; margin:0;}</span>) will affect the rest of the site. Use FTP to upload your saved changes to:</p>
        <p><strong><?php echo $path?></strong>.</p>
        <p style="font-size:7pt;">CSS handbook: <a href="http://w3schools.com">www.w3schools.com</a></p>
    </form>
</div>