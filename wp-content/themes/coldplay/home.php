<?php get_header();
/*Template Name:Home*/ ?>
		<img src="<?php echo get_template_directory_uri(); ?>/images/banner.jpg" id="BannerImg" />
        <div class="clearH20"></div>
<div id="main">

		<div id="container">
			<div id="content" role="main">
            <h1 class="entry-title">Coldplay CONCERT TICKETS</h1>

			<?php the_post(); ?>
            <?php the_content(); ?>

			</div><!-- #content -->
		</div><!-- #container -->
        
<?php get_sidebar(); ?>
<div class="clearH10"></div>
<?php get_footer(); ?>