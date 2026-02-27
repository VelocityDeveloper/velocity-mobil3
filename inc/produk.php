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

// Skema metabox sederhana (tanpa plugin)
function velocity_metabox($meta_boxes = array()) {
	$textdomain = 'justg';

	$meta_boxes[] = array(
		'id'           => 'velocity_produk_meta',
		'title'        => __('Detail Produk', $textdomain),
		'post_types'   => array('produk'),
		'context'      => 'normal',
		'priority'     => 'default',
		'nonce_action' => 'velocity_produk_meta_save',
		'nonce_name'   => 'velocity_produk_meta_nonce',
		'fields'       => array(
			array(
				'name' => __('Harga', $textdomain),
				'id'   => 'harga',
				'type' => 'number',
				'min'  => 0,
				'step' => 1,
				'desc' => __('Isi nominalnya saja, contoh: 250000000', $textdomain),
			),
			array(
				'name' => __('Warna', $textdomain),
				'id'   => 'warna',
				'type' => 'text',
				'desc' => __('Pisahkan dengan tanda koma, contoh: Hitam, Putih, Biru', $textdomain),
			),
			array(
				'name' => __('Mesin', $textdomain),
				'id'   => 'mesin',
				'type' => 'text',
			),
			array(
				'name' => __('Transmisi', $textdomain),
				'id'   => 'transmisi',
				'type' => 'text',
			),
		),
	);

	return $meta_boxes;
}

function velocity_get_metabox_schema_map() {
	$schema = velocity_metabox(array());
	$map    = array();
	foreach ($schema as $meta_box) {
		if (!empty($meta_box['id'])) {
			$map[$meta_box['id']] = $meta_box;
		}
	}

	return $map;
}

function velocity_register_meta_boxes() {
	$meta_boxes = velocity_metabox(array());
	foreach ($meta_boxes as $meta_box) {
		$post_types = !empty($meta_box['post_types']) && is_array($meta_box['post_types']) ? $meta_box['post_types'] : array('post');
		foreach ($post_types as $post_type) {
			add_meta_box(
				$meta_box['id'],
				$meta_box['title'],
				'velocity_render_meta_box',
				$post_type,
				isset($meta_box['context']) ? $meta_box['context'] : 'advanced',
				isset($meta_box['priority']) ? $meta_box['priority'] : 'default',
				array('meta_box' => $meta_box)
			);
		}
	}
}
add_action('add_meta_boxes', 'velocity_register_meta_boxes');

