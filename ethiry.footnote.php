<?php
/*
Plugin Name: ET Footnote
Description: create footnotes in posts
Author: Emmanuel Thiry
Version: 1
*/

$plugin_dir_path = plugin_dir_path( __FILE__ )."ethiry.config.php";
include_once($plugin_dir_path);

add_action('init', 'plugin_shortcodes_init');

function plugin_shortcodes_init() 
{
	add_shortcode('note', 'note_creation');
	add_shortcode('noteText', 'note_texte');
}

function note_creation($attr = [], $content = null, $tag = '')
{
	$result = "";
	$id = $attr['id'];
	if ($id) 
	{
		$result = "<sup><a id='ref$id' href='#fn$id'>$id</a></sup>";
	}
	return $result;
}

function note_texte($attr = [], $content = null, $tag = '')
{
	$result = "";
	$id = $attr['id'];
	if ($id && $content) 
	{
		$result = "<sup id='fn$id'>";
		$result .= "<span class='footnote-text'>$id. $content</span>";
		$result .= "<a title='Retour à la note $id dans le texte' href='#ref$id'><img class='footnote-icon' src='".GOBACK_URL."' alt='go back' /></a>";
		$result .= "</sup>";
	}
	return $result;
}

?>
