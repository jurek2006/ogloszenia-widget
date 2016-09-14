<?php
/*
Plugin Name: Ogłoszenia Widget JS
Description: Widżet do strony Centrum Sztuki wyświetlający listę ogłoszeń. Wyświetlane są tylko ogłoszenia opublikowane (czyli data publikacji już minęła) i których termin widoczności na widżecie jeszcze nie minął
Version: 0.2
Author: Jurek Skowron
*/

class ogloszeniaWidgetJS extends WP_Widget {

        // constructor
        // function ogloszeniaWidgetJS() {
        //         parent::WP_Widget(false, $name = __('Ogłoszenia Widget', 'wp_widget_plugin') );
        // }

        function __construct() {
			// parent::__construct(
			// 	'foo_widget', // Base ID
			// 	__( 'Widget Title', 'text_domain' ), // Name
			// 	array( 'description' => __( 'A Foo Widget', 'text_domain' ), ) // Args
			// );

			// parent::__construct(false, $name = __('Ogłoszenia Widget', 'wp_widget_plugin') );

			parent::__construct(
				'ogloszenia-widget', // Base ID
				__( 'Widżet ogłoszeń', 'ogloszenia-widget' ), // Name
				array( 'description' => __( 'Widżet ogłoszeń CS' ), ) // Args
			);
		}

        // widget form creation
        function form($instance) {      
        // Check values
			if( $instance) {
				 $title = esc_attr($instance['title']);
				 $text = esc_attr($instance['text']);
				 $textarea = esc_textarea($instance['textarea']);
			} else {
				 $title = '';
				 $text = '';
				 $textarea = '';
			}
			?>
			
			<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'wp_widget_plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			
			<p>
			<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text:', 'wp_widget_plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="text" value="<?php echo $text; ?>" />
			</p>
			
			<p>
			<label for="<?php echo $this->get_field_id('textarea'); ?>"><?php _e('Textarea:', 'wp_widget_plugin'); ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id('textarea'); ?>" name="<?php echo $this->get_field_name('textarea'); ?>"><?php echo $textarea; ?></textarea>
			</p>
			<?php
        }

        // widget update
        function update($new_instance, $old_instance) {
                // update widget

			  $instance = $old_instance;
			  // Fields
			  $instance['title'] = strip_tags($new_instance['title']);
			  $instance['text'] = strip_tags($new_instance['text']);
			  $instance['textarea'] = strip_tags($new_instance['textarea']);
			 return $instance;

        }

        // widget display
        function widget($args, $instance) {
                extract( $args );
				
				// these are the widget options
			   $title = apply_filters('widget_title', $instance['title']);
			   $text = $instance['text'];
			   $textarea = $instance['textarea'];
			   
				//-------------------------------------------------------------------------------------
				
				$params = array( 	'limit' => -1,
									'where'   => '(termin_opublikowania.meta_value < NOW()) AND (termin_widocznosci.meta_value > NOW())',
									'orderby'  => 'termin_opublikowania.meta_value DESC');
                
                //get pods object
				//wczytuje $params zdefiniowane powyżej (w miejscu tworzenia paska kategorii)
				$pods = pods( 'ogloszenia', $params );
                //loop through records
                if ( $pods->total() > 0 ) {
					//jeśli znaleziono ogłoszenia do wyświetlenia na widżecie - wtedy dopiero się on pojawia
					echo $before_widget;
				   // Display the widget
				   echo '<div class="ogloszenia-widget">';
				
				   // Check if title is set
				   if ( $title ) {
					  echo '<h3>'.$title.'</h3>';
				   }
					
                    while ( $pods->fetch() ) {
                        //Put field values into variables
                        $ogloszenie_title = $pods->display('name');
						$post_content = $pods-> display('post_content');
						$kategorie = $pods-> display('kategorie');
						$permalink = $pods->field('permalink' );
						$krotki_opis = $pods-> display('krotki_opis');
						$termin_opublikowania = $pods-> display('termin_opublikowania');
						
						?>
                        <p><span><?php echo $kategorie ?></span>
                        <a href="<?php echo esc_url( $permalink); ?>" rel="bookmark"><?php echo $ogloszenie_title ?></a></p>
						<?php
	
					}//while ( $pods->fetch() )
					
					   echo '</div>';
					   echo $after_widget;
					}//if ( $pods->total() > 0 )

				//--------------------------------------------------------------------------------------------------------
				

			
			   
        }
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("ogloszeniaWidgetJS");'));
?>