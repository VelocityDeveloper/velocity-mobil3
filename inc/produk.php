<?php

// Register Custom Post Type & Taxonomy
add_action('init', 'velocity_admin_init');
function velocity_admin_init() {
    register_post_type('produk', array(
        'labels' => array(
            'name' => 'Produk',
            'singular_name' => 'produk',
        ),
        'menu_icon' => 'dashicons-car',
        'public' => true,
        'has_archive' => true,
        'taxonomies' => array('kategori-produk'),
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
        ),
    ));
	register_taxonomy(
        'kategori-produk',
        'produk',
        array(
            'label' => __( 'Kategori Produk' ),
            'hierarchical' => true,
            'show_admin_column' => true,
        )
    );
}



// custom produk meta box
function add_custom_meta_box() {
	$screens = array( 'produk' );
	foreach ( $screens as $screen ) {
		add_meta_box(
			'velocity_produk_meta',
			__( 'Detail Produk', 'velprodukdetail' ),
			'vel_meta_box_callback',
			$screen
		);
	}
}
add_action( 'add_meta_boxes', 'add_custom_meta_box' );

function vel_meta_box_callback( $post ) {
	wp_nonce_field( 'vel_metabox', 'myplugin_meta_box_nonce' );
	$harga = get_post_meta( $post->ID, 'harga', true );
	$warna = get_post_meta( $post->ID, 'warna', true );
	$mesin = get_post_meta( $post->ID, 'mesin', true );
	$transmisi = get_post_meta( $post->ID, 'transmisi', true );
	echo '<table class="form-table" role="presentation"><tbody>';
	echo '<tr>';
	echo '<th><label>Harga</label></th>';
	echo '<td><input type="number" name="harga" value="'.esc_attr($harga).'" size="25" />';
	echo '<br/><small>Isi nominalnya saja, contoh: 250000000</small>';
	echo '</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<th><label>Warna</label></th>';
	echo '<td><input type="text" name="warna" value="'.esc_attr($warna).'" size="25" />';
	echo '<br/><small>Pisahkan dengan tanda koma, contoh: Hitam, Putih, Biru</small>';
	echo '</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<th><label>Mesin</label></th>';
	echo '<td><input type="text" name="mesin" value="'.esc_attr($mesin).'" size="25" /></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<th><label>Transmisi</label></th>';
	echo '<td><input type="text" name="transmisi" value="'.esc_attr($transmisi).'" size="25" /></td>';
	echo '</tr>';
	echo '</tbody></table>';
}


