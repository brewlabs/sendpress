<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
    header('HTTP/1.0 403 Forbidden');
    die;
}
/************************** CREATE A PACKAGE CLASS *****************************
 *******************************************************************************
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 * 
 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 * 
 * Our theme for this list table is going to be movies.
 */
class SendPress_Queue_Errors_Table extends WP_List_Table {
    
    /** ************************************************************************
     * Normally we would be querying data from a database and manipulating that
     * for use in your list table. For this example, we're going to simplify it
     * slightly and create a pre-built array. Think of this as the data that might
     * be returned by $wpdb->query().
     * 
     * @var array 
     **************************************************************************/
    private $_list_title = array();
    private $_sendpress = '';
   
    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct(){
        global $status, $page;
                
        $this->_sendpress = new SendPress();
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'qemail',     //singular name of the listed records
            'plural'    => 'qemails',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }
    
    
    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title() 
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as 
     * possible. 
     * 
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     * 
     * For more detailed insight into how columns are handled, take a look at 
     * WP_List_Table::single_row_columns()
     * 
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    function column_default($item, $column_name){
        switch($column_name){
            /*
            case 'firstname':
            case 'lastname':
                return $item->$column_name;
            case 'status':
               return $item->$column_name;
           */
               case 'details':
                return $item->post_content;
               break;

            case 'max_attempts':


                return "<span class='badge '>$item->max_attempts</span>";
            case 'attempts':
                $cls = "badge-success";

                if($item->attempts == 3 ){
                    $cls = "badge-important";
                }
                if($item->attempts == 2 ){
                    $cls = "badge-warning";
                }
                if($item->attempts == 1 ){
                    $cls = "badge-success";
                }
                if($item->inprocess == 1){
                    return 'in process';
                }

                 return "<span class='badge $cls'>$item->attempts</span>";

            case 'listid':
                if(isset($this->_list_title[$item->listID] )){
                    return $this->_list_title[$item->listID];

                } else {
                    $title = get_the_title($item->listID);
                    $this->_list_title[$item->listID] = $title;
                    return $title;
                }


                //return ;
            case 'gravatar':
                return get_avatar($item->to_email, 30);
            case 'last_attemp':

                if($item->last_attempt !== '0000-00-00 00:00:00'){
                   return date_i18n("Y-m-d H:i:s" , strtotime( $item->last_attempt ) );
                } else {
                    return 'never';
                }

            case 'actions':
                $buttons ='';
                if($item->attempts >= $item->max_attempts){
                    $buttons .='<a class="btn resend-btn btn-success" href="?page='.SPNL()->validate->page().'&action=requeue&emailID='. $item->id .'"><i class="icon-repeat icon-white"></i> Requeue</a> ';
                }
                $buttons .='<a class="btn resend-btn btn-primary" href="?page='.SPNL()->validate->page().'&action=queue-delete&emailID='. $item->id .'"><i class="icon-trash "></i> Delete</a> ';
             return '<div class="inline-buttons">'.$buttons.'</div>';
           
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    
        
    /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named 
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     * 
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     * 
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_title($item){
        
        //Build row actions
        $actions = array( );
        
        //Return the title contents
        return sprintf('%1$s',
            /*$1%s*/ $item->post_title,
            /*$2%s*/ $item->subscriberID,
            /*$3%s*/ $this->row_actions($actions)
        );
    }
    
    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item->id                //The value of the checkbox should be the record's id
        );
    }
    
    
    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value 
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     * 
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns(){
       
        $columns = array(
            //'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
          //  'gravatar' => ' ',
            'title' => 'Error',
            'details' => 'Details',
            
            /*
            'max_attempts' => 'Max&nbsp;Attempts',
            'attempts' => 'Attempted',
            'last_attemp' => 'Last&nbsp;Attempt',
            'actions' => 'Actions'
        */
            
        );
        return $columns;
    }
    
    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
     * you will need to register it here. This should return an array where the 
     * key is the column that needs to be sortable, and the value is db column to 
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     * 
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     * 
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
            'title' =>array('to_email',true), 
            'attempts' =>array('attempts',true), 
            'last_attemp' =>array('last_attempt',true), 
           // 'listid' =>array('listID',true), 
            /*
            'rating'    => array('rating',false),
            'director'  => array('director',false)
            */
        );
        return $sortable_columns;
    }
    
    
    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     * 
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     * 
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     * 
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $actions = array(
         //   'delete-email-queue' => 'Delete'
        );

        

        return $actions;
    }
    
   function list_select(){
        $info = SendPress_Data::get_lists_in_queue();
        echo '<select name="listid">';
        $list_id = SPNL()->validate->_int('listid');
        echo "<option cls value='-1' >".__('All Lists','sendpress')."</option>";
        foreach ($info as $list) {
            $cls = '';
            if($list_id == $list['id']){
                $cls = " selected='selected' ";
            }

           echo "<option $cls value='".$list['id']."'>".$list['title']."</option>";
        }

        
        echo '</select> ';
    }


    function email_finder(){
        echo "<input type='text' value='' name='qs' />";
    }

    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     * 
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {
        
        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
        
    }
    

    function extra_tablenav( $which ) {
        global $cat;
?>
        <div class="alignleft actions">
<?php
        if ( 'top' == $which && !is_singular() ) {

           //$this->list_select();
           //$this->email_finder();
           //submit_button( __( 'Filter' ), 'button', false, false, array( 'id' => 'post-query-submit' ) );
        }

        
?>
        </div>
<?php
    }
    
    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     * 
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items() {
        global $wpdb, $_wp_column_headers;
        $screen = get_current_screen();
        $sp_validate = SPNL()->validate;
          /*      
        select t1.* from `sp_sendpress_list_subscribers` as t1 , `sp_sendpress_subscribers` as t2
        where t1.subscriberID = t2.subscriberID and t1.listID = 2*/


 
       
        //$query = "SELECT * FROM " .  SendPress_Data::queue_table();
       
       
        /* -- Pagination parameters -- */
        //Number of elements in your table?
        $totalitems = SPNL()->log->get_log_count(0,'sending');
        //How many to display per page?
        // get the current user ID
            $user = get_current_user_id();
            // get the current admin screen
            $screen = get_current_screen();
            // retrieve the "per_page" option
           $per_page = 10;
            $screen_option = $screen->get_option('per_page', 'option');
           
            if(!empty( $screen_option)) {
                // retrieve the value of the option stored for the current user
                $per_page = get_user_meta($user, $screen_option, true);
                
                if ( empty ( $per_page) || $per_page < 1 ) {
                    // get the default value if none is set
                    $per_page = $screen->get_option( 'per_page', 'default' );
                }
            }
            
        //Which page is this?
        $paged = $sp_validate->_int("paged");
        //Page Number
        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
        //How many pages do we have in total?
        $totalpages = ceil($totalitems/$per_page);
       
      
    /* -- Register the pagination -- */
        $this->set_pagination_args( array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $per_page,
        ) );
        //The pagination links are automatically built according to those parameters

    /* -- Register the Columns -- */
       $columns = $this->get_columns();
         $hidden = array();
         $sortable = $this->get_sortable_columns();
         $this->_column_headers = array($columns, $hidden, $sortable);
    /* -- Fetch the items -- */
        $this->items = SPNL()->log->get_logs(0,'sending', $paged);
    }
    
   

}
