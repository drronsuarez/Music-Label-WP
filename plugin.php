<?php 

/*
Plugin Name: Music Label WP
Plugin URI: http://musiclabelwp.loudfeed.com/download/
Description: Delivers artist and album music content rss feeds from Loud Feed free accounts into Wordpress. Requires plugin "magpierss" (RSS enclosure support)
Author: Dan Polant, Ron Suarez, Prabode Weebadde, Loud Feed, Inc.
Author URI: http://loudfeed.com
Version: 0.9

/*
 * This plugin generates the artists and albums page structure, using
 * shortcodes that render as html populated with loudfeed 
 * information.  
 */

add_action('admin_menu', 'add_lf_options');
add_action( 'admin_init', 'register_settings' );
add_action('wp_head' , 'lf_page_load');
add_action('init' , 'do_scripts');

//retrieve settings
function register_settings() {
  register_setting( 'lf_options', 'lf_albums_feed' );
  register_setting( 'lf_options', 'lf_artists_feed' );
}

//runs when feeds are updated
add_action('update_option_lf_albums_feed', 'set_albums_content');
add_action('update_option_lf_artists_feed', 'set_artists_content');

//activation and deactivation
register_activation_hook(__file__, 'plugin_activate');
register_deactivation_hook(__file__, 'plugin_deactivate');

//generates widget ---- NOT USED YET
function widget_init_myuniquewidget() 
{
	// Check for required functions
	if (!function_exists('register_sidebar_widget'))
		return;

	function widget_myuniquewidget($args) 
	{
	    extract($args);
	 	echo $before_widget;
	    echo $before_title. 'Events'. $after_title;
	                
	    ?> 
           <!-- JS and HTML for the widget -->
                    
	            <script type="text/javascript" src="http://www.google.com/jsapi?key=ABQIAAAAPdkFctYdU_SU2ySCnJdrgRQTHzLmD_MgGeKdUyZ_6DTQ7CB2uxTWF74IvXq7-w1eSjmfk7u9JmPNjg"></script>
                <script type="text/javascript">
					  google.load("maps", "2.x");
					  
					  function initialize() 
					  {
						  if (GBrowserIsCompatible()) 
						  {
							var map = new GMap2(document.getElementById("map"));
							map.setCenter(new GLatLng(37.4419, -122.1419), 13);
							map.setUIToDefault();
							
							map.openInfoWindow(map.getCenter(),
							document.createTextNode("Hello, world"));
							
							jQuery(document).ready(function($) 
							{
								$("#showMap").toggle(function(){
								 $("#map").hide('slow');
							   },function(){
								 $("#map").show('fast');
							   });
							})
							
						  }
					  }
					  google.setOnLoadCallback(initialize);
				</script>
                
                <!-- End -->
		<?php 
		
		echo "<div id='map' onload='initialize()' onunload='GUnload()' style='height: 300px'></div>";
		echo "<input type='button' id='showMap' value='Show'/>";
	  
		echo $after_widget; 
	
	}
	register_sidebar_widget('widget_myuniquewidget' , 'widget_myuniquewidget');
}

//add_action('widgets_init', 'widget_init_myuniquewidget');

