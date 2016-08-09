<?php
/**
 * Daily-Menu Widget goals is to provide the menu of the current day, if the current time
 * (of the DB server) is before 16h (4PM) or the next menu if after.
 */
class DailyMenuWidget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
				'dmWidget', // Base ID
				__( 'Daily Menu', DM_DOMAIN_NAME), // Name
				array( 'description' => __( 'Daily Menu Widget shows the menu of the day', DM_DOMAIN_NAME), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		$nextMenu = ListMenus::getNextMenu();
		$types = ListTypes::getAllTypesTable();

		if (!$nextMenu) {
			return ;
		}
		
		$html = $args['before_widget'];
		
		$html .= $args['before_title'];
		
		$day = new DateTime($nextMenu->getDate());
		/* translators:
		 * %1$s is the name of the day,
		 * %2$d is the number of the day within month
		 * %3$s is the name of the month*/
		$html .= sprintf(esc_html__('Menu of %1$s, %2$d of %3$s',DM_DOMAIN_NAME),
					date_i18n("l",$day->getTimestamp()),
					date_i18n("j",$day->getTimestamp()),
					date_i18n("F",$day->getTimestamp()));
		
		$html .= $args['after_title'];
		
		$html .= "<dl style=\"text-align: center;\">";
		
		// dish lines
		foreach ($types as $type => $typename) {
			if (null !== $nextMenu->getDish($type)->getID() && "" !== $nextMenu->getDish($type)->getID()) {
				$html .= "<dt>".esc_html($typename)."</dt>";
				$html .= "<dd>".esc_html($nextMenu->getDish($type)->getName())."</dd>";
			}
		}
		
		$html .= "</dl>";
		
		if (isset($instance['dm_widget_page_link'])) {
			$html .= "<ul>";
			$html .= "<li>";
			$html .= "<a href=\"".esc_url($instance['dm_widget_page_link'])."\">";
			$html .= esc_html__("Menus of the week",DM_DOMAIN_NAME);
			$html .= "</a>";
			$html .= "</li>";
			$html .= "</ul>";
		}

		$html .= $args['after_widget'];
		
		echo $html;

	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$html .= "<p>";
		$html .= "<label for=\"".$this->get_field_id( 'dm_widget_page_link' )."\">";
		$html .= esc_html__("Link to a page with all menus of the week :",DM_DOMAIN_NAME);
		$html .= "</label>";
		$html .= "<input class=\"widefat\" id=\""
				.esc_html($this->get_field_id( 'dm_widget_page_link' ))
				."\" name=\""
				.esc_html($this->get_field_name( 'dm_widget_page_link' ))
				."\" type=\"text\" value=\""
				.esc_url($instance['dm_widget_page_link']).
				"\"	/>";
		$html .=  "</p>";
		echo $html;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['dm_widget_page_link'] = ( ! empty( $new_instance['dm_widget_page_link'] ) ) ? strip_tags( $new_instance['dm_widget_page_link'] ) :' ';
		
		return $instance;
	}

}