<?php
/**
 * Plugin Name: Enhanced Custom Registration Form
 * Description: A plugin to create a custom registration form with advanced fields.
 * Version: 1.2
 * Author: Chidi Emeribe
 */

// Enqueue scripts and styles
function ecrf_enqueue_scripts_and_styles() {
    wp_enqueue_script('jquery');
    wp_enqueue_style('ecrf-styles', plugins_url('/css/ecrf-styles.css', __FILE__));
    wp_enqueue_script('ecrf-validation', plugins_url('/js/ecrf-validation.js', __FILE__), array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'ecrf_enqueue_scripts_and_styles');

// Shortcode to display the registration form
add_shortcode('enhanced_custom_registration_form', 'ecrf_display_registration_form');

function ecrf_display_registration_form() {
    ob_start();
    if (is_user_logged_in()) {
        echo '<p>You are already registered and logged in.</p>';
    } else {
        ?>
        <form id="enhanced-registration-form" action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="form_identifier" value="enhanced_custom_registration">
            <p>
                <label for="first_name">First Name</label>
                <input type="text" name="first_name" required>
            </p>
            <p>
                <label for="surname">Surname</label>
                <input type="text" name="surname" required>
            </p>
            <p>
                <label for="email">E-mail Address</label>
                <input type="email" name="email" required>
            </p>
            <p>
                <label for="phone">Phone</label>
                <input type="text" name="phone" required>
            </p>
            <p>
                <label for="address">Address</label>
                <textarea name="address" required></textarea>
            </p>
            <p>
                <label for="cv">Upload CV</label>
                <input type="file" name="cv" accept=".doc,.docx,.pdf" required>
            </p>
            <p>
                <label for="certificates">Upload Certificates (Max 3)</label>
                <input type="file" name="certificates[]" accept=".doc,.docx,.pdf" multiple required>
            </p>
            <p>
                <label for="job_categories">Job Categories</label>
                <select name="job_categories" required>
                    <option value="Hospitality">Hospitality</option>
                    <option value="NGOs">NGOs</option>
                    <option value="Healthcare">Healthcare</option>
                    <option value="Tech">Tech</option>
                    <option value="Finance">Finance</option>
                    <option value="Manufacturing">Manufacturing</option>
                    <option value="Retail">Retail</option>
                    <option value="Telcos">Telcos</option>
                    <option value="Emerging Industries">Emerging Industries</option>
                </select>
            </p>
            <p>
                <label for="profession">Profession</label>
                <select name="profession" required>
                    <option value="Fresh Graduate">Fresh Graduate</option>
                    <option value="Driver">Driver</option>
                    <option value="Admin">Admin</option>
                    <option value="Waiter">Waiter</option>
                    <option value="Secretary">Secretary</option>
                    <option value="Receptionist">Receptionist</option>
                    <option value="Customer Relations">Customer Relations</option>
                    <option value="Marketer">Marketer</option>
                    <option value="Sales">Sales</option>
                    <option value="IT">IT</option>
                    <option value="Other">Other</option>
                </select>
            </p>
            <p>
                <label for="id_type">ID Type</label>
                <select name="id_type" required>
                    <option value="License">License</option>
                    <option value="Int Passport">International Passport</option>
                    <option value="NIN">NIN</option>
                    <option value="National ID Card">National ID Card</option>
                </select>
            </p>
            <p>
                <label for="user_pass">Password</label>
                <input type="password" name="user_pass" required>
            </p>
            <p>
                <label for="confirm_pass">Confirm Password</label>
                <input type="password" name="confirm_pass" required>
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
function ecrf_handle_registration() {
    if (isset($_POST['submit_registration']) && $_POST['form_identifier'] === 'enhanced_custom_registration') {
        // Sanitize inputs
        $first_name = sanitize_text_field($_POST['first_name']);
        $surname = sanitize_text_field($_POST['surname']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $address = sanitize_textarea_field($_POST['address']);
        $job_category = sanitize_text_field($_POST['job_categories']);
        $profession = sanitize_text_field($_POST['profession']);
        $id_type = sanitize_text_field($_POST['id_type']);
        $password = $_POST['user_pass'];
        $confirm_password = $_POST['confirm_pass'];

        if ($password !== $confirm_password) {
            echo '<p style="color:red;">Error: Passwords do not match.</p>';
            return;
        }

        // Handle file uploads
require_once(ABSPATH . 'wp-admin/includes/file.php');
$upload_overrides = ['test_form' => false];

// CV upload
$cv_upload = null; // Default to null
if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
    $cv = $_FILES['cv'];
    $cv_upload = wp_handle_upload($cv, $upload_overrides);
    if (isset($cv_upload['error'])) {
        echo '<p style="color:red;">Error uploading CV: ' . esc_html($cv_upload['error']) . '</p>';
        return;
    }
}

// Certificates upload
$certificate_urls = []; // Initialize an empty array
if (isset($_FILES['certificates']) && !empty($_FILES['certificates']['name'][0])) {
    foreach ($_FILES['certificates']['name'] as $key => $certificate_name) {
        if (!empty($_FILES['certificates']['tmp_name'][$key]) && $_FILES['certificates']['error'][$key] === UPLOAD_ERR_OK) {
            $certificate_file = [
                'name' => $_FILES['certificates']['name'][$key],
                'type' => $_FILES['certificates']['type'][$key],
                'tmp_name' => $_FILES['certificates']['tmp_name'][$key],
                'error' => $_FILES['certificates']['error'][$key],
                'size' => $_FILES['certificates']['size'][$key],
            ];
            $certificate_upload = wp_handle_upload($certificate_file, $upload_overrides);
            if (!isset($certificate_upload['error'])) {
                $certificate_urls[] = $certificate_upload['url'];
            }
        }
    }
}
        // Create user
        $userdata = [
            'user_login' => $email,
            'user_email' => $email,
            'first_name' => $first_name,
            'last_name' => $surname,
            'role' => 'subscriber',
            'user_pass' => $password,
        ];
        $user_id = wp_insert_user($userdata);
        if (!is_wp_error($user_id)) {
            // Save additional fields with ecrf_ prefix
            update_user_meta($user_id, 'ecrf_phone', $phone);
            update_user_meta($user_id, 'ecrf_address', $address);
            update_user_meta($user_id, 'ecrf_job_category', $job_category);
            update_user_meta($user_id, 'ecrf_profession', $profession);
            update_user_meta($user_id, 'ecrf_id_type', $id_type);
            update_user_meta($user_id, 'ecrf_cv', $cv_upload['url']);
            update_user_meta($user_id, 'ecrf_certificates', $certificate_urls);

            // Log the user in
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
            echo '<p style="color:green;">Registration complete. You are now logged in.</p>';
        } else {
            echo '<p style="color:red;">Error: ' . $user_id->get_error_message() . '</p>';
        }
        update_user_meta($user_id, 'ecrf_cv', $cv_upload['url']);
        update_user_meta($user_id, 'ecrf_certificates', maybe_serialize($certificate_urls));


    }
}
add_action('init', 'ecrf_handle_registration');
// Save URLs to user_meta


// Add role-specific fields to the profile page
function ecrf_show_role_specific_fields($user) {
    // Get user roles
    $user_roles = get_userdata($user->ID)->roles;

    // Common fields for all users
    ?>
    <!--
    <h3>Common Fields</h3>
    <table class="form-table">
        <tr>
            <th><label for="ecrf_phone">Phone</label></th>
            <td>
                <input type="text" name="ecrf_phone" value="<?php echo esc_attr(get_the_author_meta('ecrf_phone', $user->ID)); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="ecrf_address">Address</label></th>
            <td>
                <textarea name="ecrf_address" class="regular-text"><?php echo esc_textarea(get_the_author_meta('ecrf_address', $user->ID)); ?></textarea>
            </td>
        </tr>
    </table>
    -->
    <?php

    // Fields for "Subscriber" role
    if (in_array('subscriber', $user_roles)) {
        ?>
        <h3>Subscriber-Specific Fields</h3>
        <table class="form-table">
        <tr>
            <th><label for="ecrf_phone">Phone</label></th>
            <td>
                <input type="text" name="ecrf_phone" value="<?php echo esc_attr(get_the_author_meta('ecrf_phone', $user->ID)); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="ecrf_address">Address</label></th>
            <td>
                <textarea name="ecrf_address" class="regular-text"><?php echo esc_textarea(get_the_author_meta('ecrf_address', $user->ID)); ?></textarea>
            </td>
        </tr>
            <tr>
                <th><label for="ecrf_job_category">Job Category</label></th>
                <td>
                    <input type="text" name="ecrf_job_category" value="<?php echo esc_attr(get_the_author_meta('ecrf_job_category', $user->ID)); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="ecrf_profession">Profession</label></th>
                <td>
                    <input type="text" name="ecrf_profession" value="<?php echo esc_attr(get_the_author_meta('ecrf_profession', $user->ID)); ?>" class="regular-text" />
                </td>
            </tr>
        </table>
        <?php
      ?>
      <h3>Uploaded Files</h3>
      <table class="form-table">
          <!-- CV -->
          <tr>
              <th><label for="ecrf_cv">Uploaded CV</label></th>
              <td>
                  <?php
                  $cv = get_user_meta($user->ID, 'ecrf_cv', true);
                  if ($cv) {
                      echo '<a href="' . esc_url($cv) . '" target="_blank">View CV</a>';
                  } else {
                      echo '<p>No CV uploaded.</p>';
                  }
                  ?>
              </td>
          </tr>
          <!-- Certificates -->
          <tr>
              <th><label for="ecrf_certificates">Uploaded Certificates</label></th>
              <td>
                  <?php
                  $certificates = maybe_unserialize(get_user_meta($user->ID, 'ecrf_certificates', true));
                  if (!empty($certificates) && is_array($certificates)) {
                      foreach ($certificates as $certificate) {
                          echo '<p><a href="' . esc_url($certificate) . '" target="_blank">View Certificate</a></p>';
                      }
                  } else {
                      echo '<p>No Certificates uploaded.</p>';
                  }
                  ?>
              </td>
          </tr>
      </table>
      <?php
        
    }
}
add_action('show_user_profile', 'ecrf_show_role_specific_fields');
add_action('edit_user_profile', 'ecrf_show_role_specific_fields');

// Save role-specific fields
function ecrf_save_role_specific_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    require_once(ABSPATH . 'wp-admin/includes/file.php');
$upload_overrides = ['test_form' => false];

// Update CV
if (isset($_FILES['ecrf_cv']) && $_FILES['ecrf_cv']['error'] === UPLOAD_ERR_OK) {
    $cv = $_FILES['ecrf_cv'];
    $cv_upload = wp_handle_upload($cv, $upload_overrides);
    if (!isset($cv_upload['error'])) {
        update_user_meta($user_id, 'ecrf_cv', $cv_upload['url']);
    }
}

// Update Certificates
if (isset($_FILES['ecrf_certificates']) && !empty($_FILES['ecrf_certificates']['name'][0])) {
    $certificate_urls = [];
    foreach ($_FILES['ecrf_certificates']['name'] as $key => $certificate_name) {
        if (!empty($_FILES['ecrf_certificates']['tmp_name'][$key]) && $_FILES['ecrf_certificates']['error'][$key] === UPLOAD_ERR_OK) {
            $certificate_file = [
                'name' => $_FILES['ecrf_certificates']['name'][$key],
                'type' => $_FILES['ecrf_certificates']['type'][$key],
                'tmp_name' => $_FILES['ecrf_certificates']['tmp_name'][$key],
                'error' => $_FILES['ecrf_certificates']['error'][$key],
                'size' => $_FILES['ecrf_certificates']['size'][$key],
            ];
            $certificate_upload = wp_handle_upload($certificate_file, $upload_overrides);
            if (!isset($certificate_upload['error'])) {
                $certificate_urls[] = $certificate_upload['url'];
            }
        }
    }
    update_user_meta($user_id, 'ecrf_certificates', maybe_serialize($certificate_urls));
}
    
}
add_action('personal_options_update', 'ecrf_save_role_specific_fields');
add_action('edit_user_profile_update', 'ecrf_save_role_specific_fields');
