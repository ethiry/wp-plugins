<?php
/*
Plugin Name: ET PHOTO2
Description: embed photos from my website
Author: Emmanuel Thiry
Version: 1.1
*/

$plugin_dir_path = plugin_dir_path( __FILE__ )."ethiry.config.php";
include_once($plugin_dir_path);

define('TAGNAME', "etphoto2");
define('META_PHOTOS', "ET_filenames");

function myplugin_shortcodes_init()
{
    add_shortcode(TAGNAME, 'et_photo2_shortcode');
}

function et_photo2_shortcode($attr = [], $content = null, $tag = '')
{
    $result = "";

    $urlPhotoTemplate = URL_TRAVELPHOTOS."/assets/photos/%s/MEDIUM/%s.jpg";
    $urlPhotoPageTemplate = URL_TRAVELPHOTOS."/photos/voyage/%s/%s";
    $title = urldecode($attr['title']);
    $voyage_key = $attr['voy_key'];
    $filename = $attr['filename'];
    $show_caption = $attr['show_caption'] && ($attr['show_caption'] == 'true');

    if ($voyage_key && $filename) {
      $urlPhotoInStorage = sprintf($urlPhotoTemplate, $voyage_key, $filename);
      $urlPhotoPage = sprintf($urlPhotoPageTemplate, $voyage_key, $filename);
      
      $result .= sprintf('<a href="%s" title="%s"><img src="%s" alt="%s" /></a>',
                            $urlPhotoPage, $title, $urlPhotoInStorage, $title); 
    }

    if ($title && $show_caption) {
        $result = '<figure style="width: 1920px" class="wp-caption alignnone">' . $result;
        $result .= '<figcaption class="wp-caption-text">' . $title . '</figcaption>';
        $result .= '</figure>';
    }

  	$result = "<p>$result</p>";

    return $result;
}

function onPostUpdated($post_ID, $post_after, $post_before)
{
  mylog("post_updated ($post_ID) status=".$post_after->post_status);
	testPhotoTags($post_ID);
	if ($post_after->post_status == "publish") 
	{
		updatePhotoTags($post_ID, $post_after->post_title, $post_after->post_content);
	}
}

// function onSavePost( $post_ID, $post, $update ) {
//   mylog("save_post($post_ID) update=$update");
// }

function testPhotoTags($post_ID)
{
	$filenames = get_post_meta($post_ID, META_PHOTOS, false);
	$dump = var_export($filenames, true);
	mylog("for post $post_ID: $dump");
}

function updatePhotoTags($post_ID, $title, $content)
{
	mylog('['.date("Y-m-d H:i:s").']');
	mylog('-----------------');
	mylog("  post $post_ID  ");
	mylog('-----------------');
	delete_post_meta($post_ID, META_PHOTOS);
	$photos = Array();
	$cptTag = 0;
	if (preg_match_all("/\[".TAGNAME." (.+)\]/", $content, $matches, PREG_SET_ORDER))
	{
		foreach ($matches as $match) 
		{
			$cptTag++;
			$params = explode(" ", $match[1]);
			foreach ($params as $param)
			{
				$parts = explode("=", $param);
				$key = $parts[0];

				if ($key == "filename")
				{
					$val = trim($parts[1], '"');
					mylog("$cptTag: $key => $val");

					add_post_meta($post_ID, META_PHOTOS, $val, false);
					array_push($photos, $val);
					break;
				}

				// log all parameters (without break)
				/*
				$val = urldecode(trim($parts[1], '"'));
				mylog("$cptTag: $key => $val");

				if ($key == "filename")
				{
					add_post_meta($post_ID, META_PHOTOS, $val, false);
					array_push($photos, $val);
				}
				*/
			}
		}
	}

	sendDataToServer($post_ID, $title, $photos);
}

function sendDataToServer($post_ID, $title, $photos)
{
	$url = "http://".DOCKER_TRAVELPHOTOS."/api/journal/photos/$post_ID";
  mylog($url);
	
	$body = array(
		'title' => $title,
		'photos' => $photos,
	);

	$res = wp_remote_post($url, array(
    'method'      => 'PUT',
    'headers'     => array(
			'Content-Type' => 'application/json; charset=utf-8',
			'Authorization' => TOKEN,
		),
    'body'        => json_encode($body),
    'data_format' => 'body',
	));

	log_http_response($res);
}

function log_http_response($res)
{
	if ($res instanceof WP_error)
	{
		mylog("PUT ".json_encode($res));
	}
	else
	{
		if ($res['response']['code'] == 200) 
		{
			mylog("PUT OK");
		}
		else
		{
			mylog("PUT ".$res['response']['code']." ".$res['response']['message']); 
		}
	}
}

function mylog($message)
{
	if (DEBUG)
	{
		error_log("$message\n", 3, LOG_FILE);
	}
}

add_action('init', 'myplugin_shortcodes_init');
add_action('post_updated', 'onPostUpdated', 10, 3 );
// add_action('save_post', 'onSavePost', 10, 3 );

?>