function vel_metabox( $post_id ) {
	if ( ! isset( $_POST['myplugin_meta_box_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['myplugin_meta_box_nonce'], 'vel_metabox' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}


	if ( ! isset( $_POST['harga'] ) ) {
		return;
	}
	if ( ! isset( $_POST['warna'] ) ) {
		return;
	}
	if ( ! isset( $_POST['mesin'] ) ) {
		return;
	}
	if ( ! isset( $_POST['transmisi'] ) ) {
		return;
	}
	// Update the meta field in the database.
	update_post_meta( $post_id, 'harga', sanitize_text_field( $_POST['harga'] ) );
	update_post_meta( $post_id, 'warna', sanitize_text_field( $_POST['warna'] ) );
	update_post_meta( $post_id, 'mesin', sanitize_text_field( $_POST['mesin'] ) );
	update_post_meta( $post_id, 'transmisi', sanitize_text_field( $_POST['transmisi'] ) );
}
add_action( 'save_post', 'vel_metabox' );



function velocity_harga($postid = null){
    global $post;
    if(empty($postid)){
        $post_id = $post->ID;
    } else {
        $post_id = $postid;
    }
    $price = get_post_meta($post_id,'harga',true);
    $html = '<span class="text-muted">';
    if($price){
        $harga = preg_replace('/[^0-9]/', '', $price);
        $html .= 'Rp '.number_format( $harga ,0 , ',','.' );
    } else {
        $html .= '(Hubungi Admin)';
    }
    $html .= '</span>';
    return $html;
}



// Update jumlah pengunjung dengan plugin WP-Statistics
function velocity_allpage() {
    global $wpdb,$post;
    $postID = $post->ID;
    $count_key = 'hit';
    if(empty($post))
    return false;
	if( function_exists( 'WP_Statistics' ) ) {
		$table_name = $wpdb->prefix . "statistics_pages";
		$results    = $wpdb->get_results("SELECT sum(count) as result_value FROM $table_name WHERE id = $postID");
		$count = $results?$results[0]->result_value:'0';
		if($count=='') {
			delete_post_meta($postID, $count_key);
			add_post_meta($postID, $count_key, '0');
		} else {
			update_post_meta($postID, $count_key, $count);
		}
	} else {
        $user_ip = $_SERVER['REMOTE_ADDR']; //retrieve the current IP address of the visitor
        $key = $user_ip . 'x' . $postID; //combine post ID & IP to form unique key
        $value = array($user_ip, $postID); // store post ID & IP as separate values (see note)
        $visited = get_transient($key); //get transient and store in variable

        //check to see if the Post ID/IP ($key) address is currently stored as a transient
        if ( false === ( $visited ) ) {

            //store the unique key, Post ID & IP address for 12 hours if it does not exist
           set_transient( $key, $value, 60*60*12 );

            // now run post views function
            $count = get_post_meta($postID, $count_key, true);
            if($count==''){
                $count = 0;
                delete_post_meta($postID, $count_key);
                add_post_meta($postID, $count_key, '0');
            }else{
                $count++;
                update_post_meta($postID, $count_key, $count);
            }
        }		
	}
}
add_action( 'wp', 'velocity_allpage' );


// [velocity-produk]
function velocity_katalog_produk($atts){
    ob_start();
    $atribut = shortcode_atts(array(
        'style' 	=> 'grid',
        'kategori' 	=> '', // pakai slug
        'jumlah' => 6
    ),$atts);
    $args['posts_per_page'] = $atribut['jumlah'];
    $args['post_type'] = 'produk';
    $kategori = $atribut['kategori'];
    $lokasi = $atribut['lokasi'];
    $style = $atribut['style'];
    $taxquery = array();
    if ($kategori) {
        $taxquery[] = array(
            'taxonomy' => 'kategori-produk',
            'field'    => 'slug',
            'terms'    => $kategori,
        );
        $args['tax_query'] = $taxquery;
    }
    $wpex_query = new wp_query( $args );
    echo '<div class="velocity-produk row m-0">';
    foreach( $wpex_query->posts as $post ) { setup_postdata( $post ); ?>
    <?php if ($style == 'list') { ?>
    <div class="col-12">
    <div class="bg-white row border-bottom pb-2">
        <div class="col-4 pe-0 pt-2">
    <?php } else { ?>
    <div class="col-sm-4 col-6 p-2 text-center">
    <div class="bg-white h-100 border">
        <div class="p-2">
    <?php } ?>
            <?php echo do_shortcode("[resize-thumbnail width='280' height='200' crop='false' upscale='true' post_id='".$post->ID."']"); ?>
        </div>
        <div class="p-2 col">
            <h4 class="mb-1 fs-6"><a class="fw-bold text-dark" href="<?php echo get_the_permalink($post->ID); ?>"><?php echo get_the_title($post->ID); ?></a></h4>
            <div class="text-dark"><?php echo velocity_harga($post->ID); ?></div>
            <div class="mt-2">
                <a class="btn btn-sm btn-dark rounded-0 lh-1" href="<?php echo get_the_permalink($post->ID); ?>"><small>Detail</small></a>
            </div>
        </div>
    </div>
    </div>
    <?php }
    echo '</div>';
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('velocity-produk', 'velocity_katalog_produk');