function velocity_render_meta_box($post, $callback_args) {
	if (empty($callback_args['args']['meta_box']) || !is_array($callback_args['args']['meta_box'])) {
		return;
	}

	$meta_box     = $callback_args['args']['meta_box'];
	$nonce_name   = !empty($meta_box['nonce_name']) ? $meta_box['nonce_name'] : 'velocity_meta_nonce';
	$nonce_action = !empty($meta_box['nonce_action']) ? $meta_box['nonce_action'] : 'velocity_meta_save';
	$fields       = !empty($meta_box['fields']) && is_array($meta_box['fields']) ? $meta_box['fields'] : array();

	wp_nonce_field($nonce_action, $nonce_name);

	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ($fields as $field) {
		$field_id = isset($field['id']) ? $field['id'] : '';
		if ($field_id === '') {
			continue;
		}
		$value = get_post_meta($post->ID, $field_id, true);
		echo '<tr>';
		echo '<th><label for="' . esc_attr($field_id) . '">' . esc_html(isset($field['name']) ? $field['name'] : $field_id) . '</label></th>';
		echo '<td>';
		velocity_render_meta_field_input($field, $value);
		if (!empty($field['desc'])) {
			echo '<p><small>' . esc_html($field['desc']) . '</small></p>';
		}
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody></table>';
}

function velocity_render_meta_field_input($field, $value) {
	$field_id    = isset($field['id']) ? $field['id'] : '';
	$field_type  = isset($field['type']) ? $field['type'] : 'text';
	$field_class = !empty($field['class']) ? $field['class'] : 'regular-text';
	$options     = isset($field['options']) && is_array($field['options']) ? $field['options'] : array();

	switch ($field_type) {
		case 'textarea':
			echo '<textarea id="' . esc_attr($field_id) . '" name="' . esc_attr($field_id) . '" class="' . esc_attr($field_class) . '" rows="4">' . esc_textarea($value) . '</textarea>';
			break;

		case 'number':
			$min  = isset($field['min']) ? ' min="' . esc_attr($field['min']) . '"' : '';
			$max  = isset($field['max']) ? ' max="' . esc_attr($field['max']) . '"' : '';
			$step = isset($field['step']) ? ' step="' . esc_attr($field['step']) . '"' : '';
			echo '<input type="number" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_id) . '" class="' . esc_attr($field_class) . '" value="' . esc_attr($value) . '"' . $min . $max . $step . ' />';
			break;

		case 'checkbox':
			echo '<label><input type="checkbox" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_id) . '" value="1" ' . checked((string) $value, '1', false) . ' /> ' . esc_html(isset($field['label']) ? $field['label'] : '') . '</label>';
			break;

		case 'radio':
			foreach ($options as $option_value => $option_label) {
				echo '<label style="display:block;margin-bottom:4px;"><input type="radio" name="' . esc_attr($field_id) . '" value="' . esc_attr($option_value) . '" ' . checked((string) $value, (string) $option_value, false) . ' /> ' . esc_html($option_label) . '</label>';
			}
			break;

		case 'select':
			echo '<select id="' . esc_attr($field_id) . '" name="' . esc_attr($field_id) . '" class="' . esc_attr($field_class) . '">';
			foreach ($options as $option_value => $option_label) {
				echo '<option value="' . esc_attr($option_value) . '" ' . selected((string) $value, (string) $option_value, false) . '>' . esc_html($option_label) . '</option>';
			}
			echo '</select>';
			break;

		case 'email':
		case 'url':
		case 'date':
			echo '<input type="' . esc_attr($field_type) . '" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_id) . '" class="' . esc_attr($field_class) . '" value="' . esc_attr($value) . '" />';
			break;

		case 'file':
			echo '<input type="url" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_id) . '" class="' . esc_attr($field_class) . '" value="' . esc_attr($value) . '" placeholder="https://..." />';
			break;

		case 'text':
		default:
			echo '<input type="text" id="' . esc_attr($field_id) . '" name="' . esc_attr($field_id) . '" class="' . esc_attr($field_class) . '" value="' . esc_attr($value) . '" />';
			break;
	}
}

function velocity_sanitize_meta_field_value($raw_value, $field) {
	$field_type = isset($field['type']) ? $field['type'] : 'text';

	switch ($field_type) {
		case 'textarea':
			return sanitize_textarea_field($raw_value);

		case 'number':
			$clean_number = preg_replace('/[^0-9.\-]/', '', (string) $raw_value);
			return $clean_number;

		case 'email':
			return sanitize_email($raw_value);

		case 'url':
		case 'file':
			return esc_url_raw($raw_value);

		case 'checkbox':
			return !empty($raw_value) ? '1' : '0';

		case 'radio':
		case 'select':
			$clean_value = sanitize_text_field($raw_value);
			$options     = isset($field['options']) && is_array($field['options']) ? array_keys($field['options']) : array();
			if (!empty($options) && !in_array($clean_value, $options, true)) {
				return '';
			}
			return $clean_value;

		case 'date':
			return sanitize_text_field($raw_value);

		case 'text':
		default:
			return sanitize_text_field($raw_value);
	}
}

function velocity_save_meta_boxes($post_id) {
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	if (wp_is_post_revision($post_id)) {
		return;
	}

	$post_type = get_post_type($post_id);
	if (!$post_type) {
		return;
	}

	if (!current_user_can('edit_post', $post_id)) {
		return;
	}

	$schema_map = velocity_get_metabox_schema_map();
	foreach ($schema_map as $meta_box) {
		$post_types = !empty($meta_box['post_types']) && is_array($meta_box['post_types']) ? $meta_box['post_types'] : array();
		if (!in_array($post_type, $post_types, true)) {
			continue;
		}

		$nonce_name   = !empty($meta_box['nonce_name']) ? $meta_box['nonce_name'] : 'velocity_meta_nonce';
		$nonce_action = !empty($meta_box['nonce_action']) ? $meta_box['nonce_action'] : 'velocity_meta_save';
		if (empty($_POST[$nonce_name]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[$nonce_name])), $nonce_action)) {
			continue;
		}

		$fields = !empty($meta_box['fields']) && is_array($meta_box['fields']) ? $meta_box['fields'] : array();
		foreach ($fields as $field) {
			$field_id   = isset($field['id']) ? $field['id'] : '';
			$field_type = isset($field['type']) ? $field['type'] : 'text';
			if ($field_id === '') {
				continue;
			}

			$is_checkbox = ($field_type === 'checkbox');
			$posted      = isset($_POST[$field_id]) ? wp_unslash($_POST[$field_id]) : ($is_checkbox ? '0' : null);

			if ($posted === null) {
				continue;
			}

			$clean_value = velocity_sanitize_meta_field_value($posted, $field);
			update_post_meta($post_id, $field_id, $clean_value);
		}
	}
}
add_action('save_post', 'velocity_save_meta_boxes');



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
            <?php echo velocity_mobil3_render_post_thumb($post->ID); ?>
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
