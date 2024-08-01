<?php
/*
Plugin Name: User Info Columns
Description: Adds custom columns for registration date and last login date to the user list table.
Version: 1.0
Author: Virtual Market Advantage
*/

// Add the custom columns to the user list table
add_filter('manage_users_columns', 'my_add_user_columns');
function my_add_user_columns($columns) {
    $columns['user_registered'] = 'Registration Date';
    $columns['last_login'] = 'Last Login';
    return $columns;
}

// Populate the custom columns with the user's registration date and last login date
add_filter('manage_users_custom_column', 'my_populate_user_columns', 10, 3);
function my_populate_user_columns($value, $column_name, $user_id) {
    if ('user_registered' == $column_name) {
        $user_data = get_userdata($user_id);
        $registration_date = $user_data->user_registered;
        $formatted_date = date('Y-m-d', strtotime($registration_date));
        return $formatted_date;
    } elseif ('last_login' == $column_name) {
        $last_login = get_user_meta($user_id, 'last_login', true);
        if ($last_login) {
            $formatted_date = date('Y-m-d H:i:s', strtotime($last_login));
            return $formatted_date;
        } else {
            return 'Never';
        }
    }
    return $value;
}

// Make the custom columns sortable
add_filter('manage_edit-users_sortable_columns', 'my_make_user_columns_sortable');
function my_make_user_columns_sortable($columns) {
    $columns['user_registered'] = 'user_registered';
    $columns['last_login'] = 'last_login';
    return $columns;
}

// Sort users by the custom columns
add_action('pre_get_users', 'my_sort_users_by_custom_columns');
function my_sort_users_by_custom_columns($query) {
    if (isset($query->query_vars['orderby'])) {
        if ('user_registered' == $query->query_vars['orderby']) {
            $query->query_vars['orderby'] = 'user_registered';
        } elseif ('last_login' == $query->query_vars['orderby']) {
            $query->query_vars['meta_key'] = 'last_login';
            $query->query_vars['orderby'] = 'meta_value';
        }
    }
}

// Track the last login date
add_action('wp_login', 'my_track_last_login', 10, 2);
function my_track_last_login($user_login, $user) {
    update_user_meta($user->ID, 'last_login', current_time('mysql'));
}

?>
