<?php get_header(); ?>
<?php 
	session_start();
	require $_SERVER['DOCUMENT_ROOT'].'/www.coldplayconcerttickets.co.uk/wp_ex_functions.php';
	$splitted_url = explode("/", $_SERVER['REQUEST_URI']);			
	foreach($splitted_url as $eachSplit){
		if (!empty($eachSplit)) $newSplittedUrls[] = $eachSplit;
	}
	if (!$DBConn->checkTheSelectedEventSluigIsParentEventOrChildEvent(end($newSplittedUrls))){
		$events = $DBConn->getTheEventSubEventsBySlugName(end($newSplittedUrls));
		$parentId = 0;
	}else{
		$events = $DBConn->getTheSubEventsDetailsByEventSlug(end($newSplittedUrls));		
		$parentId = $events[0]['event_parent_id'];	
		$seatTypes = $DBConn->getSeatTypes();	
	}	

	if ("POST" == $_SERVER['REQUEST_METHOD']){
		if (isset($_POST['seatTypeSubmitButton'])){		
			if (($_POST['seatType']['Qty'] != 0) && (!empty($_POST['seatType']['Qty'])) && ($_POST['seatType']['Qty'] != NULL)){
				$seatTypeDetails = $DBConn->getSeatTypeDetails($_POST['seatType']);
				$seatTypeDetails['selectedQty'] = $_POST['seatType']['Qty'];
				$_SESSION['seatsBuyForm'] = $seatTypeDetails;
				$events = $DBConn->getTheSubEventsDetailsByEventSlug(end($newSplittedUrls));	
			}
		}else if (isset($_POST['cutomer'])){

			$events_extra_details['event_slug'] = end($newSplittedUrls);
			$events_extra_details['event_name'] = $DBConn->getEventNameBySlugName(end($newSplittedUrls));			
			$events_extra_details['event_start_date'] = $events[0]['event_start_date'];
			$events_extra_details['event_start_time'] = $events[0]['event_start_time'];            
			$events_extra_details['location'] = (($events[0]['location_name'] == "") ? "--" : $events[0]['location_name']).(($events[0]['location_town'] != "") ? (", ".$events[0]['location_town']) : "");                   

			$DBConn->addBookingOrder($events_extra_details['event_name'], $_POST['cutomer'], $_SESSION['seatsBuyForm'], $events_extra_details);
			//$DBConn->doMailForBooking($_POST['cutomer'], $_SESSION['seatsBuyForm'], $events_extra_details);
			$_SESSION['mail_sent'] = 'OK';
			echo "<script type='text/javascript' language='javascript'>window.location = '".home_url( '/' )."tickets/'</script>";
		}	
	}
