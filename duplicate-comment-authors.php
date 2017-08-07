<?php
/*
Plugin Name: Duplicate Comment Authors
Plugin URI: https://github.com/benmay/Duplicate-Comment-Authors
Description: Prevents guests leaving comments as same name or email address as users.
Author: Ben May
Version: 0.3
Author URI: http://benmay.org
*/


if( ! class_exists( 'DuplicateCommentAuthorFilter' ) ) {
   
    Class DuplicateCommentAuthorFilter{

        public function __construct() {
            add_filter( 'pre_comment_approved' , array( $this, 'filter' ), 99, 2 );
        }
        
        public function filter( $status=0, $data=array() ) {

            global $wpdb;
            
            // If the user is logged in, then return.
            if( ! $data['user_ID'] == 0 )
                return $status;
             
            // Run the query to check for email or name
            $user_exists = $wpdb->get_var( $wpdb->prepare( 
                                            "
                                                SELECT ID FROM $wpdb->users 
                                                WHERE display_name = '%s'
                                                OR user_email =  '%s'
                                            ", 
                                            $data[ 'comment_author' ],
                                            $data[ 'comment_author_email' ]
                                          ) );

            // If the user doesn't exist - then we're good to go!
            if( ! $user_exists )
                return $status;
            
            // If you have got this far then it means the user does exist. 
            wp_die (
                        'Sorry - you cannot post on this blog using that nickname 
                         or email address as it belongs to one of our registered users. <br /><br />
                         Please go back and try again to post your comment. '
                    );
        }
    }
    New DuplicateCommentAuthorFilter();
}