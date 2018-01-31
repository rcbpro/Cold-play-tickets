<?php get_header(); ?>
<?php 
	require $_SERVER['DOCUMENT_ROOT'].'/wp_ex_functions.php';
	$splitted_url = explode("/", $_SERVER['REQUEST_URI']);			
	foreach($splitted_url as $eachSplit){
		if (!empty($eachSplit)) $newSplittedUrls[] = $eachSplit;
	}
	$events = $DBConn->getAllEventsListed(end($newSplittedUrls));
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
	color:#000;
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
<div id="main">
		<div id="container">
			<div id="content" role="main">
			<table border="0" cellpadding="0" cellspacing="0">
            	<thead class="tableHeading">
                	<th align="center">Event</th>
                </thead>
                <tbody>	
                	<?php for($i=0; $i<count($events); $i++):?>
                    <tr class="<?php echo ($i % 2 == 0) ? 'evenCssClass' : 'oddCssClass';?>">
                        <td><a href="<?php echo home_url( '/' ); ?>tickets/<?php echo $events[$i]['event_slug'];?>"><?php echo $events[$i]['event_name'];?></a></td>	
                    </tr>    
                    <?php endfor;?>
                </tbody>
            </table>

			<?php
			/* Run the loop to output the page.
			 * If you want to overload this in a child theme then include a file
			 * called loop-page.php and that will be used instead.
			 */
			//get_template_part( 'loop', 'page' );
			?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
