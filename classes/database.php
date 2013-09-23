<?php
#Author: Sunnefa Lind
#Project: Icontastic
#Date: 14.05.11

/*
* This is classes/database.php
* Provides the CRUD for the entire database
*/

class database {
	
	private $host = 'localhost';
	private $user = 'icons';
	private $pass = 'icons';
	private $data = 'icons';
	private $connection;
	public static $instance;
	
	public function __construct() {
		$this->connection = mysql_connect($this->host, $this->user, $this->pass);
		if(!$this->connection) die($this->db_error());	
		
		$db = mysql_select_db($this->data);
		if(!$db) die ($this->db_error());
	}
	
	public function select($table, $fields = array(), $where = NULL, $single = false, $joins = array()) {
		if($this->table_exists($table)) {
			$sql = "SELECT";
			$last = end(array_keys($fields));
			foreach($fields as $key => $field) {
				$sql .= " $field";
				if($key != $last) $sql .= ',';
			}
			$sql .= " FROM $table";
			if(!empty($joins)) {
				foreach($joins as $join) {
					$sql .= " LEFT JOIN $join";	
				}
			}
			if($where != NULL) $sql .= " WHERE $where";
			$query = mysql_query($sql);
			if(!$query) die($this->db_error());
			$data = array();
			while($row = mysql_fetch_assoc($query)) {
				$data[] = $row;	
			}
			if($single == true) {
				$data = $this->flat($data);	
			}
			return $data;
		} else die($this->db_error());
	}
	
	private function table_exists($table) {
		$sql = "SELECT COUNT(*) FROM $table";
		$query = mysql_query($sql);
		$data = mysql_num_rows($query);
		if($data < 0 || !$query) return false;
		return true;
	}
	
	public function flat($array, $unique = false) {
		$single = array();
		$i = 0;
		foreach($array as $one) {
			foreach($one as $key => $value) {
				if($unique == true) {
					$single[$key . '_' . $i++] = $value;
				} else {
					$single[$key] = $value;	
				}
			}
		}
		return $single;
	}
	
	private function db_error() {
		return mysql_error($this->connection);	
	}
	
	public function get_cat_list($where = NULL) {
		//get the data about all the categories
		return $this->select('categories AS c', array('c.name', 'c.id', '(SELECT COUNT(id) FROM icons WHERE categories_id = c.id) AS num_icons', 'c.description'), $where, false, array());	
	}
	
	public function get_user_list($where = NULL) {
		return $this->select('users AS u', array('u.username', 'u.id', 'FROM_UNIXTIME(u.date, "%D %M %Y") AS date', '(SELECT COUNT(id) FROM icons WHERE users_id = u.id) AS num_icons', '(SELECT COUNT(id) FROM categories WHERE users_id = u.id) AS num_cats'), $where);	
	}
	
	public function get_icon_list($where = NULL) {
		return $this->select('icons AS i', array("FROM_UNIXTIME(i.date, '%D %M %Y') as date", 'i.path', 'i.categories_id AS cat_id'), $where, false, array());
	}
}
?>