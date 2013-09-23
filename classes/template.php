<?php
#Author: Sunnefa Lind
#Project:
#Date:

class Instantiator {
	public $db;
	public $tmp;
	public static $instance;
	
	public function __construct() {
		$this->db = new Database();
		$this->tmp = new Template();
			
	}
}

class Template {
	private $master;
	private $small;
	public static $instance;
	
	public function html_master($tokens, $template =  'theme/index.html') {
		$this->master = (file_exists($template)) ? file_get_contents($template) : die('Template Error!!!');
		$this->master = $this->replace_tokens($tokens, $this->master);
		return $this->master;	
	}
	
	private function html_small($tokens, $template) {
		$this->small = $template;
		$this->small = $this->replace_tokens($tokens, $this->small);
		return $this->small;	
	}
	
	public function replace_tokens($tokens, $temp) {
		foreach($tokens as $token => $data) {
			$temp = str_replace('{' . $token . '}', $data, $temp);
		}
		return $temp;
	}
	
	public function parse_theme($content) {
		global $lang;
		$content = $this->replace_tokens($lang, $content);
		$theme = file_get_contents(THEME . 'template.html');
		
		return $this->replace_tokens(array('MAIN_BODY' => $content), $theme);	
	}
	
	public function build_user_list($users, $replace, $single = false) {
		$out = NULL;
		
		//for each user we need to display some data
		foreach($users as $user) {
			//replace the template tokens with the actual data and display it
			$out .= $this->html_small(array(
				'U_ID' => $user['id'],
				'U_NAME' => $user['username'],
				'U_DATE' => $user['date'],
				'NUM_ICONS' => $user['num_icons'],
				'NUM_CATS' => $user['num_cats']
				), $replace);	
		}
		
		return $out;
	}
	
	public function build_cat_list($cats, $replace, $single = false) {
		//the text displayed before the category list
		$out = NULL;
		//for each category we need to display some data
		foreach($cats as $cat) {
			//replace the template tokens with the actual data and display it
			$out .= $this->html_small(array(
				'C_ID' => $cat['id'],
				'C_NAME' => $cat['name'],
				'NUM_ICONS' => $cat['num_icons'],
				'C_DESC' => $cat['description']
			), $replace);	
		}
		return $out;	
	}
	
	public function build_icon_list($icons, $replace, $no_cat = false) {
		$out = NULL;
		//for each icons we need to display some data
		foreach($icons as $icon) {
			//replace the template tokens with the actual data and display it
			$out .= $this->html_small(array(
			'I_PATH' => $icon['path'],
			'I_DATE' => $icon['date']
			), $replace);	
		}
		return $out;		
	}		
}

?>