<?php
/*
  Plugin Name: One Post Widget
  Description: Show recent post in widget area so the widget title/content itself is given post title and contents. You can choose queue which post to show from recent posts or just give specific ID.
  Author: EagerFish
  Plugin URI: http://eagerfish.eu/wordpress-plugin-one-post-widget
  Author URI: http://eagerfish.eu/about/
  Version: 1.0
*/



class OnePostWidget {

	var $codever = '1.0';

	var $default_options = array(
		'queue_1' => 1,
		'queue_2' => 2,
		'queue_3' => 3,
		'queue_4' => 4,
		'queue_5' => 5,
		'pid_1' => 0,
		'pid_2' => 0,
		'pid_3' => 0,
		'pid_4' => 0,
		'pid_5' => 0,
	);

	function OnePostWidget() {
		global $wpdb;

		add_action('init', array(&$this, 'initOnePostWidget'), 11 );
	}
	
	function initOnePostWidget(){
		global $wpdb;

		if($options_saved = get_option('one_post_widget_options')){
			//options already there
		} else {
			add_option("one_post_widget_codeversion", $this->codever);
			add_option('one_post_widget_options', $this->default_options);
		}
		
		/* add 5 widgets and their controls into theme widgets */
		if ( !function_exists('wp_register_sidebar_widget') || !function_exists('register_widget_control') )
			return;
		else {
			foreach(range(1, 5) as $nr){
				wp_register_sidebar_widget(
					'one_post_widget_widgets' . $nr,
					'One Post Widget #' . $nr,
					array(&$this, 'widget_onePostWidget' . $nr),
					array('description' => '')
				);
				
				wp_register_widget_control(
					'one_post_widget_widgets' . $nr,
					'One Post Widget Control' . $nr,
					array(&$this, 'widget_onePostWidgetControl' . $nr)
				);
			}
			
		}
	}

	function widget_onePostWidgetControl1() {
		$this->widget_onePostWidgetControl(1);
	}
	function widget_onePostWidgetControl2() {
		$this->widget_onePostWidgetControl(2);
	}
	function widget_onePostWidgetControl3() {
		$this->widget_onePostWidgetControl(3);
	}
	function widget_onePostWidgetControl4() {
		$this->widget_onePostWidgetControl(4);
	}
	function widget_onePostWidgetControl5() {
		$this->widget_onePostWidgetControl(5);
	}
	
	function widget_onePostWidget1($args) {
		$this->widget_onePostWidget($args, 1);
	}
	function widget_onePostWidget2($args) {
		$this->widget_onePostWidget($args, 2);
	}
	function widget_onePostWidget3($args) {
		$this->widget_onePostWidget($args, 3);
	}
	function widget_onePostWidget4($args) {
		$this->widget_onePostWidget($args, 4);
	}
	function widget_onePostWidget5($args) {
		$this->widget_onePostWidget($args, 5);
	}
	
	/* widget control code for admin */
	function widget_onePostWidgetControl($nr) {

		/* default values for options */
		$options = $this->default_options;

		/* get saved options and merge */
		if($options_saved = get_option('one_post_widget_options')){
			$options = array_merge($options, $options_saved);
		}

		/* update new values to options */
		if(isset($_POST['one_post_widget_save_values'])){
			$options['queue_' . $nr] = (int)$_POST['one_post_widget_queue_' . $nr];
			$options['pid_' . $nr] = (int)$_POST['one_post_widget_pid_' . $nr];
			
			update_option('one_post_widget_options', $options);
		}

		/* render options  */
		?>
		<input type="text" width="6" name="one_post_widget_queue_<?php echo $nr; ?>" id="one_post_widget_queue_<?php echo $nr; ?>" value="<?php echo $options['queue_' . $nr]; ?>" /><br />
		<label for="one_post_widget_queue_<?php echo $nr; ?>">Which post in order to show from recent posts queue (1-x), 0 if none.</a>

		<input type="text" width="6" name="one_post_widget_pid_<?php echo $nr; ?>" id="one_post_widget_pid_<?php echo $nr; ?>" value="<?php echo $options['pid_' . $nr]; ?>" /><br />
		<label for="one_post_widget_pid_<?php echo $nr; ?>">Post ID which to show in widget, 0 if none.</a>

		<input type="hidden" id="one_post_widget_save_values" name="one_post_widget_save_values" value="1" />
		<?php
		
	}


	/* widget code */
	function widget_onePostWidget($args, $nr) {
		global $wpdb;
		extract($args);
		
		/* get default options */
		$options = $this->default_options;

		/* get saved options and merge */
		if($options_saved = get_option('one_post_widget_options')){
			$options = array_merge($options, $options_saved);
		}

		$queue = 0;
		$pid = 0;

		if(isset($options['queue_' . $nr]) && $options['queue_' . $nr]){
			$queue = $options['queue_' . $nr];
		}
		if(isset($options['pid_' . $nr]) && $options['pid_' . $nr]){
			$pid = $options['pid_' . $nr];
		}
		
		$post = null;

		if($queue){
			$items = wp_get_recent_posts(array(
				'numberposts' => '1',
				'offset' => ($queue - 1)
			));
			$post = isset($items[0]) ? $items[0] : null;
		} else if($pid){
			$post = (array) get_post( $pid );
		}
		
		if($post){
			if(trim($post['post_excerpt'])){
				$content = $post['post_excerpt'];
			} else {
				$content = $post['post_content'];
			}
			
			$content = apply_filters('one_post_widget_content', $content);
			$post['post_title'] = apply_filters('one_post_widget_title', $post['post_title']);
			
			echo $before_widget;
			echo $before_title . '<a href="' . $post['guid'] . '">' . $post['post_title'] . '</a>' . $after_title;
			echo apply_filters('one_post_widget_content' . $nr, $content);
			echo $after_widget;
		}
	}
	
}

$onePostWidget = new OnePostWidget();
