<?php

/*
Plugin name: Event Management System
Plugin URL: http://www.socialseedmedia.com.au
Description: Event Management System
Author: Ruchira Chamara
Version: 1.0
*/
class EventManager{
	
	public function __construct(){
	
		$this->register_main_event_type();
		$this->register_sub_event_type();
		$this->meta_boxes_for_sub_events();	
	}
	
	public function register_main_event_type(){
		
		$mainEventTypeDetails = array(
			'labels' => array(
						'name' => 'Main Event Manager',
						'singular_name' => 'Main Event Manager',
						'add_new' => 'Add new main event',
						'all_items' => 'All main events',
						'add_new_item' => 'Add new main event',
						'edit_item' => 'Edit main event',
						'new_item' => 'Add new main event',
						'view_item' => 'View the main event',
						'search_items' => 'Search main event',
						'not_found' => 'Main event not found!',
						'not_found_in_trash' => 'Main event not found in trash!',
						'menu_name' => 'Main Event manager',
						'parent_item_colon' => ''
						),
			'query_var' => 'main_event_manager',
			'rewrite' => array('slug' => 'main_event_manager'),
			'public' => true,
			'menu_position' => 0,
			'menu_icon' => admin_url() . 'images/media-button-video.gif',
			'supports' => array('title', 'excerpt')
		);
		register_post_type('main_event_manager', $mainEventTypeDetails);
	}
	
	public function register_sub_event_type(){
		
		$subEventTypeDetails = array(
			'labels' => array(
						'name' => 'Sub Event Manager',
						'singular_name' => 'Sub Event Manager',
						'add_new' => 'Add new sub event',
						'all_items' => 'All sub events',
						'add_new_item' => 'Add new sub event',
						'edit_item' => 'Edit sub event',
						'new_item' => 'Add new sub event',
						'view_item' => 'View the sub event',
						'search_items' => 'Search sub event',
						'not_found' => 'Sub event not found!',
						'not_found_in_trash' => 'Sub event not found in trash!',
						'menu_name' => 'Sub Event manager',
						'parent_item_colon' => ''
						),
			'query_var' => 'sub_event_manager',
			'rewrite' => array('slug' => 'sub_event_manager'),
			'public' => true,
			'menu_position' => 1,
			'menu_icon' => admin_url() . 'images/media-button-video.gif',
			'supports' => array('title', 'excerpt')
		);
		register_post_type('sub_event_manager', $subEventTypeDetails);
	}
	
	public function meta_boxes_for_sub_events(){
		
		add_action('add_meta_boxes', function(){
			add_meta_box('venue_name', 'Venue name', function($post){
					$venue = get_post_meta($post->ID, 'sub_event_manager_venue', true);
					?>
					<p>
						<label for="sub_event_manager_venue">Venue: </label>    
						<input type="text" name="sub_event_manager_venue" id="sub_event_manager_venue" class="widefat" value="<?php echo esc_attr($venue);?>" />
					</p>    
					<?php
				}, 'sub_event_manager');
			add_meta_box('city', 'City', function($post){
					$city = get_post_meta($post->ID, 'sub_event_manager_city', true);
					?>
					<p>
						<label for="sub_event_manager_city">Venue: </label>    
						<input type="text" name="sub_event_manager_city" id="sub_event_manager_city" class="widefat" value="<?php echo esc_attr($city);?>" />
					</p>    
                    <?php
				}, 'sub_event_manager');
			add_meta_box('ticket_type', 'Ticket Type', function($post){
					$ticket_type = get_post_meta($post->ID, 'sub_event_manager_ticket_type', true);
					?>
					<p>
						<label for="sub_event_manager_ticket_type">Type: </label>    
						<input type="text" name="sub_event_manager_ticket_type" id="sub_event_manager_ticket_type" class="widefat" value="<?php echo esc_attr($ticket_type);?>" />
					</p>    
                    <?php
				}, 'sub_event_manager');
			add_meta_box('ticket_price', 'Ticket Price', function($post){
					$ticket_price = get_post_meta($post->ID, 'sub_event_manager_ticket_price', true);
					?>
					<p>
						<label for="sub_event_manager_ticket_price">Price: </label>    
						<input type="text" name="sub_event_manager_ticket_price" id="sub_event_manager_ticket_price" class="widefat" value="<?php echo esc_attr($ticket_price);?>" />
					</p>    
					<?php
				}, 'sub_event_manager');
		});
		add_action('save_post', function($id){
			if (
				(isset($_POST['sub_event_manager_venue'])) &&
				(isset($_POST['sub_event_manager_city'])) &&
				(isset($_POST['sub_event_manager_ticket_type'])) &&
				(isset($_POST['sub_event_manager_ticket_price']))
				)
				{
				update_post_meta($id, 'sub_event_manager_venue', strip_tags($_POST['sub_event_manager_venue']));
				update_post_meta($id, 'sub_event_manager_city', strip_tags($_POST['sub_event_manager_city']));
				update_post_meta($id, 'sub_event_manager_ticket_type', strip_tags($_POST['sub_event_manager_ticket_type']));
				update_post_meta($id, 'sub_event_manager_ticket_price', strip_tags($_POST['sub_event_manager_ticket_price']));
			}
		});
	}
}

add_action('init', function(){
	new EventManager();
});