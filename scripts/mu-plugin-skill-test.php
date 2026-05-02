<?php
/**
 * Plugin Name: ZZZ Skill Setup (temporary)
 * Description: Endpoints for skill testing — DELETE AFTER USE
 */

add_action('rest_api_init', function () {
    // === Quirk #20 — uag_enable_on_page_css_button doit être 'yes' ===
    // Sans ça, le meta _uag_custom_page_level_css est ignoré silencieusement.
    register_rest_route('skill-test/v1', '/enable-on-page-css', [
        'methods' => 'POST',
        'permission_callback' => function () { return current_user_can('manage_options'); },
        'callback' => function () {
            $before = get_option('uag_enable_on_page_css_button', 'yes');
            update_option('uag_enable_on_page_css_button', 'yes');
            // Force regen de tous les page assets pour que le toggle prenne effet
            if (class_exists('UAGB_Helper') && method_exists('UAGB_Helper', 'delete_uag_asset_dir')) {
                UAGB_Helper::delete_uag_asset_dir();
            }
            wp_cache_flush();
            return [
                'ok' => true,
                'before' => $before,
                'after' => get_option('uag_enable_on_page_css_button'),
                'cache_flushed' => true,
            ];
        },
    ]);

    register_rest_route('skill-test/v1', '/setup', [
        'methods' => 'POST',
        'permission_callback' => function () { return current_user_can('manage_options'); },
        'callback' => function ($req) {
            $palette_3 = ['#FD9800', '#E98C00', '#0F172A', '#454F5E', '#FEF9E1', '#FFFFFF', '#F9F0C8', '#141006', '#222222'];
            $astra = get_option('astra-settings', []);
            if (!is_array($astra)) $astra = [];
            $astra['global-color-palette'] = [
                'title' => 'Orange WPF (palette_3)',
                'palette' => $palette_3,
            ];
            update_option('astra-settings', $astra);
            if (class_exists('UAGB_Helper') && method_exists('UAGB_Helper', 'delete_uag_asset_dir')) {
                UAGB_Helper::delete_uag_asset_dir();
            }
            delete_transient('astra_dynamic_css');
            wp_cache_flush();
            return ['ok' => true, 'palette' => $palette_3];
        },
    ]);

    register_rest_route('skill-test/v1', '/upload-image', [
        'methods' => 'POST',
        'permission_callback' => function () { return current_user_can('upload_files'); },
        'callback' => function ($req) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
            $url = $req->get_param('url');
            $custom_name = $req->get_param('name') ?: '';
            $tmp = download_url($url);
            if (is_wp_error($tmp)) return ['ok' => false, 'err' => $tmp->get_error_message()];
            $name = $custom_name ?: (basename(parse_url($url, PHP_URL_PATH)) ?: 'image.jpg');
            if (!preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $name)) {
                $name .= '.jpg';
            }
            $file = ['name' => $name, 'tmp_name' => $tmp];
            $aid = media_handle_sideload($file, 0, null, ['test_form' => false]);
            if (is_wp_error($aid)) {
                @unlink($tmp);
                return ['ok' => false, 'err' => $aid->get_error_message()];
            }
            return ['ok' => true, 'id' => $aid, 'url' => wp_get_attachment_url($aid)];
        },
    ]);

    register_rest_route('skill-test/v1', '/regen-spectra', [
        'methods' => 'POST',
        'permission_callback' => function () { return current_user_can('edit_posts'); },
        'callback' => function ($req) {
            $post_id = (int) $req->get_param('post_id');
            if (!$post_id) return ['ok' => false, 'err' => 'no post_id'];
            if (!class_exists('UAGB_Post_Assets')) return ['ok' => false, 'err' => 'Spectra not active'];
            $a = new UAGB_Post_Assets($post_id);
            $a->generate_assets();
            if (method_exists($a, 'update_page_assets')) $a->update_page_assets();
            $meta = get_post_meta($post_id, '_uag_page_assets', true);
            return [
                'ok' => true,
                'css_len' => strlen($meta['css'] ?? ''),
                'js_len' => strlen($meta['js'] ?? ''),
            ];
        },
    ]);

    register_rest_route('skill-test/v1', '/inspect-faq', [
        'methods' => 'GET',
        'permission_callback' => function () { return current_user_can('edit_posts'); },
        'callback' => function ($req) {
            // List all faq-child block attributes from registered blocks
            $registry = WP_Block_Type_Registry::get_instance();
            $faq = $registry->get_registered('uagb/faq-child');
            return [
                'ok' => true,
                'attributes' => $faq ? array_keys($faq->attributes ?: []) : null,
                'parent' => $faq ? $faq->parent : null,
            ];
        },
    ]);

    register_rest_route('skill-test/v1', '/render-faq-test', [
        'methods' => 'POST',
        'permission_callback' => function () { return current_user_can('edit_posts'); },
        'callback' => function ($req) {
            $variants = $req->get_param('variants') ?: [];
            $results = [];
            foreach ($variants as $i => $variant) {
                $id = wp_insert_post([
                    'post_title' => 'FAQ-test-' . $i,
                    'post_content' => $variant,
                    'post_status' => 'publish',
                    'post_type' => 'page',
                ]);
                if (class_exists('UAGB_Post_Assets')) {
                    $a = new UAGB_Post_Assets($id);
                    $a->generate_assets();
                    if (method_exists($a, 'update_page_assets')) $a->update_page_assets();
                }
                // Render the post content
                $post = get_post($id);
                $rendered = apply_filters('the_content', $post->post_content);
                $results[] = [
                    'id' => $id,
                    'rendered_length' => strlen($rendered),
                    'rendered_excerpt' => mb_substr(wp_strip_all_tags($rendered), 0, 200),
                ];
                wp_delete_post($id, true);
            }
            return ['ok' => true, 'results' => $results];
        },
    ]);

    register_rest_route('skill-test/v1', '/cleanup', [
        'methods' => 'POST',
        'permission_callback' => function () { return current_user_can('manage_options'); },
        'callback' => function ($req) {
            $pages = get_posts([
                'post_type' => 'page',
                'posts_per_page' => -1,
                'meta_key' => '_skill_test_page',
                'meta_value' => '1',
                'post_status' => ['publish', 'draft'],
            ]);
            foreach ($pages as $p) wp_delete_post($p->ID, true);
            return ['ok' => true, 'deleted' => count($pages)];
        },
    ]);
});
