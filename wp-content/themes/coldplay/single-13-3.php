<?php get_header(); ?>
<?php 
	session_start();
	require $_SERVER['DOCUMENT_ROOT'].'/wp_ex_functions.php';
	$splitted_url = explode("/", $_SERVER['REQUEST_URI']);			
	foreach($splitted_url as $eachSplit){
		if (!empty($eachSplit)) $newSplittedUrls[] = $eachSplit;
	}
	$events = $DBConn->getTheEventPostDetailsByEventSlugName(end($newSplittedUrls));

	if ("POST" == $_SERVER['REQUEST_METHOD']){
		if (isset($_POST['post_id'])){
			for($i=0; $i<count($events); $i++){
				if ($events[$i]['post_id'] == $_POST['post_id']){
					$_SESSION['eventsBuyForm'] = $events[$i];
					$seatTypes = $DBConn->getSeatTpes();					
				}
			}
		}else if (isset($_POST['term_id'])){
			$seatTypes = $DBConn->getSeatTpes();					
			for($i=0; $i<count($seatTypes); $i++){
				if ($seatTypes[$i]['term_id'] == $_POST['term_id']){
					$_SESSION['seatsBuyForm'] = $seatTypes[$i];
				}
			}
		}	
	}
?>
<style>
.tableHeading{
	color:#8299C0;
}
.evenCssClass{
	background-color:#8299C0;
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size:11px;
	font-weight:bold;	
}
.oddCssClass{
	background-color:#FFFFFF;
	color:#8299C0;
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size:11px;
	font-weight:bold;
}
.buyButton{
	cursor:pointer;
	color:#8299C0 !important;
	font-weight:bold;	
}	
.buyButton:hover{
	cursor:pointer;
	color:#FFF !important;	
	font-weight:bold;	
}	
.seatBuyButton{
	cursor:pointer;
	color:#8299C0 !important;
	font-weight:bold;	
}
.seatBuyButton:hover{
	cursor:pointer;
	color:#FFF !important;	
	font-weight:bold;	
}
</style>
<script type="text/javascript" language="javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function(){
	$("input.buyButton").click(function(){
		$("#eventsBuyForm").submit();
	})
	$("input.seatBuyButton").click(function(){
		$("#seatsBuyForm").submit();
	})
});
</script>
<div id="main">
		<div id="container">
			<div id="content" role="main">
			
            <?php if (empty($_POST)):?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<table border="0" cellpadding="0" cellspacing="0">
            	<thead class="tableHeading">
                	<th>Event</th>
                	<th>Date</th>
                	<th>Venue</th>
                	<th>City</th>                                                            
                	<th>Buy / Sell</th>                                                                                
                </thead>
                <tbody>	
                	<?php for($i=0; $i<count($events); $i++):?>
                    <tr class="<?php echo ($i % 2 == 0) ? 'evenCssClass' : 'oddCssClass';?>">
                        <td><?php echo $events[$i]['event_name'];?></td>	
                        <td><?php echo implode("-",array_reverse(explode("-", $events[$i]['event_start_date'])));?></td>	
                        <td><?php echo $events[$i]['location_name'];?></td>	
                        <td><?php echo $events[$i]['location_town'];?></td>	
                        <td><form name="eventsBuyForm" id="eventsBuyForm" method="post">
                        	<input type="hidden" name="post_id" value="<?php echo $events[$i]['post_id'];?>" />
                            <input type="button" value="Buy" class="buyButton" />
                            </form>
                        </td>	
                    </tr>    
                    <?php endfor;?>
                </tbody>
            </table>
            <?php elseif (isset($_POST['post_id'])):?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
            <img src="<?php echo home_url( '/' ); ?>/images/o2_arena-london-end_stage-8339_f.jpg" /><br /><br />
			<table border="0" cellpadding="0" cellspacing="0">
            	<thead class="tableHeading">
                	<th>Description</th>
                	<th>Price</th>
                	<th>Buy</th>
                </thead>
                <tbody>	
                	<?php for($i=0; $i<count($seatTypes); $i++):?>
                    <tr class="<?php echo ($i % 2 == 0) ? 'evenCssClass' : 'oddCssClass';?>">
                        <td><?php echo ucwords($seatTypes[$i]['name']);?></td>	
                        <td><?php echo $seatTypes[$i]['description'];?></td>	
                        <td><form name="seatsBuyForm" id="seatsBuyForm" method="post">
                        	<input type="hidden" name="term_id" value="<?php echo $seatTypes[$i]['term_id'];?>" />
                            <input type="button" value="Buy" class="seatBuyButton" />
                            </form>
                        </td>	
                    </tr>    
                    <?php endfor;?>
                </tbody>
            </table>
            <?php elseif (isset($_SESSION['seatsBuyForm'])):?>
            <?php
				/* Run the loop to output the post.
				 * If you want to overload this in a child theme then include a file
				 * called loop-single.php and that will be used instead.
				 */
				get_template_part( 'loop', 'single' );
			?>
            <h3>Ticket Type : <?php echo $_SESSION['seatsBuyForm']['name'];?></h3><br />
            <h3>Price : <?php echo $_SESSION['seatsBuyForm']['description'];?></h3>
            <?php endif;?>
			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
