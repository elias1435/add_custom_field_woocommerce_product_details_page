/* add a textarea before 'add to cart' button and it will show when customer fillout in cart, checkout, producessing customer email and admin new order email template 
use the code in wodpress functions.php and make sure you have backup your website. */

// Add custom textarea field on product page
add_action('woocommerce_before_add_to_cart_button', 'custom_gift_note_field', 10);
function custom_gift_note_field() {
    echo '<div class="custom-gift-note">
        <h2 class="wc-pao-addon-heading">Sending as a gift? Add a gift note!</h2>
        <textarea name="gift_note" id="gift_note" rows="3" style="width:100%;"></textarea>
    </div>';
}

// Validate custom field input
add_filter('woocommerce_add_to_cart_validation', 'validate_gift_note_field', 10, 2);
function validate_gift_note_field($passed, $product_id) {
    if (isset($_POST['gift_note']) && empty($_POST['gift_note'])) {
        $passed = false;
        wc_add_notice(__('Please add a Sending as a gift? Add a gift note!: or leave it empty.'), 'error');
    }
    return $passed;
}

// Save custom field data to cart item
add_filter('woocommerce_add_cart_item_data', 'save_gift_note_field', 10, 2);
function save_gift_note_field($cart_item_data, $product_id) {
    if (isset($_POST['gift_note'])) {
        $cart_item_data['gift_note'] = sanitize_textarea_field($_POST['gift_note']);
    }
    return $cart_item_data;
}

// Display custom field value in cart and checkout
add_filter('woocommerce_get_item_data', 'display_gift_note_in_cart', 10, 2);
function display_gift_note_in_cart($item_data, $cart_item) {
    if (isset($cart_item['gift_note'])) {
        $item_data[] = array(
            'name' => __('Sending as a gift? Add a gift note!'),
            'value' => sanitize_textarea_field($cart_item['gift_note']),
        );
    }
    return $item_data;
}

// Save custom field to order meta
add_action('woocommerce_checkout_create_order_line_item', 'save_gift_note_to_order', 10, 4);
function save_gift_note_to_order($item, $cart_item_key, $values, $order) {
    if (isset($values['gift_note'])) {
        $item->add_meta_data(__('Sending as a gift? Add a gift note!:'), $values['gift_note']);
    }
}



// Display custom field in customer and admin emails
add_filter('woocommerce_email_order_meta_fields', 'add_gift_note_to_emails', 10, 3);
function add_gift_note_to_emails($fields, $sent_to_admin, $order) {
    foreach ($order->get_items() as $item_id => $item) {
        $gift_note = $item->get_meta('Sending as a gift? Add a gift note!');
        if ($gift_note) {
            $fields['gift_note'] = array(
                'label' => __('Sending as a gift? Add a gift note!:'),
                'value' => wp_strip_all_tags($gift_note),
            );
        }
    }
    return $fields;
}
