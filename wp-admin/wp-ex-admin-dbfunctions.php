<?php

require '../wp-config.php';

class AdminDbExtraFunctions {

	private $dbAdminConnection = NULL;
	private $query = "";
	
	function __construct() {
	
		$this->dbAdminConnection = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
		if ($this->dbAdminConnection != NULL){
			mysql_select_db(DB_NAME) or die(mysql_error());
		}
	}
	
	function execute_query() { return mysql_query($this->query); }
	
	function return_single_filed_value($result) { return mysql_result($result, 0); }
	
	function fetchSomeDataToSingleArray($results, $params) {
	
		$fetchedArray = array();
		while($row = mysql_fetch_array($results)){
			foreach($params as $eachParam){
				$fetchedArray[$eachParam] = $row[$eachParam];
			}
		}
		return $fetchedArray;
	}
	
	function fetchSomeDataToAssArray($results, $params) {
	
		$i = 0;
		$fetchedArray = array();
		while($row = mysql_fetch_array($results)){
			foreach($params as $eachParam){
				$fetchedArray[$i][$eachParam] = $row[$eachParam];
			}$i++;
		}
		return $fetchedArray;
	}
	
	function loadAllParentEvents($current_val = "") {
		
		$this->query = "SELECT event_id, event_slug, event_name FROM wp_em_events WHERE event_status = 1 AND event_parent_id = 0";
		$parent_events = $this->fetchSomeDataToAssArray($this->execute_query(), array('event_id', 'event_slug', 'event_name'));
		foreach($parent_events as $eachEvent){
			if ($eachEvent['event_id'] == $current_val){
				$eachEvent['selected'] = "selected";
			}else{
				$eachEvent['selected'] = "";
			}
			$modified_events[] = $eachEvent;
		}	
		return $modified_events;
	}
	
	function returnParentEventIdByPostId($post_id = "") {

		if (($post_id != "") && ($_GET['action'] == "edit")){
			$this->query = "SELECT event_parent_id FROM wp_em_events WHERE post_id = {$post_id}";
			return $this->return_single_filed_value($this->execute_query());
		}else{
			return "";
		}	
	}
	
   function errorCheckingFields($fieldsArray = array()) {
   
   		$errorFieldsArray = array();
		$errorsFound = true;
   		foreach($fieldsArray as $field => $value) {
			if ((isset($value)) && (!empty($value)) && ($value != '') && ($value != NULL)) $errorsFound = false;
			else $errorFieldsArray[] = $field;
		}
		return array('errorStatus' => $errorsFound, 'errorFields' => $errorFieldsArray);
   }
   
   function addNewSeatPrice($postedValues, $seat_map) {
   
   		$this->query = "INSERT INTO wp_em_seat_map (seat_type, price, seat_map) VALUES('".$postedValues['seat_title']."', '".$postedValues['seat_price']."', '".$seat_map."')";	
		if ($this->execute_query()){
			return true;
		}
   }
   
   function loadAllSeatTypes() {
   
   		$this->query = "SELECT * FROM wp_em_seat_map";
		return $this->fetchSomeDataToAssArray($this->execute_query(), array('id', 'seat_type', 'price', 'seat_map'));
   }
   
   function findSeatMapDetails($id) {
   
		$this->query = "SELECT * FROM wp_em_seat_map WHERE id = {$id}";
		return $this->fetchSomeDataToSingleArray($this->execute_query(), array('id', 'seat_type', 'price', 'seat_map'));
   }
   
   function updateNewSeatPrice($postedValues, $seat_map, $id) {
   
		$this->query = "UPDATE wp_em_seat_map SET seat_type = '".$postedValues['seat_title']."', price = '".$postedValues['seat_price']."', seat_map = '".$seat_map."' WHERE id = {$id}";
   		$results = $this->execute_query();
		if ($results){
			return true;
		}   	
   }
   
   function listAllBookings() {
   
		$this->query = "SELECT * FROM wp_em_real_bookings";
		return $this->fetchSomeDataToAssArray($this->execute_query(), array('booking_id', 'event_name', 'email', 'first_name', 'last_name', 'billing_address', 'post_code', 'ticket_type',
													'total_tickets_price', 'total_tickets', 'name_of_card', 'card_type', 'card_number', 'security_number', 'expiry_date', 'delivery_address', 'tel_no'));
   }
   
   function dropSelectedSeatMap($seatMapId) {
   
 		$this->query = "DELETE FROM wp_em_seat_map WHERE id = {$seatMapId}";
		$results = $this->execute_query();  		
		if ($results) return true;
   }
}