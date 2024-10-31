<?php
/**
 * The template for displaying all single posts booking
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @since 1.0
 * @version 1.0
 */
get_header(); ?>
<div id="primary" class="content-area">

    <?php
    // Start the loop.
    while ( have_posts() ) : the_post();
      /*
      * Include the post format-specific template for the content. If you want to
      * use this in a child theme, then include a file called called content-___.php
      * (where ___ is the post format) and that will be used instead.
      */
        $booking = get_post_meta(get_the_ID(),'',false);
            if($booking != array()){?>
             
              <div id="ns-single-booking-container">
               <h1>Booking: <?php the_title(); ?></h1>
                <form role="form">
                  <div class="form-group">
                      <label>Date :</label>
                      <?php echo $booking['date_in'][0]; ?>
                  </div>
                  <!--<div class="form-group">
                      <label>Ending Date: </label>
                      <?php echo $booking['date_out'][0]; ?>
                  </div>-->
                  <div class="form-group">
                      <label>From:</label> 
                      <?php echo $booking['hour_in'][0]; ?>
                  </div>
                  <div class="form-group"> 
                      <label>To:</label> 
                      <?php echo $booking['hour_out'][0]; ?>
                  </div>
                  <div class="form-group">
                      <label>Product : </label>
                      <?php echo $booking['product_name'][0]; ?>
                  </div>
                  <div class="form-group">
                      <?php 
					  $prod_img_id = get_post_meta($booking['product_id'][0],'_thumbnail_id', true);
                      if(isset($prod_img_id) && $prod_img_id != ''){
						    echo '<label>Image : </label>';
							echo wp_get_attachment_image($prod_img_id);
					  }
   
					  ?>
                  </div>
                  <div class="form-group">
                      <label>Price : </label>
                      <?php
                      $booked_price = get_post_meta($booking['product_id'][0], 'ns_agenda_hourly_price', true);
                      $price = $booked_price * $booking['ns_agenda_quantity'][0];
                      echo  wc_price($price);
                       ?>
                  </div>
                   <div class="form-group">
                      <label>Link : </label>
                      <?php echo '<a href="'.get_permalink($booking['product_id'][0]).'">';
                      echo $booking['product_name'][0];
                       echo '</a>';
                       ?>
                  </div>
                   </form>     
               </div>  
<?php }
     // get_template_part( 'content', get_post_format() );
      // If comments are open or we have at least one comment, load up the comment template.
      if ( comments_open() || get_comments_number() ) :
        comments_template();
      endif;
    endwhile; ?>

</div><!-- .content-area -->
<?php get_footer(); ?>