//shortcode stuff
function lf_content_func($atts) 
{
	extract(shortcode_atts(array(
		'feed' => 'no feed',
		'type' => 'none',
	), $atts));
	
	$type = $atts['type'];

	if ($type == 'artists')
		$feed = get_option('lf_artists_feed');
	elseif ($type == 'albums')
		$feed = get_option('lf_albums_feed');
	else
		$feed = $atts['feed'];
	
	$feed_obj = fetch_rss($feed); //need it as an object to get the channel meta
	$feed_array = feed_to_array($feed); //need it as an array for foreach
	
	//echo $feed;
	if(is_array($feed_array))
		{
	
	/*============================== Builds the HTML for Artists page ===============================*/
	
		if ($type == 'artists')
		{
			$output .=				"<div id='albumscontainer'>";
			$output .=					 "<div class='inner'>";
			
				foreach ($feed_array as $artist)
				{
					$short = word_limiter($artist['description'], 38);
					
					if (!$short)
					{
						$short = "No Description.";
					}
					else
					{
						$short = $short . " ...";
					}
					
					$link = $root . "/Artists/" . $artist['title'];
					$link = str_ireplace(" ", "-", $link);
					$image = $artist['enclosure'][0]['url'];
		
					$output .=                       "<div class='album'>";
					$output .=							  "<h2><a href='" . $link . "'>" . $artist['title'] . "</a></h2>";
					$output .= 							  "<a href='" . $link . "'><img src='" . $image . "' style='width:100px;'/></a>";
					$output .= 							  "<div id='p_hack'>" . $short;						
					$output .= 							  "<div style='text-align:right'><a href='" . $link . "'>more</a></div></div>"; 
					$output .=							  "<div class='clear'></div>";
					$output .=						 "</div>";
				}
				
				$output .=					"<div class='clear'></div>";
				$output .=					"</div>";
				$output .=				"</div>";
		}
		
		/*============================== For Albums page ===============================================*/
		
		elseif ($type == 'albums')
		{
			$output .=				"<div id='albumscontainer'>";
			$output .=					 "<div class='inner'>";
			
			foreach ($feed_array as $album)
			{
				$short = word_limiter($album['comments'], 38);
				
				if (!$short)
				{
					$short = "No Description.";
				}
				
				$link = $root . "/Albums/" . $album ['author'] . "-" . $album['title'];
				$link = str_ireplace(" ", "-", $link);
				$image = $album['enclosure'][0]['url'];
	
				$output .=                       "<div class='album' >";
				$output .=							  "<h2><a href='" . $link . "'>" . $album['title'] . " - " . $album['author'] . "</a></h2>";
				$output .= 							  "<a href='" . $link . "'><img src='" . $image . "' style='width:100px;'/></a>";
				$output .= 							  "<div id='p_hack'>" . $short . " ...";							
				$output .= 							  "<div style='text-align:right'><a href='" . $link . "'>more</a></div></div>"; 
				$output .=							  "<div class='clear'></div>";
				$output .=						 "</div>";
			}
			
			$output .=					"<div class='clear'></div>";
			$output .=					"</div>";
			$output .=				"</div>";
		}
		
		/*===================================== For artist_landing pages ===============================*/
		
		elseif ($type == "artist_landing") 
		{
			$image   = $feed_obj->image['url'];
			$title   = $feed_obj->channel['title'];
			$website = $feed_obj->channel['link'];
			$bio     = $feed_obj->channel['description'];
			$email   = $feed_obj->channel['managingeditor'];
	
			$output .= 							"<div class='artistcontainer'>";
			$output .= 								"<div class='halfLeft'>";
			$output .=									"<h2>" . $title . "</h2>";
			$output .= 							    	"<img src='" . $image . "' style='width:100%;'/>";
			$output .=								"</div>";
			$output .=							"<div class='halfRight'>";
			$output .=							"<div class='box'>";
			$output .=                              "<h3>Website: <br/><a href='" . $website . "'>" . $website . "</a></h3>";
			$output .=                              "<h3>Email: <br/><a href='mailto:" . $email ."'>" . $email . "</a></h3>";
			$output .=                              "<h3>Albums</h3>";
			$output .=                              "<ul>";
	
				foreach ($feed_array as $album) 
				{
					$album_link = $root . "/Albums/" . $album['author'] . "-" . $album['title'];
					$album_link = str_ireplace(" ", "-", $album_link);
					
					$output .=                           "<li><a href='" . $album_link . "'>" . $album['title'] . "</a></li>";
				}
				
			$output .=                              "</ul>";
			$output .=                          "</div>";
			$output .=							"</div>";
			$output .=						"<div class='clear'></div>";
			$output .=							"<div class='bio'>";
			$output .= 								"<h2>Artist Bio</h2>" . $bio;
			$output .= 							"</div>";
			$output .=						"<div class='clear'></div>";
			$output .=					"</div>";
		}
		
		/*========================================For album_landing pages ============================*/
		
		elseif ($type == "album_landing")
		{
			$image   = $feed_obj->image['url'];
				
			$title   = $feed_obj->channel['title'];
			$artist  = $feed_obj->channel['itunes']['author'];
		$description = $feed_obj->channel['itunes']['summary'];
			 $genre  = $feed_obj->channel['category'];
			   $link = $root . "/Artists/" . $artist;
			   $link = str_ireplace(" ", "-", $link);
			   
			$output .= 							"<div class='artistcontainer'>";
			$output .= 								"<div class='halfLeft'>";
			$output .=									"<h2>" . $title . "</h2>";
			$output .= 							    	"<img src='" . $image . "' style='width:100%;'/>";
			$output .=								"</div>";
			$output .=							"<div class='halfRight'>";
			$output .=								"<div class='box'>";
			$output .=                              	"<h3>Artist: <br/><a href='" . $link . "'>" . $artist . "</a></h3>";
			$output .=                              	"<h3>Genre:</h3>" . $genre;
			$output .=                              	"<h3>Songs</h3>";
			$output .=                              	"<ul>";
						
			foreach ($feed_array as $song)
			{
				$player = "";
				$player = add_url_param($song['enclosure'][0]['url']);
				$output.=                           "<li>" . $song['title'] . " " . $player . "</li>";	
			}
			
			$output .=                              	"</ul>";
			$output .=                          	"</div>";
			$output .=							"</div>";
			$output .=						"<div class='clear'></div>";
			$output .=							"<div class='bio'>";
			$output .= 								"<h2>Description</h2>" . $description;
			$output .= 							"</div>";
			$output .=						"<div class='clear'></div>";
			$output .=					"</div>";
		}
	}
	else
	{
		$output .= "<h2>No feed info, try checking your feed url.</h2>";
	}
	return $output;
}