?>
<script type="text/javascript" language="javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" language="javascript">
function gotToFunc(url) {

	location.href = '<?php echo home_url( '/' )."tickets/"?>' + url;
}	
$(document).ready(function(){
	/*
	$("input.buyButton").click(function(){
		$("#eventsBuyForm").submit();
	})
	$("input.seatBuyButton").click(function(){
		$("#seatsBuyForm").submit();
	})
	*/
	$("#emailSubmit").click(function(){
		var errors = false;
   		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;   
		if (
			($("#cutomer_email").val() == "") &&
			($("#cutomer_name").val() == "") &&
			($("#cutomer_address").val() == "") &&
			($("#customer_postcode").val() == "") &&
			($("#cutomer_delievery_address").val() == "") &&
			($("#customer_telno").val() == "") && 
			($("#card_type").val() == "Select your card type") && 
			($("#customer_card_name").val() == "") &&			
			($("#customer_card_number").val() == "") &&
			($("#customer_security_number").val() == "") &&
			($("#customer_card_expiry_month").val() == "") &&
			($("#customer_card_expiry_year").val() == "")
		   ){
				$("#cutomer_email").addClass('errorClass');
				$("#cutomer_name").addClass('errorClass');
				$("#cutomer_address").addClass('errorClass');
				$("#customer_postcode").addClass('errorClass');
				$("#cutomer_delievery_address").addClass('errorClass');
				$("#customer_telno").addClass('errorClass');
				$("#customer_card_name").addClass('errorClass');				
				$("#card_type").addClass('errorClass');
				$("#customer_card_number").addClass('errorClass');
				$("#customer_security_number").addClass('errorClass');
				$("#customer_card_expiry_month").addClass('errorClass');
				$("#customer_card_expiry_year").addClass('errorClass');
				
				errors = true;																				
		   }else{ 
		   		if ($("#cutomer_email").val() == ""){ 
					errors = true;																				
					$("#cutomer_email").addClass('errorClass');
				}else{
					if (!emailReg.test($("#cutomer_email").val())){
						errors = true;																				
						$("#cutomer_email").addClass('errorClass');
					}else{
						$("#cutomer_email").removeClass('errorClass');
					}	
				}				
				if ($("#cutomer_fname").val() == ""){ 
					errors = true;							
					$("#cutomer_fname").addClass('errorClass');
				}else{
					$("#cutomer_fname").removeClass('errorClass');				
				}
				if ($("#cutomer_lname").val() == ""){ 
					errors = true;							
					$("#cutomer_lname").addClass('errorClass');
				}else{
					$("#cutomer_lname").removeClass('errorClass');				
				}
				if ($("#cutomer_address").val() == ""){ 
					errors = true;							
					$("#cutomer_address").addClass('errorClass');
				}else{
					$("#cutomer_address").removeClass('errorClass');								
				}
				if ($("#customer_postcode").val() == ""){ 
					errors = true;							
					$("#customer_postcode").addClass('errorClass');
				}else{
					$("#customer_postcode").removeClass('errorClass');												
				}
				if ($("#customer_card_name").val() == ""){ 
					errors = true;							
					$("#customer_card_name").addClass('errorClass');
				}else{
					$("#customer_card_name").removeClass('errorClass');												
				}
				if ($("#card_type").val() == "Select your card type"){ 
					errors = true;							
					$("#card_type").addClass('errorClass');
				}else{
					$("#card_type").removeClass('errorClass');												
				}
				if ($("#customer_card_number").val() == ""){ 
					errors = true;							
					$("#customer_card_number").addClass('errorClass');
				}else{
					$("#customer_card_number").removeClass('errorClass');												
				}
				if ($("#customer_security_number").val() == ""){ 
					errors = true;							
					$("#customer_security_number").addClass('errorClass');
				}else{
					$("#customer_security_number").removeClass('errorClass');												
				}
				if ($("#customer_card_expiry_month").val() == ""){ 
					errors = true;							
					$("#customer_card_expiry_month").addClass('errorClass');
				}else{
					$("#customer_card_expiry_month").removeClass('errorClass');												
				}
				if ($("#customer_card_expiry_year").val() == ""){ 
					errors = true;							
					$("#customer_card_expiry_year").addClass('errorClass');
				}else{
					$("#customer_card_expiry_year").removeClass('errorClass');												
				}
				
				if ($("#customer_card_start_month").val() != ""){ 
					if ($("#customer_card_start_year").val() == ""){ 
						errors = true;							
						$("#customer_card_start_year").addClass('errorClass');
					}else{
						$("#customer_card_start_year").removeClass('errorClass');												
					}
				}else{
					$("#customer_card_start_month").removeClass('errorClass');												
				}
				
				if ($("#cutomer_delievery_address").val() == ""){ 
					errors = true;							
					$("#cutomer_delievery_address").addClass('errorClass');
				}else{
					$("#cutomer_delievery_address").removeClass('errorClass');																
				}
				if ($("#customer_telno").val() == ""){ 
					errors = true;							
					$("#customer_telno").addClass('errorClass');																				
				}else{
					$("#customer_telno").removeClass('errorClass');																				
				}					
		   }								
		   
		   if (errors){
		   		$("#bookingMessage").removeClass("hide");
				$("#bookingMessage").html("Validation errors occurred. Please confirm the fields and submit it again.");
				$("#bookingMessage").addClass("validationMessageForBooking");
		   }else{
		   		$("#custom_booking_notification_mail_form").submit();
		   }							
	});
});
</script>
<div id="main">
		<div id="container">
			<div id="content" role="main">
			
            <?php if (($parentId == 0) && (!isset($_SESSION['seatsBuyForm']))):?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<table border="0" cellpadding="0" cellspacing="0">
               	<?php if (!empty($events)):?>            
            	<thead class="tableHeading">
                	<th>Event</th>
                	<th>Date</th>
                	<th>Venue</th>
                	<th>City</th>                                                            
                	<th>Buy / Sell</th>                                                                                
                </thead>
                <?php endif;?>                
                <tbody>	
                	<?php if (!empty($events)):?>
                	<?php for($i=0; $i<count($events); $i++):?>
                    <tr class="<?php echo ($i % 2 == 0) ? 'evenCssClass' : 'oddCssClass';?>">
                        <td><?php echo $events[$i]['event_name'];?></td>	
                        <td><?php echo implode("-",array_reverse(explode("-", $events[$i]['event_start_date'])));?></td>	
                        <td><?php echo $events[$i]['location_name'];?></td>	
                        <td><?php echo $events[$i]['location_town'];?></td>	
                        <td><!--<form name="eventsBuyForm" id="eventsBuyForm" method="post">
                        	<input type="hidden" name="post_id" value="<?php //echo $events[$i]['post_id'];?>" />-->
                            <input type="button" value="Buy" class="buyButton" onclick="javascript:gotToFunc('<?php echo $events[$i]['event_slug'];?>');" />
                            <!--</form>-->
                        </td>	
                    </tr>    
                    <?php endfor;?>
                    <?php else:?>
                    <tr class="evenCssClass">
                        <td colspan="5">No Sub Events</td>	
                    </tr>    
                    <?php endif;?>
                </tbody>
            </table>
            <?php elseif (($parentId != 0) && (!isset($_SESSION['seatsBuyForm']))):?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
            <h3 align="right">Date : <?php echo $events[0]['event_start_date'];?></h3>
            <h3 align="right">Time : <?php echo date('h:i',strtotime($events[0]['event_start_time']));?></h3>
            <h3 align="right">Location : <?php echo ($events[0]['location_name'] == "") ? "--" : $events[0]['location_name'];?><?php echo ($events[0]['location_town'] != "") ? (", ".$events[0]['location_town']) : "";?></h3><br />
            <div id="SeatMap"><img src="<?php echo home_url( '/' ); ?>wp-content/uploads/2012/03/o2_arena-london-end_stage-8339_f.jpg" /></div>
            <div class="clearH"></div>
			<table border="0" cellpadding="0" cellspacing="0">
            	<thead class="tableHeading">
                	<th>Seat Type</th>
                	<th>Price</th>
                	<th>Qunatity</th>
                	<th>Buy</th>
                </thead>
                <tbody>	
                	<?php for($i=0; $i<count($seatTypes); $i++):?>
                    <form name="seatsBuyForm" id="seatsBuyForm" method="post">
                    <tr class="<?php echo ($i % 2 == 0) ? 'evenCssClass' : 'oddCssClass';?>">
                        <td><?php echo ucwords($seatTypes[$i]['seat_type']);?></td>	
                        <td>$<?php echo $seatTypes[$i]['price'];?></td>	
                        <td><input class="smallTextBox" type="text" name="seatType[Qty]" value="1" /><input class="smallTextBox" type="hidden" name="seatType[Id]" value="<?php echo $seatTypes[$i]['id'];?>" /></td>	
                        <td><input type="submit" value="Buy" class="seatBuyButton" name="seatTypeSubmitButton" /></td>	
                    </tr>    
                    </form>                    
                    <?php endfor;?>
                </tbody>
            </table>
            <?php elseif (isset($_SESSION['seatsBuyForm'])):?>
            <?php
				/* Run the loop to output the post.
				 * If you want to overload this in a child theme then include a file
				 * called loop-single.php and that will be used instead.
				 */
				//get_template_part( 'loop', 'single' );
			?>
