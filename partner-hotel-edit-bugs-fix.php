<?php

/**
 * Testing
 */
/*
add_action('wp_footer', function () {

    echo 'Hotel Meta: ';
    print_r(get_post_meta(10206, 'multi_location')[0]);
    echo  '<br><br>';

    echo 'Room Meta: ';
    print_r(get_post_meta(10212, 'multi_location')[0]);
    echo  '<br><br>';

    global $wpdb;

    $table1 = $wpdb->prefix . 'st_hotel';

    $result1 = $wpdb->get_results("SELECT multi_location FROM {$table1} WHERE post_id=10206", ARRAY_A);

    echo 'st_hotel: ';

    print_r($result1[0]['multi_location']);

    echo  '<br><br>';
});
*/
/**
 * End Testing
 */

// Scripts

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('bkr_partner_custom_actions', plugin_dir_url(__FILE__) . 'assets/partner-hotel-edit-bugs-fix.js', [], '1.1', true);
    wp_add_inline_script('bkr_partner_custom_actions', 'const bkr_scripts = ' . json_encode([
        'nonce' => wp_create_nonce('bkr_partner_custom_actions_nonce'),
        'ajax_url' => admin_url('admin-ajax.php'),
    ]), 'before');
});



// End Scripts

add_action('wp_ajax_bkr_partner_custom_action', 'bkr_partner_hotel_edit_data');
add_action('wp_ajax_nopriv_bkr_partner_custom_action', 'bkr_partner_hotel_edit_data');

function bkr_partner_hotel_edit_data()
{
    if (!wp_verify_nonce($_REQUEST['nonce'], 'bkr_partner_custom_actions_nonce')) {
        wp_send_json_error('Dont\t cheat us!');
    }


    /**
     * Wait for one second and then run the process
     */
    sleep(1);


    /**
     * Variables
     */
    $post_id = $_REQUEST['post_id'];

    $edit_screen = $_REQUEST['edit_screen'];

    $parent_hotel_id = $_REQUEST['parent_hotel_id'];

    $hotel_id = ($edit_screen == 'hotel') ? $post_id : $parent_hotel_id;


    /**
     * Get min price from hotel inventory
     */
    global $wpdb;

    $table = $wpdb->prefix . 'st_room_availability';
    $sql = "SELECT price FROM {$table} WHERE parent_id = {$hotel_id} AND status = 'available'";

    $result = $wpdb->get_results($sql, ARRAY_A);

    $prices = [];

    foreach ($result as $price) {
        foreach ($price as $inner_price) {
            $prices[] = $inner_price;
        }
    }

    $min = min($prices);

    /**
     * Update min_price column in st_hotel table
     */

    $wpdb->update(
        $wpdb->prefix . 'st_hotel',
        array(
            'min_price' => $min,
        ),
        array('post_id' => $post_id)
    );

    /**
     * Update multi_location column in st_hotel table with the value of single current hotel or room meta (multi_location)
     */

    $bkr_new_multi_location = get_post_meta($post_id, 'multi_location')[0];

    $wpdb->update(
        $wpdb->prefix . 'st_hotel',
        array(
            'multi_location' => $bkr_new_multi_location,
        ),
        array('post_id' => $post_id)
    );


    /**
     * If edit screen is room then update the sinlge hotel meta with the multi_location value of room
     */

    if ($edit_screen == 'room') {
        update_post_meta($parent_hotel_id, 'multi_location', $bkr_new_multi_location);

        // Update parent hotel
        wp_update_post(['ID' => $parent_hotel_id]);
    }


    /**
     * if edit screen is hotel then cpdate current hotel signle post
     */
    if ($edit_screen == 'hotel') {
        wp_update_post(['ID' => $post_id]);
    }

    // wp_send_json_success([
    //     'msg' => $min,
    //     'post-id' => $post_id,
    // ]);
}