add_shortcode('lf_content', 'lf_content_func');

/************************ utility functions go here *******************************/

//purges loudfeed posts in wp_posts
function lf_purge($condition)
{
	global $wpdb;
	$wp_posts = $GLOBALS['table_prefix'] . "posts";
	
	//deletes lf posts from wp_posts
	$get_lf_posts = "SELECT * FROM $wp_posts WHERE $condition AND lf = '1'";
	$rows_to_delete = $wpdb->get_results($get_lf_posts, ARRAY_A);
	
	if (is_array($rows_to_delete))
	{
		foreach ($rows_to_delete as $row)
		{
			wp_delete_post($row['ID']);
		}
	}
}

//truncates string, courtesy of http://www.roscripts.com/snippets/show/30
function word_limiter($str, $n, $end_char = '&#8230;')
{
	if (strlen($str) < $n)
	{
		return $str;
	}
	
	$words = explode(' ', preg_replace("/\s+/", ' ', preg_replace("/(\r\n|\r|\n)/", " ", $str)));
	
	if (count($words) <= $n)
	{
		return $str;
	}
			
	$str = '';
	for ($i = 0; $i < $n; $i++)
	{
		$str .= $words[$i].' ';
	}

	return trim($str);
}

//turns feed into array
function feed_to_array($feed)
{
	//gets the rss feed
	include_once(ABSPATH . WPINC . '/rss.php');
	$rss = fetch_rss($feed);
	
	if (is_object($rss))
	{
		$items = array_slice($rss->items, 0, 99);
		return $items;
	}
	else
	{
		return false;
	}
}

//adds a url parameter to the xpsf flash player
function add_url_param($url)
{
	$url = urlencode($url);
	$embed = "<embed style='' quality='high' autoplay='false' allowscriptaccess='sameDomain' flashvars='song_url=" . $url . "' id='1560' type='application/x-shockwave-flash' src='http://loudfeed.s3.amazonaws.com/flash/button_player.swf?&amp;song_url=" . $url . "' bgcolor='#eeeeee' name='1560' width='18' height='18'></embed>";
	
	return $embed;
}

// keeps db up to date
function render_feed($feed, $child_type, $page_name, $type)
{	
	$wp_posts = $GLOBALS['table_prefix'] . "posts";
	$parent_row = get_page_by_title($page_name);
	$parent = $parent_row->ID;
	$items = feed_to_array($feed);
	
	global $wpdb;
	
	//Get the child pages already in the database to check against the array from the feed
	$get_old_pages = "SELECT * FROM $wp_posts WHERE lf_type='$child_type'";
	$old_pages = $wpdb->get_results($get_old_pages, ARRAY_A);
	
	if(!empty($old_pages))
	{
		foreach ($old_pages as $old_page)
		{
			foreach ($items as $item)
			{
				//naming conventions
				if ($type == 'artists')
				{
					$title = $item['title'];
				}
				elseif ($type == 'albums')
				{
					$title = $item['author'] . " - " . $item['title'];
				}
				
				if ($old_page['post_title']==$title)
				{
					$still_there = true;
				}
			}
			
			//removes posts not associated with current feed
			if (!$still_there)
			{
				$old_ID = $old_page['ID'];
				lf_purge("ID = '$old_ID'");
			}
		}
	}
	
	if (is_array($items))
	{
		if (empty($items)) 
		{
			return false;
		}
		else
		{
			foreach ( $items as $item )
			{
				if (!empty($old_pages))
				{
					foreach ($old_pages as $old_page)
					{
						//naming conventions
						if ($type == 'artists')
						{
							$title = $item['title'];
						}
						elseif ($type == 'albums')
						{
							$title = $item['author'] . " - " . $item['title'];
						}
						// finds collisions between feed and old pages
						if ($old_page['post_title']==$title)
						{
							$collision = true;
						}
					}
				}
				if (!$collision)
				{
					//naming conventions
					if ($type == 'artists')
					{
						$title = $item['title'];
					}
					elseif ($type == 'albums')
					{
						$title = $item['author'] . " - " . $item['title'];
					}
					
					//inserts children into wp_posts
					$child = array();

					$child['post_title'] = $title;
					$child['post_status'] = 'publish';
					$child['post_author'] = 1;
					$child['post_category'] = array(8,39);
					$child['post_type'] = 'page';
					$child['post_parent'] = $parent;
					$child['post_content'] = "[lf_content feed='" . $item['link'] . "' type='" . $child_type . "']";
					
					$child_ID = wp_insert_post($child);
					$wpdb->query("UPDATE $wp_posts SET lf = '1' WHERE ID = '$child_ID'");
					$wpdb->query("UPDATE $wp_posts SET lf_type = '$child_type' WHERE ID = '$child_ID'");
				}                                                           
			}
		}
	}
	else
	{
		return false;
	}
}