<!--            
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">

  function initialize() {
	var mapDiv = document.getElementById('map-canvas');
	var map = new google.maps.Map(mapDiv, {
	  center: new google.maps.LatLng(53.275, -9.0185),
	  zoom: 13,
	  mapTypeId: google.maps.MapTypeId.ROADMAP
	});
  
	google.maps.event.addListenerOnce(map, 'tilesloaded', function() {
	  window.setTimeout(function() {
		map.panTo(new google.maps.LatLng(53.275, -9.06532));
	  }, 1000);
	});
	var latLng = new google.maps.LatLng(53.275, -9.06532);
	  var marker = new google.maps.Marker({
		position: latLng,
		map: map
	  });
	  //infowindow.open(map,marker);
  }

  google.maps.event.addDomListener(window, 'load', initialize);
</script>
<div id="map-canvas" style="width: 500px; height: 400px"></div>
-->
			<h1 class="entry-title"><?php the_title(); ?></h1>
            <h3 align="right">Date : <?php echo $events[0]['event_start_date'];?></h3>
            <h3 align="right">Time : <?php echo date('h:i',strtotime($events[0]['event_start_time']));?></h3>
            <h3 align="right">Location : <?php echo ($events[0]['location_name'] == "") ? "--" : $events[0]['location_name'];?><?php echo ($events[0]['location_town'] != "") ? (", ".$events[0]['location_town']) : "";?></h3>
            <form id="custom_booking_notification_mail_form" name="custom_booking_notification_mail_form" method="post" action="">
            <table id="custom_booking_notification_mail_table" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td><h3>Ticket Type : </td>
                    <td><h3><?php echo ucwords($_SESSION['seatsBuyForm']['seat_type']);?></h3></td>
                </tr>
                <tr>
                    <td><h3>Ticket Price : </td>
                    <td><h3>&euro;<?php printf("%01.2f", ($_SESSION['seatsBuyForm']['price'] * $_SESSION['seatsBuyForm']['selectedQty']));?></h3></td>
                </tr>
                <tr>
                    <td><h3>Number of Tickets : </td>
                    <td><h3><?php echo $_SESSION['seatsBuyForm']['selectedQty'];?></h3></td>
                </tr>
                <tr>
                    <td>Email * </td>
                    <td><input type="text" name="cutomer[email]" id="cutomer_email" /></td>
                </tr>
                <tr>
                    <td>First Name * </td>
                    <td><input type="text" name="cutomer[fname]" id="cutomer_fname" /></td>
                </tr>
                <tr>
                    <td>Last Name * </td>
                    <td><input type="text" name="cutomer[lname]" id="cutomer_lname" /></td>
                </tr>
                <tr>
                    <td>Billing Address * </td>
                    <td><textarea name="cutomer[address]" rows="3" cols="30" id="cutomer_address"></textarea></td>
                </tr>
                <tr>
                    <td>Post code * </td>
                    <td><input type="text" name="cutomer[postcode]" id="customer_postcode" /></td>
                </tr>
                <tr>
                    <td>Name of The Card * </td>
                    <td><input type="text" name="cutomer[card_name]" id="customer_card_name" /></td>
                </tr>
                <tr>
                    <td>Credit/debit card type * </td>
                    <td>
                    	<select name="cutomer[card_type]" id="card_type">
                        	<option name="">Select your card type</option>                        
                        	<option name="visa">Visa</option>
                        	<option name="visa_debit">Visa debit</option>
                        	<option name="credit">Credit</option>
                        	<option name="visa_electron">Visa electron</option>
                        	<option name="master_card(credit)">Master card(credit)</option>           
                        	<option name="mastercard(debit)">Master card(debit)</option>           
                        	<option name="maestro">Maestro</option>                                                                   
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Credit/debit card number * </td>
                    <td><input type="text" name="cutomer[card_number]" id="customer_card_number" /></td>
                </tr>
                <tr>
                    <td>Security number * </td>
                    <td><input type="text" name="cutomer[security_number]" id="customer_security_number" /></td>
                </tr>
                <tr>
                    <td>Expiry date * </td>
                    <td>
                    	<select id="customer_card_expiry_month" name="cutomer[card_expiry_month]" class="smallWithInDate">
                            <option value="">MM</option>
                            <?php for ($i = 1; $i <= 12; $i++):?>
                                <option value="<?php echo $i;?>"><?php echo date("F", mktime(0, 0, 0, $i+1, 0, 0));?></option>
                            <?php endfor;?>
						</select>
                    &nbsp;
                    	<select id="customer_card_expiry_year" name="cutomer[card_expiry_year]" class="smallWithInDate">
                            <option value="">YY</option>
                            <?php for ($i = date("Y") ; $i <= date("Y")+25; $i++):?>
                                <option value="<?php echo $i;?>"><?php echo $i;?></option>
                            <?php endfor;?>
						</select>
                    </td>
                </tr>
                <tr>
                    <td>Start date </td>
                    <td>
                    	<select id="customer_card_start_month" name="cutomer[card_start_month]" class="smallWithInDate">
                            <option value="">MM</option>
                            <?php for ($i = 1; $i <= 12; $i++):?>
                                <option value="<?php echo $i;?>"><?php echo date("F", mktime(0, 0, 0, $i+1, 0, 0));?></option>
                            <?php endfor;?>
						</select>
                    &nbsp;
                    	<select id="customer_card_start_year" name="cutomer[card_start_year]" class="smallWithInDate">
                            <option value="">YY</option>
                            <?php for ($i = date("Y") ; $i <= date("Y")+25; $i++):?>
                                <option value="<?php echo $i;?>"><?php echo $i;?></option>
                            <?php endfor;?>
						</select>
                    </td>
                </tr>
                <tr>
                    <td>Delivery Address * </td>
                    <td><textarea name="cutomer[delievery_address]" rows="3" cols="30" id="cutomer_delievery_address"></textarea></td>
                </tr>
                <tr>
                    <td>Telephone Number * </td>
                    <td><input type="text" name="cutomer[telno]" id="customer_telno" /></td>
                </tr>
                <tr>
                    <td colspan="2" align="center"><input id="emailSubmit" class="buyButton" type="button" name="emailSubmit" value="Submit"  /></td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <div id="bookingMessage" class="validationMessageForBooking hide"></div>
                    </td>
                </tr>
            </table> 
            </form>        
            
            <?php endif;?>
			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
