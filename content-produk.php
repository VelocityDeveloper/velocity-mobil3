<?php

/**
 * Post rendering content according to caller of get_template_part
 *
 * @package justg
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

$warna = get_post_meta( $post->ID, 'warna', true );
$mesin = get_post_meta( $post->ID, 'mesin', true );
$transmisi = get_post_meta( $post->ID, 'transmisi', true );
?>

<article <?php post_class('block-primary mb-4'); ?> id="post-<?php the_ID(); ?>">

    <header class="entry-header">
        <?php do_action('justg_before_title');
        the_title('<h1 class="entry-title">', '</h1>'); ?>
        <div class="entry-meta mb-2">
            <?php justg_posted_on(); ?>
        </div><!-- .entry-meta -->
    </header><!-- .entry-header -->

    <div class="entry-content">

        <div class="border bg-white mb-3">
                <div class="row bg-light m-0 py-2">
                    <div class="col-md-8 h6 mb-2 mb-md-0">
                        <?php $args = array(
                            'orderby' => 'term_order',                          
                        );
                            $kategori_produk = wp_get_object_terms( $post->ID,  'kategori-produk', $args );
                            //echo '<pre>'.print_r($kategori_produk,1).'</pre>'; 
                            if ( ! empty( $kategori_produk ) ) {
                                if ( ! is_wp_error( $kategori_produk ) ) {
                                    echo '<div class="kategori-produk-frame">';
                                        echo '<small class="d-inline-block me-1">Kategori:</small>';
                                        echo '<div class="kategori-produk d-inline-block">';
                                            foreach( array_reverse($kategori_produk) as $term ) {
                                                echo '<span><a  class="text-dark" href="' . esc_url( get_term_link( $term->slug, 'kategori-produk' ) ) . '"><small>' . esc_html( $term->name ) . '</small></a></span>'; 
                                            }
                                        echo '</div>';
                                    echo '</div>';
                                }
                            }
                        ?>
                    </div>
                    <div class="col-md-4 h6 mb-0 text-md-end fw-bold">
                        <?php echo velocity_harga(); ?>
                    </div>
                </div>
                <div class="row m-0 py-3">
                    <div class="col-12 mb-2 text-center">
                        <?php if(has_post_thumbnail()) { ?>
                            <img src="<?php the_post_thumbnail_url( 'full' ); ?> " />
                        <?php } ?>
                    </div>
                    <div class="col-12">
                        <table class="table">
                            <tbody>
                                <?php if(!empty($warna)){ ?>
                                    <tr>
                                        <th>Warna Tersedia</th>
                                        <td><?php echo $warna; ?></td>
                                    </tr>
                                <?php } ?>
                                <?php if(!empty($transmisi)){ ?>
                                    <tr>
                                        <th>Transmisi</th>
                                        <td><?php echo $transmisi; ?></td>
                                    </tr>
                                <?php } ?>
                                <?php if(!empty($mesin)){ ?>
                                    <tr>
                                        <th>Mesin</th>
                                        <td><?php echo $mesin; ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <th>Dilihat</th>
                                    <td><?php echo get_post_meta(get_the_ID(),'hit',true);?> kali</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-12">
                        <?php echo get_the_content();?>
                    </div>
                </div>
            </div>

</div>

</article><!-- #post-## -->