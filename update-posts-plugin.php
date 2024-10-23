<?php
/*
Plugin Name: Update All Posts
Description: Plugin para actualizar todos los posts, páginas y custom post types al hacer clic en un botón.
Version: 1.0
Author: 704k0
Author URI: https://704k0.com
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Agregar el menú en el dashboard de WordPress
function update_posts_plugin_menu() {
    add_menu_page(
        'Actualizar todos los posts', 
        'Actualizar Posts', 
        'manage_options', 
        'update-posts-plugin', 
        'update_all_posts_page'
    );
}
add_action('admin_menu', 'update_posts_plugin_menu');

// Mostrar el botón en la página del plugin
function update_all_posts_page() {
    ?>
    <div class="wrap">
        <h1>Actualizar Todos los Posts</h1>
        <form method="post" action="">
            <?php
            // Agregar nonce de seguridad
            wp_nonce_field('update_all_posts_action', 'update_all_posts_nonce');
            submit_button('Actualizar todos los posts');
            ?>
        </form>
    </div>
    <?php

    // Verificar si se ha enviado el formulario
    if (isset($_POST['submit'])) {
        // Verificar y sanitizar el nonce
        if (isset($_POST['update_all_posts_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['update_all_posts_nonce'])), 'update_all_posts_action')) {
            update_all_posts();
        } else {
            echo '<div class="error notice is-dismissible"><p>Error de seguridad: nonce no válido.</p></div>';
        }
    }
}

// Función para actualizar todos los posts
function update_all_posts() {
    $post_types = get_post_types(['public' => true], 'names');
    
    foreach ($post_types as $post_type) {
        $posts = get_posts([
            'post_type' => $post_type,
            'post_status' => 'publish',
            'numberposts' => -1
        ]);

        foreach ($posts as $post) {
            $post->post_modified = current_time('mysql');
            $post->post_modified_gmt = current_time('mysql', 1);

            // Actualiza el post
            wp_update_post($post);
        }
    }

    echo '<div class="updated notice is-dismissible"><p>Todos los posts han sido actualizados.</p></div>';
}
