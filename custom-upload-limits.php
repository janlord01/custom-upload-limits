<?php
/*
Plugin Name: Custom Upload Limits
Description: A plugin to set the maximum upload file size with an admin setting.
Version: 1.0
Author: Janlord Luga
Author URI: https://janlordluga.com/
*/

// Add the menu item in the admin dashboard
function custom_upload_limits_menu() {
    add_options_page(
        'Upload Limit Settings',       // Page title
        'Upload Limit',                // Menu title
        'manage_options',              // Capability
        'custom-upload-limits',        // Menu slug
        'custom_upload_limits_settings_page' // Function to display the settings page
    );
}
add_action('admin_menu', 'custom_upload_limits_menu');

// Display the settings page
function custom_upload_limits_settings_page() {
    ?>
    <div class="wrap">
        <h1>Upload Limit Settings</h1>
        <form method="post" action="options.php">
            <?php
            // Output security fields for the registered setting
            settings_fields('custom_upload_limits_settings');
            
            // Output setting sections and their fields
            do_settings_sections('custom-upload-limits');
            
            // Submit button
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register and define settings
function custom_upload_limits_settings_init() {
    // Register the setting
    register_setting('custom_upload_limits_settings', 'custom_upload_limit');
    
    // Add a new section to the settings page
    add_settings_section(
        'custom_upload_limits_section', // Section ID
        'Upload Limit Settings',        // Section title
        null,                           // Callback for section description
        'custom-upload-limits'          // Page slug
    );
    
    // Add a field for the custom upload limit
    add_settings_field(
        'custom_upload_limit',          // Field ID
        'Maximum Upload File Size (in MB)', // Field title
        'custom_upload_limit_field_callback', // Callback to display the field
        'custom-upload-limits',         // Page slug
        'custom_upload_limits_section'  // Section ID
    );
}
add_action('admin_init', 'custom_upload_limits_settings_init');

// Display the upload limit input field
function custom_upload_limit_field_callback() {
    // Get the current value from the database
    $upload_limit = get_option('custom_upload_limit', 10); // Default to 10 MB
    ?>
    <input type="number" name="custom_upload_limit" value="<?php echo esc_attr($upload_limit); ?>" min="1" max="10000000" />
    <p class="description">Set the maximum upload file size in megabytes (MB).</p>
    <?php
}

// Apply the custom upload limit
function set_custom_upload_size_limit( $size ) {
    // Get the value set in the admin page
    $upload_limit = get_option('custom_upload_limit', 10); // Default to 10 MB
    
    // Convert to bytes
    return $upload_limit * 1024 * 1024;
}
add_filter( 'upload_size_limit', 'set_custom_upload_size_limit' );

// Optionally, adjust PHP's max file size settings
function custom_upload_limits_ini_settings() {
    $upload_limit = get_option('custom_upload_limit', 10); // Default to 10 MB
    @ini_set( 'upload_max_filesize' , $upload_limit . 'M' );
    @ini_set( 'post_max_size', $upload_limit . 'M' );
}
add_action('init', 'custom_upload_limits_ini_settings');