/****************************** /end utilities ************************************/

//updates content of albums page when user changes feed
function set_albums_content()
{
	global $wpdb;
	$wp_posts = $GLOBALS['table_prefix'] . "posts";
	$get_albums_page = "SELECT * FROM $wp_posts WHERE lf_type='albums'";
	$page = $wpdb->get_results($get_albums_page, ARRAY_A);
	$update = array();

	foreach ($page as $parent)
	{

		$update['ID'] = $parent['ID'];
		$update['post_content'] = "[lf_content feed='" . get_option('lf_albums_feed') . "' type='albums']";
		
		wp_update_post($update);
	}
}

function set_artists_content() //same as above
{
	global $wpdb;
	$wp_posts = $GLOBALS['table_prefix'] . "posts";
	$get_artists_page = "SELECT * FROM $wp_posts WHERE lf_type='artists'";
	$page = $wpdb->get_results($get_artists_page, ARRAY_A);
	
	foreach ($page as $parent)
	{
		$update = array();
		$update['ID'] = $parent['ID'];
		$update['post_content'] = "[lf_content feed='" . get_option('lf_artists_feed') . "' type='artists']";
		
		wp_update_post($update);
	}
}

// echoes style sheet, runs render_feed
function lf_page_load()
{	
	
	$artists_feed = get_option('lf_artists_feed');
	$albums_feed = get_option('lf_albums_feed');
	
	//gets the path to the stylesheet
	$root = get_option('siteurl');
	$path = $root . "/wp-content/plugins/loudfeed/lf_styles.css";
	echo "<link rel='stylesheet' type='text/css' href='" . $path . "'/>";
	
	render_feed($artists_feed, 'artist_landing', 'Artists', 'artists');
	render_feed($albums_feed, 'album_landing', 'Albums', 'albums');
}

// Add a new submenu under Options:
function add_lf_options() 
{
    add_options_page('Music Label WP', 'Music Label WP', 8, 'Music Label WP', 'lf_options_page');
}

// lf_options_page() displays the page content for the Test Options submenu
function lf_options_page() 
{
    include('lf_options.php');
}

//creates the Albums and Artist pages, creates lf_att table
function plugin_activate()
{
	$wp_posts = $GLOBALS['table_prefix'] . "posts";
	$artists_feed = get_option('lf_artists_feed');
	$albums_feed = get_option('lf_albums_feed');
	
	global $wpdb;
	$wpdb->query("ALTER TABLE $wp_posts ADD lf int(1)");
	$wpdb->query("ALTER TABLE $wp_posts ADD lf_type varchar(255)");
									  
	//does insert call for Artists and Albums
    $artists = array();
    $artists['post_title'] = 'Artists';
    $artists['post_status'] = 'publish';
    $artists['post_author'] = 1;
    $artists['post_category'] = array(8,39);
    $artists['post_type'] = 'page';
	$artists['post_content'] = "[lf_content feed='" . $artists_feed . "' type='artists']";
    $artists_ID = wp_insert_post($artists);
	$wpdb->query("UPDATE $wp_posts SET lf = '1' WHERE ID = '$artists_ID'");
	$wpdb->query("UPDATE $wp_posts SET lf_type = 'artists' WHERE ID = '$artists_ID'");
	
	$albums = array();
    $albums['post_title'] = 'Albums';
    $albums['post_status'] = 'publish';
    $albums['post_author'] = 1;
    $albums['post_category'] = array(8,39);
    $albums['post_type'] = 'page';
	$albums['post_content'] = "[lf_content feed='" . $albums_feed . "' type='albums']";
    $albums_ID = wp_insert_post($albums);
	$wpdb->query("UPDATE $wp_posts SET lf = '1' WHERE ID = '$albums_ID'");
	$wpdb->query("UPDATE $wp_posts SET lf_type = 'albums' WHERE ID = '$albums_ID'");
}

function do_scripts()
{
	wp_enqueue_script('jquery');
}

function plugin_deactivate()
{
	lf_purge("1=1"); 
}
?>