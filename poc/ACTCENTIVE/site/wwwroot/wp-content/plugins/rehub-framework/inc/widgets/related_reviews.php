<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
/**
 * Plugin Name: News Widget
 */

add_action( 'widgets_init', 'related_review_load_widget' );

function related_review_load_widget() {
	register_widget( 'related_review_widget' );
}

class related_review_widget extends WP_Widget {

    function __construct() {
		$widget_ops = array( 'classname' => 'posts_widget', 'description' => esc_html__('A widget that displays related reviews. Works only on single review post page', 'rehub-framework') );
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'related_review_widget' );
        parent::__construct('related_review_widget', esc_html__('ReHub: Related Reviews', 'rehub-framework'), $widget_ops, $control_ops);
    }

/**
 * How to display the widget on the screen.
 */
function widget( $args, $instance ) {
	extract( $args );

if(is_singular('post')) :
	/* Our variables from the widget settings. */
	$title = apply_filters('widget_title', $instance['title'] );
	$number = $instance['number'];
	global $post;
	$category = get_the_category($post->ID); $first_cat = $category[0]->term_id;
	
	$query = array(
		'posts_per_page' => $number, 
		'post_status' => 'publish', 
		'ignore_sticky_posts' => 1, 
		'cat' => $first_cat, 
		'meta_key' => 'rehub_review_overall_score', 
		'orderby' => 'meta_value_num',
		'post__not_in'     => array($post->ID),
        'meta_query' => array(
                array(
                'key' => 'rehub_framework_post_type',
                'value' => 'review',
                'compare' => 'LIKE',
                )
        )		
	);
	
	
	$loop = new WP_Query($query);
	/* Before widget (defined by themes). */
	echo ''.$before_widget;
	
	if ($loop->have_posts()) :

	/* Display the widget title if one was input (before and after defined by themes). */
	if ( $title )
		echo '<div class="title">' . $title . '</div>';

	?>
		<div class="tabs-item clearfix">
		<?php  $i=0; while ($loop->have_posts()) : $loop->the_post();$i++; ?>	
			<div class="clearfix flowhidden<?php if($i != $number): ?> mb15 pb15 border-grey-bottom<?php endif;?>">
	            <figure class="floatleft width-100 img-maxh-100 img-width-auto">
	            	<?php wpsm_thumb('minithumb'); ?>
	            </figure>
	            <div class="detail floatright width-100-calc pl15 rtlpr15">
		            <h5 class="mt0 lineheight20 fontnormal font95"><a href="<?php the_permalink();?>"><?php the_title();?></a></h5>
	            	<div class="post-meta">
	                	<?php meta_small( false, false, true ); ?>
	                </div>
		            <?php rehub_format_score('small') ?>
	            </div>
            </div>	
		<?php endwhile; ?>
		</div>
		<?php wp_reset_query(); ?>
		<?php else: ?>
		<?php endif; ?>	
	<?php

	/* After widget (defined by themes). */
	echo ''.$after_widget;
endif;	
}


	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = strip_tags( $new_instance['number'] );

		return $instance;
	}


	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => esc_html__('Related reviews', 'rehub-framework'), 'number' => 5);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
	

		<p>
			<label for="<?php echo ''.$this->get_field_id( 'title' ); ?>"><?php esc_html_e('Title of widget:', 'rehub-framework'); ?></label>
			<input  type="text" class="widefat" id="<?php echo ''.$this->get_field_id( 'title' ); ?>" name="<?php echo ''.$this->get_field_name( 'title' ); ?>" value="<?php echo ''.$instance['title']; ?>"  />
		</p>
		<p>
			<label for="<?php echo ''.$this->get_field_id( 'number' ); ?>"><?php esc_html_e('Number of posts to show:', 'rehub-framework'); ?></label>
			<input  type="text" class="widefat" id="<?php echo ''.$this->get_field_id( 'number' ); ?>" name="<?php echo ''.$this->get_field_name( 'number' ); ?>" value="<?php echo ''.$instance['number']; ?>" size="3" />
		</p>


	<?php
	}
}

?>