<?php
/**
 * Plugin Name: WP2Shell Batch Guard (Must-Use)
 * Description: Blocks anonymous REST batch API (wp2shell mitigation).
 * Version: 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

function wp2shell_fixer_is_batch_route($route) {
    $route = strtolower((string) $route);
    $route = function_exists('untrailingslashit') ? untrailingslashit($route) : rtrim($route, '/');
    if ($route === '/batch/v1' || $route === 'batch/v1') {
        return true;
    }
    return (bool) preg_match('#(^|/)batch/v1$#', $route);
}

function wp2shell_fixer_require_auth_for_rest_batch($result, $server, $request) {
    if (!is_object($request) || !method_exists($request, 'get_route')) {
        return $result;
    }
    if (!wp2shell_fixer_is_batch_route($request->get_route())) {
        return $result;
    }
    if (function_exists('is_user_logged_in') && is_user_logged_in()) {
        return $result;
    }
    return new WP_Error(
        'rest_batch_authentication_required',
        'Authentication is required to use the batch API.',
        array('status' => 401)
    );
}

add_filter('rest_pre_dispatch', 'wp2shell_fixer_require_auth_for_rest_batch', -1000, 3);

add_filter('rest_authentication_errors', function ($result) {
    if (true === $result || is_wp_error($result)) {
        return $result;
    }
    $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
    $rest = isset($_REQUEST['rest_route']) ? (string) $_REQUEST['rest_route'] : '';
    $hit = (stripos($uri, 'batch/v1') !== false)
        || (stripos($rest, 'batch/v1') !== false)
        || (stripos($uri, 'rest_route=/batch') !== false);
    if (!$hit) {
        return $result;
    }
    if (function_exists('is_user_logged_in') && is_user_logged_in()) {
        return $result;
    }
    return new WP_Error(
        'rest_batch_authentication_required',
        'Authentication is required to use the batch API.',
        array('status' => 401)
    );
}, 99);
