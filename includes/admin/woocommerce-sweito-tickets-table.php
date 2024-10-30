<?php
/**
 * Admin Ticket Table - Inbox page
 * 
 * @package Inbox-For-WooCommerce-LTE
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class IBXFWL_WCSweitoTicketsTable extends WP_List_Table {
	
	public function __construct() {
		parent::__construct( array(
		'singular'=> esc_html__('WooCommerce Inbox', 'inbox-for-woocommerce'), //Singular label
		'plural' => esc_html__('WooCommerce Inboxes', 'inbox-for-woocommerce'), //plural label, also this well be one of the table css class
		'ajax'   => false //We won't support Ajax for this table
	   ) );
	}

	/**
	 * Define the columns that are going to be used in the table
	 *
	 * @return array $columns, the array of columns to use with the table
	 */
	public function get_columns() {
		return array(
			'cb' => '<input type="checkbox" />',
			'wcs_id' => esc_html__('ID', 'inbox-for-woocommerce'),
			'wcs_reference' => esc_html__('Reference', 'inbox-for-woocommerce'),
			'wcs_status' => esc_html__('Status', 'inbox-for-woocommerce'),
			'wcs_type' => esc_html__('Type', 'inbox-for-woocommerce'),
			'wcs_sender' => esc_html__('Sender', 'inbox-for-woocommerce'),
			'wcs_assigned' => esc_html__('Assigned', 'inbox-for-woocommerce'),
			'wcs_subject' => esc_html__('Subject', 'inbox-for-woocommerce'),
			'wcs_created_at' => esc_html__('Created At', 'inbox-for-woocommerce')
		);
	}

	/**
	 * Decide which columns to activate the sorting functionality on
	 *
	 * @return array $sortable, the array of columns that can be sorted by the user
	 */
	public function get_sortable_columns() {
		return array(
			'wcs_id'=> array('wcs_id', true),
			'wcs_sender'=> array('wcs_sender', true),
			'wcs_status'=> array('wcs_status', true),
			'wcs_type'=> array('wcs_type', true),
			'wcs_created_at'=> array('wcs_created_at', true),
			'wcs_assigned'=> array('wcs_assigned', true)
		);
	}

	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	public function prepare_items() {
		require_once(IBXFWL_SWEITO_INCLUDES_URL . '/DatabaseController.php');

		global $wpdb;
		$screen = get_current_screen();
	
		/* -- Preparing your query -- */
		$tableName = $wpdb->prefix . IBXFWL_Inbox_DatabaseController::DB_TICKETS_TABLE;
		$query = "SELECT * FROM $tableName";

		$currentUser = wp_get_current_user();
		$roles = ( array ) $currentUser->roles;

		$section = isset($_GET['section']) ? sanitize_text_field($_GET['section']) : '';
		$inboxUserId = 0;
		if ( !in_array('administrator', $roles) && in_array('inbox-for-woocommerce-agent', $roles) ) {
			$inboxUserId = IBXFWL_Inbox_DatabaseController::getInboxUser($currentUser->user_email, $currentUser->ID);
			$query = $query . ' WHERE assigned_agent_id = ' . ( $inboxUserId );

			if ($section) {
				$query = $query . " AND status = '" . ( $section ) . "'";
			} else {
				$query = $query . " WHERE status != 'archive'";
			}
		} elseif ($section) {
			$query = $query . " WHERE status = '" . ( $section ) . "'";
		} else {
			$query = $query . " WHERE status != 'archive'";
		}
	
		/* -- Ordering parameters -- */
		//Parameters that are going to be used to order the result
		$orderby = !empty($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'ASC';
		$order = !empty($_GET['order']) ? sanitize_text_field($_GET['order']) : '';
		
		if (!empty($orderby) & !empty($order)) { 
			if ( 'wcs_status' == $orderby ) {
				$orderby = 'status';
			} elseif ( 'wcs_type' == $orderby ) {
				$orderby = 'type';
			} elseif ( 'wcs_id' == $orderby ) {
				$orderby = 'id';
			} elseif ( 'wcs_created_at' == $orderby ) {
				$orderby = 'created_at';
			} else {
				$orderby = 'created_at';
			}
			$query.=' ORDER BY ' . $orderby . ' ' . $order; 
		} else {
			$orderby = 'created_at';
			$order = 'DESC';
			$query.=' ORDER BY created_at DESC'; 
		}
	
		/* -- Pagination parameters -- */
		//Number of elements in your table?
		$totalitems = 0; //return the total number of affected rows
		// $totalitems = $wpdb->query($wpdb->prepare($query, $orderby)); //return the total number of affected rows
		if ( !in_array('administrator', $roles) && in_array('inbox-for-woocommerce-agent', $roles) ) {
			if ($section) {
				$totalitems = $wpdb->query($wpdb->prepare('SELECT * FROM %1s WHERE assigned_agent_id = %d AND status = %s ORDER BY %1s %1s', $tableName, $inboxUserId, $section, $orderby, $order)); //return the total number of affected rows
			} else {
				$totalitems = $wpdb->query($wpdb->prepare('SELECT * FROM %1s WHERE assigned_agent_id = %d AND status != %s ORDER BY %1s %1s', $tableName, $inboxUserId, 'archive', $orderby, $order));
			}
		} elseif ($section) {
			$totalitems = $wpdb->query($wpdb->prepare('SELECT * FROM %1s WHERE status = %s ORDER BY %1s %1s', $tableName, $section, $orderby, $order));
		} else {
			$totalitems = $wpdb->query($wpdb->prepare('SELECT * FROM %1s WHERE status != %s ORDER BY %1s %1s', $tableName, 'archive', $orderby, $order));
		}
		
		//How many to display per page?
		$perpage = 20;
		//Which page is this?
		$paged = !empty($_GET['paged']) ? sanitize_text_field($_GET['paged']) : '';

		//Page Number
		if (empty($paged) || !is_numeric($paged) || $paged<=0 ) { 
			$paged=1; 
		} 
		
		//How many pages do we have in total? 
		$totalpages = ceil($totalitems/$perpage); 
		
		//adjust the query to take pagination into account 
		if (!empty($paged) && !empty($perpage)) { 
			$offset=( $paged-1 )*$perpage;
$query.=' LIMIT ' . (int) $offset . ',' . (int) $perpage; 
		} 
		
		/* -- Register the pagination -- */ 
		$this->set_pagination_args( array(
			'total_items' => $totalitems,
			'total_pages' => $totalpages,
			'per_page' => $perpage,
		) );
		//The pagination links are automatically built according to those parameters
	
		/* -- Register the Columns -- */
		$columns = $this->get_columns();
		// $_wp_column_headers[$screen->id]=$columns;
		
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$primary = 'id';
		$this->_column_headers = array( $columns, $hidden, $sortable, $primary );
	
		/* -- Fetch the items -- */
		// $tickets = $wpdb->get_results($wpdb->prepare($query, $orderby), ARRAY_A);
		$tickets = [];
		if ( !in_array('administrator', $roles) && in_array('inbox-for-woocommerce-agent', $roles) ) {
			if ($section) {
				if (!empty($paged) && !empty($perpage)) { 
					$tickets = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE assigned_agent_id = %d AND status = %s ORDER BY %1s %1s LIMIT %d,%d', $tableName, $inboxUserId, $section, $orderby, $order, $offset, $perpage), ARRAY_A);
				} else {
					$tickets = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE assigned_agent_id = %d AND status = %s ORDER BY %1s %1s', $tableName, $inboxUserId, $section, $orderby, $order), ARRAY_A);
				}
			} else {
				if (!empty($paged) && !empty($perpage)) { 
					$tickets = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE assigned_agent_id = %d AND status != %s ORDER BY %1s %1s LIMIT %d,%d', $tableName, $inboxUserId, 'archive', $orderby, $order, $offset, $perpage), ARRAY_A);
				} else {
					$tickets = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE assigned_agent_id = %d AND status != %s ORDER BY %1s %1s', $tableName, $inboxUserId, 'archive', $orderby, $order), ARRAY_A);
				}
			}
		} elseif ($section) {
			if (!empty($paged) && !empty($perpage)) { 
				$tickets = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE status = %s ORDER BY %1s %1s LIMIT %d,%d', $tableName, $section, $orderby, $order, $offset, $perpage), ARRAY_A);
			} else {
				$tickets = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE status = %s ORDER BY %1s %1s', $tableName, $section, $orderby, $order), ARRAY_A);
			}
		} else {
			if (!empty($paged) && !empty($perpage)) { 
				$tickets = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE status != %s ORDER BY %1s %1s LIMIT %d,%d', $tableName, 'archive', $orderby, $order, $offset, $perpage), ARRAY_A);
			} else {
				$tickets = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE status != %s ORDER BY %1s %1s', $tableName, 'archive', $orderby, $order), ARRAY_A);
			}
		}
		
		
		$tableName = $wpdb->prefix . IBXFWL_Inbox_DatabaseController::DB_USERS_TABLE;
		$users = $wpdb->get_results($wpdb->prepare('SELECT * from %1s', $tableName), ARRAY_A);
		$allUsers = [];
		$allUserWPIDs = [];
		foreach ($users as $user) {
			$allUsers[$user['ID']] = $this->get_user_name($wpdb, $user);
			if ($user['wp_user_id']) {
				$allUserWPIDs[$user['ID']] = $user['wp_user_id'];
			}
		}
		
		foreach ($tickets as $indx => $ticket) {
			$tickets[$indx]['sender'] = isset($allUsers[$ticket['user_id']]) ? $allUsers[$ticket['user_id']] : 'Unknown';
			$tickets[$indx]['sender_wp_id'] = isset($allUserWPIDs[$ticket['user_id']]) ? $allUserWPIDs[$ticket['user_id']] : '';
			$tickets[$indx]['agent'] = isset($allUsers[$ticket['assigned_agent_id']]) ? $allUsers[$ticket['assigned_agent_id']] : 'Unknown';
			$tickets[$indx]['agent_wp_id'] = isset($allUserWPIDs[$ticket['assigned_agent_id']]) ? $allUserWPIDs[$ticket['assigned_agent_id']] : '';

			if ( !$tickets[$indx]['agent_wp_id'] || ( $tickets[$indx]['agent_wp_id'] == $currentUser->ID ) ) {
				$tickets[$indx]['is_assigned_agent'] = true;
			} else {
				$tickets[$indx]['is_assigned_agent'] = false;
			}
		}

		$this->items = $tickets;
	}

	/**
	 * Get User Display Name
	 *
	 * @param object $wpdb
	 * @param array $user
	 * @return string
	 */
	private function get_user_name( $wpdb, $user) {
		if ( $user['wp_user_id'] ) {
			$wpUserId = $user['wp_user_id'];
			$tableName = $wpdb->prefix . 'users';
			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM %1s WHERE `ID` = %d', $tableName, $wpUserId));
			if ( count($results) > 0 ) {
				$name = $results[0]->display_name;
				return ucfirst($name);
			}
		} else {
			$name = $user['name'] ? $user['name'] : $user['email'];
			if ( 'Guest User' === $name && $user['email']) {
				$name = $user['email'];
				return $name;
			}
			return ucfirst($name);
		}
	}


	/** Text displayed when no customer data is available */
	public function no_items() {
		echo esc_html__( 'No tickets available.', 'inbox-for-woocommerce' );
	}

	/**
	* Render a column when no column specific method exists.
	*
	* @param array $item
	* @param string $column_name
	*
	* @return mixed
	*/
	public function column_default( $item, $column_name) {
		return $item[$column_name];
	}

	/**
	* Delete a customer record.
	*
	* @param int $id customer ID
	*/
	public static function delete_customer( $id ) {
		global $wpdb;
		
		$wpdb->delete(
		"{$wpdb->prefix}customers",
		[ 'ID' => $id ],
		[ '%d' ]
		);
	}

	/**
	* Render the bulk edit checkbox
	*
	* @param array $item
	*
	* @return string
	*/
	public function column_cb( $item ) {
		return sprintf(
		'<input type="checkbox" name="bulk-select[]" value="%s" />', $item['ID']
		);
	}

	public function column_wcs_id( $item ) {
		return '<a href="' . admin_url('admin.php?page=woocommerce-inbox-sweito-preview&reference=' . $item['reference']) . '">' . $item['ID'] . ( ( !$item['read_at'] && $item['is_assigned_agent'] ) ? ' <span class="wcs-ticket-list-status">new</span>' : '' ) . '</a>';
	}

	public function column_wcs_reference( $item ) {
		return '<a href="' . admin_url('admin.php?page=woocommerce-inbox-sweito-preview&reference=' . $item['reference']) . '">#' . strtoupper($item['reference']) . '</a>';
	}

	public function column_wcs_status( $item ) {
		return '<span class="wcs-table-status-' . $item['status'] . '">' . strtoupper($item['status']) . '</span>';
	}

	public function column_wcs_type( $item ) {
		return strtoupper($item['type']);
	}

	public function column_wcs_subject( $item ) {
		return $item['subject'];
	}

	public function column_wcs_sender( $item ) {
		if ($item['sender_wp_id']) {
			return '<a href="/wp-admin/user-edit.php?user_id=' . $item['sender_wp_id'] . '">' . $item['sender'] . '</a>';
		}
		return $item['sender'];
	}

	public function column_wcs_assigned( $item ) {
		return $item['agent'];
	}

	public function column_wcs_created_at( $item ) {
		return gmdate('jS M, Y h:i A', strtotime($item['created_at']));
	}

	/**
	* Returns an associative array containing the bulk action
	*
	* @return array
	*/
	public function get_bulk_actions() {
		$actions = [
		'bulk-delete' => esc_html__('Delete', 'inbox-for-woocommerce'),
		'move-to-open' => esc_html__('Move to "Open"', 'inbox-for-woocommerce'),
		'move-to-closed' => esc_html__('Move to "Closed"', 'inbox-for-woocommerce'),
		'move-to-archive' => esc_html__('Move to "Archive"', 'inbox-for-woocommerce'),
		];
		
		return $actions;
	}

	public function process_bulk_action() { 
		require_once(IBXFWL_SWEITO_INCLUDES_URL . '/TicketController.php');

		$nonce = isset($_POST['wcs_wpnonce']) ? sanitize_text_field($_POST['wcs_wpnonce']) : '';
		if ( ! wp_verify_nonce( $nonce, 'wcs-ticket-table-nonce' ) ) {
			return;
		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && 'bulk-delete' == $_POST['action'] )
		|| ( isset( $_POST['action2'] ) && 'bulk-delete' == $_POST['action2'] )
		) {
			$deleteIds = isset($_POST['bulk-select']) ? array_map( 'sanitize_text_field', $_POST['bulk-select'] ) : '';
			IBXFWL_Inbox_TicketController::adminDeleteTickets($deleteIds);

			echo '<div class="updated">
                    <p>' . count($deleteIds) . ' ' . esc_html__('Tickets has been deleted successfully!', 'inbox-for-woocommerce') . '</p>
                </div>';
		}

		if ( ( isset( $_POST['action'] ) && 'move-to-open' == $_POST['action'] )
		|| ( isset( $_POST['action2'] ) && 'move-to-open' == $_POST['action2'] )
		) {
			$ticketIds = isset($_POST['bulk-select']) ? array_map( 'sanitize_text_field', $_POST['bulk-select']) : '';
			IBXFWL_Inbox_TicketController::adminMoveTicketToStatus($ticketIds, 'open');

			echo '<div class="updated">
                    <p>' . count($ticketIds) . ' ' . esc_html__('Tickets has been move to OPEN status successfully!', 'inbox-for-woocommerce') . '</p>
                </div>';
		}

		if ( ( isset( $_POST['action'] ) && 'move-to-closed' == $_POST['action'] )
		|| ( isset( $_POST['action2'] ) && 'move-to-closed' == $_POST['action2'] )
		) {
			$ticketIds = isset($_POST['bulk-select']) ? array_map( 'sanitize_text_field', $_POST['bulk-select']) : '';
			IBXFWL_Inbox_TicketController::adminMoveTicketToStatus($ticketIds, 'closed');

			echo '<div class="updated">
                    <p>' . count($ticketIds) . ' ' . esc_html__('Tickets has been moved to CLOSED status successfully!', 'inbox-for-woocommerce') . '</p>
                </div>';
		}

		if ( ( isset( $_POST['action'] ) && 'move-to-archive' == $_POST['action'] )
		|| ( isset( $_POST['action2'] ) && 'move-to-archive' == $_POST['action2'] )
		) {
			$ticketIds = isset($_POST['bulk-select']) ? array_map( 'sanitize_text_field', $_POST['bulk-select']) : '';
			IBXFWL_Inbox_TicketController::adminMoveTicketToStatus($ticketIds, 'archive');

			echo '<div class="updated">
                    <p>' . count($ticketIds) . ' ' . esc_html__('Tickets has been moved to ARCHIVE status successfully!', 'inbox-for-woocommerce') . '</p>
                </div>';
		}
	}

	/**
	* Generates content for a single row of the table.
	*
	* @param object $item The current item.
	*/
	// public function single_row( $item ) {
	//     echo '<tr>';
	//     $this->single_row_columns( $item );
	//     echo '</tr>';
	// }
}
