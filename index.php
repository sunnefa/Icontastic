<?php
#Author: Sunnefa Lind
#Project: Icontastic
#Date: 14.05.11

/*
* This is index.php
* All requests are funneled through this page
*/

/*
* To do:
* Finish implementing the default theme complete with a header and footer etc
* Implementing translations so all words and any text written can be changed
* Implementing login features
* Implementing registration features
* Implementing category creation fetures
* Implementing icon upload features
* Implementing multiple icon upload features, look into either adding upload boxes or some multiple select uploads
* Implementing deleting icons
* Implementing deleting categories
* Implementing a user role system
*/

error_reporting(E_ALL);

include 'classes/database.php';
include 'classes/template.php';
include 'lang/english.php';

define('THEME', 'themes/dw/');

if(file_exists(THEME . 'theme.php')) include THEME . 'theme.php';

include 'theme_master.php';
$current_page = (isset($_GET['page'])) ? $_GET['page'] : 'home';

Instantiator::$instance = new Instantiator();

ob_start();

switch($current_page) {
	
	//a list of users - maybe implement some sort of statistics here as well
	case 'home':
		//get the user data from the database
		echo $before_user_list_template;
		echo Instantiator::$instance->tmp->build_user_list(Instantiator::$instance->db->get_user_list(), $user_list_template);
	break;
	//end home
	
	//individual user
	case 'user':
		//if the user's id is not in the GET we have no data to display so it's better to die here
		if(!isset($_GET['id'])) die('This page cannot be accessed without a userid');
		
		//get the user info from the database
		$user = Instantiator::$instance->db->get_user_list("u.id = {$_GET['id']}");
		
		//build the user 'list'
		$user_list = Instantiator::$instance->tmp->build_user_list($user, $single_user_template, true);
		
		echo Instantiator::$instance->tmp->replace_tokens(array(
		'USER_LIST' => $user_list
		), $single_user_block_template);
		
		//flatten the user array so we can use it in our conditionals below
		$user = Instantiator::$instance->db->flat($user);
		
		//if the user has created categories we need to display a list of them
		if($user['num_cats'] != 0) {
			//get the categories from the database
			$cats = Instantiator::$instance->db->get_cat_list("c.users_id = {$_GET['id']}");
			
			//get the actual cat list itself
			$cat_list = Instantiator::$instance->tmp->build_cat_list($cats, $category_list_template);
			
			//display the cat list
			echo Instantiator::$instance->tmp->replace_tokens(array(
			'BEFORE_CATEGORY_LIST' => $before_category_list_template,
			'CAT_LIST' => $cat_list
			), $category_list_block_template);
		}
			
		//if this user has uploaded any icons we need to fetch some info about them
		if($user['num_icons'] != 0) {
			//get the icons from the database
			$icons = Instantiator::$instance->db->get_icon_list("i.users_id = {$_GET['id']} AND i.categories_id = 0");
			
			//get the actual icon list itself
			$icon_list = Instantiator::$instance->tmp->build_icon_list($icons, $icon_list_template, true);
			
			//display the icon list
			echo Instantiator::$instance->tmp->replace_tokens(array(
				'BEFORE_ICONS' => $before_unsorted_icons_template, 
				'ICON_LIST' => $icon_list
			), $icon_block_template);
		}
	break;
	//end single user
	
	//single category
	case 'cat':
		if(!isset($_GET['id'])) die("This page cannot be accessed without a category id");
			//get the data about all the categories
		$cat = Instantiator::$instance->db->get_cat_list("c.id = {$_GET['id']}");
		echo Instantiator::$instance->tmp->build_cat_list($cat, $single_category_template, true);
		$icons = Instantiator::$instance->db->get_icon_list("i.categories_id = {$_GET['id']}");
		$icon_list = Instantiator::$instance->tmp->build_icon_list($icons, $icon_list_template);
		
		//display the icon list
		echo Instantiator::$instance->tmp->replace_tokens(array(
			'BEFORE_ICONS' => '', 
			'ICON_LIST' => $icon_list
		), $icon_block_template);
	break;
	//end single cat	
}

echo Instantiator::$instance->tmp->parse_theme(ob_get_clean());

?>