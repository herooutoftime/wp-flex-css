<?php
class Flex_Css_Admin_Table extends WP_List_Table {

	var $empty_data = array(
		array(
			'id' => '0',
			'property' => '',
			'type' => '',
			'value' => '',
		)
	);

	private $matches = array();
	/**
	* Constructor, we override the parent to pass our own arguments
	* We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
	*/
	function __construct() {
		parent::__construct( array(
			'singular'=> 'wp_list_text_link', //Singular label
			'plural' => 'wp_list_test_links', //plural label, also this well be one of the table css class
			'ajax'   => false //We won't support Ajax for this table
		) );
		$this->matches = Flex_Css_Helper::get_matches();
	}

	function extra_tablenav( $which ) {
		if ( $which == "top" ){
			//The code that goes before the table is here
//			echo "<button class='button button-primary add-row' data-event='add-row'>Add new row</button>";
		}
		if ( $which == "bottom" ){
			//The code that goes after the table is there
			echo "<button class='button button-primary add-row' data-event='add-row'>Add new row</button>";
		}
	}

	function get_columns() {
		return $columns= array(
			'id' => __('ID'),
			'property' => __('Property'),
			'type' => __('Type'),
			'value' => __('Value'),
		);
	}

	function display_tablenav( $which )
	{
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">

			<div class="alignleft actions">
				<?php $this->bulk_actions(); ?>
			</div>
			<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>
			<br class="clear" />
		</div>
		<?php
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'id' => array('id', false),
			'property'     => array('property',false),
			'type' => array('type', false),
			'value'    => array('value',false),
		);
		return $sortable_columns;
	}

	function column_default($item, $column_name) {
		switch($column_name){
			case 'id':
			case 'property':
			case 'type':
			case 'value':
				return $item[$column_name];
			default:
				return print_r($item,true); //Show the whole array for troubleshooting purposes
		}
	}

	function column_id($item) {
		echo '<input name="flex_css_data['.$item['id'].'][id]" data-colname="id" type="text" value="'.stripslashes($item['id']).'" />';
	}
	function column_property($item) {
		$icon_state = Flex_Css_Helper::get_match($item['property']) ? 'yes' : 'no';
		$icon_color = Flex_Css_Helper::get_match($item['property']) ? '#46b450' : '#dc3232';
		//Return the title contents
		return sprintf('%1$s %2$s %3$s',
			'<span class="delete-row dashicons dashicons-trash" style="padding:3px 0;color:#dc3232"></span>'
			,'<input class="autocomplete" name="flex_css_data['.$item['id'].'][property]" data-colname="property" type="text" value="'.stripslashes($item['property']).'" />'
			,'<span class="dashicons dashicons-'.$icon_state.'" style="padding:3px 0;color:'.$icon_color.'"></span>'
//			,$this->row_actions($actions, true)
		);
	}
	function column_type($item) {
		$types = array(
			'text' => 'Text',
			'colorpicker' => 'Colorpicker',
		);
		$opts = array();
		foreach ($types as $value => $display) {
			$selected = selected( $item['type'], $value, false );
			$opts[] = "<option {$selected} value='{$value}'>{$display}</option>";
		}
		echo '<select class="changetype" name="flex_css_data['.$item['id'].'][type]" data-colname="type">'. implode('', $opts) .'</select>';
	}
	function column_value($item) {
		return sprintf('%1$s %2$s',
			'<input class="input-'.$item['type'].'" name="flex_css_data['.$item['id'].'][value]" data-colname="value" type="text" value="'.stripslashes($item['value']).'" />'
			,''
//			,'<span class="delete-row dashicons dashicons-yes" style="padding:3px 0;color:#46b450"></span>'
		);
	}

	function get_table_classes() {
		return array( 'flex-css-table', 'widefat', 'fixed', 'striped', $this->_args['plural'] );
	}

	function prepare_items() {

		$per_page = 25;
		$columns = $this->get_columns();
		$hidden = array('id');
		$sortable = $this->get_sortable_columns();
		$this->row_actions(array('test' => 'test'), true);

		$this->_column_headers = array($columns, $hidden, $sortable);

		$this->process_bulk_action();

		$data = Flex_Css_Helper::get_option(true, true);
//		$data = maybe_unserialize(get_option('flex_css_data'));
		if(!is_array($data))
			$data = $this->empty_data;

		/**
		 * This checks for sorting input and sorts the data in our array accordingly.
		 *
		 * In a real-world situation involving a database, you would probably want
		 * to handle sorting by passing the 'orderby' and 'order' values directly
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary.
		 */
		function usort_reorder($a,$b){
			$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id'; //If no sort, default to title
			$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
			$result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
			return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
		}
		usort($data, 'usort_reorder');

		/**
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();

		/**
		 * REQUIRED for pagination. Let's check how many items are in our data array.
		 * In real-world use, this would be the total number of items in your database,
		 * without filtering. We'll need this later, so you should always include it
		 * in your own package classes.
		 */
		$total_items = count($data);


		/**
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to
		 */
		$data = array_slice($data,(($current_page-1)*$per_page),$per_page);

		$this->items = $data;

		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
		) );
	}

	function display_rows_or_placeholder() {
		if ( $this->has_items() ) {
			$this->display_rows();
		} else {
			$this->no_items();
		}
	}
}