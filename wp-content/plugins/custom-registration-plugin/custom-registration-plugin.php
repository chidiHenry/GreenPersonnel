<?php
/**
 * Plugin Name: Custom Registration Plugin
 * Description: A custom registration plugin with fields: Contact Person, Phone, E-mail, Address, Business Name, Password, Confirm Password.
 * Version: 1.2
 * Author: Chidi Emeribe
 */

// Enqueue scripts and styles
function crp_enqueue_scripts_and_styles() {
    wp_enqueue_script('jquery');
    wp_enqueue_style('crp-styles', plugins_url('/css/crp-styles.css', __FILE__));
    wp_enqueue_script('crp-validation', plugins_url('/js/crp-validation.js', __FILE__), array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'crp_enqueue_scripts_and_styles');

// Shortcode to display the registration form
add_shortcode('custom_registration_form', 'crp_display_registration_form');

function crp_display_registration_form() {
    ob_start();
    if (is_user_logged_in()) {
        echo '<p>You are already registered and logged in.</p>';
    } else {
        ?>
        <form id="custom-registration-form" action="" method="post">
            <input type="hidden" name="form_identifier" value="custom_registration_form">
            <p>
                <label for="first_name">First Name</label>
                <input type="text" name="first_name" required>
            </p>
            <p>
                <label for="last_name">Last Name</label>
                <input type="text" name="last_name" required>
            </p>
            <p>
                <label for="phone">Phone</label>
                <input type="text" name="phone" required>
            </p>
            <p>
                <label for="email">E-mail</label>
                <input type="email" name="email" required>
            </p>
            <p>
                <label for="address">Address</label>
                <textarea name="address" required></textarea>
            </p>
            <p>
                <label for="business_name">Business Name (If Registered)</label>
                <input type="text" name="business_name">
            </p>
            <p>
                <label for="password">Password</label>
                <input type="password" name="password" required>
            </p>
            <p>
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </p>
            <p>
                <input type="submit" name="submit_registration" value="Register">
            </p>
        </form>
        <?php
    }
    return ob_get_clean();
}

// Handle form submission
function crp_handle_registration() {
    if (isset($_POST['submit_registration']) && $_POST['form_identifier'] === 'custom_registration_form') {
        // Sanitize inputs
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $phone = sanitize_text_field($_POST['phone']);
        $email = sanitize_email($_POST['email']);
        $address = sanitize_textarea_field($_POST['address']);
        $business_name = sanitize_text_field($_POST['business_name']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            echo '<p style="color:red;">Error: Passwords do not match.</p>';
            return;
        }

        // Create user
        $userdata = [
            'user_login' => $email,
            'user_email' => $email,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'role' => 'contributor', // Assign the 'Contributor' role
            'user_pass' => $password,
        ];
        $user_id = wp_insert_user($userdata);

        if (!is_wp_error($user_id)) {
            // Save additional fields with unique meta keys
            update_user_meta($user_id, 'crp_phone', $phone);
            update_user_meta($user_id, 'crp_address', $address);
            update_user_meta($user_id, 'crp_business_name', $business_name);

            // Log the user in
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
            echo '<p style="color:green;">Registration complete. You are now logged in.</p>';
        } else {
            echo '<p style="color:red;">Error: ' . $user_id->get_error_message() . '</p>';
        }
    }
}
add_action('init', 'crp_handle_registration');

// Display Role-Specific Fields in Profile
function crp_show_role_specific_fields($user) {
    // Get user roles
    $user_roles = get_userdata($user->ID)->roles;

    // Common fields for all users
    ?>
    <!--
    <h3>Common Fields</h3>
    <table class="form-table">
        <tr>
            <th><label for="crp_phone">Phone</label></th>
            <td>
                <input type="text" name="crp_phone" value="<?php echo esc_attr(get_the_author_meta('crp_phone', $user->ID)); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="crp_address">Address</label></th>
            <td>
                <textarea name="crp_address" class="regular-text"><?php echo esc_textarea(get_the_author_meta('crp_address', $user->ID)); ?></textarea>
            </td>
        </tr>
    </table>
    -->
    <?php

    // Contributor-specific fields
    if (in_array('contributor', $user_roles)) {
        ?>
        <h3>Contributor Fields</h3>
        <table class="form-table">
        <tr>
            <th><label for="crp_phone">Phone</label></th>
            <td>
                <input type="text" name="crp_phone" value="<?php echo esc_attr(get_the_author_meta('crp_phone', $user->ID)); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="crp_address">Address</label></th>
            <td>
                <textarea name="crp_address" class="regular-text"><?php echo esc_textarea(get_the_author_meta('crp_address', $user->ID)); ?></textarea>
            </td>
        </tr>
            <tr>
                <th><label for="crp_business_name">Business Name</label></th>
                <td>
                    <input type="text" name="crp_business_name" value="<?php echo esc_attr(get_the_author_meta('crp_business_name', $user->ID)); ?>" class="regular-text">
                </td>
            </tr>
        </table>
        <?php
    }
}
add_action('show_user_profile', 'crp_show_role_specific_fields');
add_action('edit_user_profile', 'crp_show_role_specific_fields');

// Save Role-Specific Fields
function crp_save_role_specific_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    // Save common fields
    if (isset($_POST['crp_phone'])) {
        update_user_meta($user_id, 'crp_phone', sanitize_text_field($_POST['crp_phone']));
    }
    if (isset($_POST['crp_address'])) {
        update_user_meta($user_id, 'crp_address', sanitize_textarea_field($_POST['crp_address']));
    }

    // Save contributor-specific fields
    $user_roles = get_userdata($user_id)->roles;
    if (in_array('contributor', $user_roles) && isset($_POST['crp_business_name'])) {
        update_user_meta($user_id, 'crp_business_name', sanitize_text_field($_POST['crp_business_name']));
    }
}
add_action('personal_options_update', 'crp_save_role_specific_fields');
add_action('edit_user_profile_update', 'crp_save_role_specific_fields');
