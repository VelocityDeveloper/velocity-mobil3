<div class="bg-gradient mb-3">
    <div class="container py-2 text-white bg-dark">
        <div class="row align-items-center">
            <div class="col-md-6 fw-bold fs-6 fst-italic text-center text-md-start mb-2 mb-md-0">
                <?php echo velocitytheme_option('welcome_text', ''); ?>                
            </div>
            <div class="col-md-6">
                <form class="border border-primary bg-white rounded" method="get" name="searchform" action="<?php echo get_home_url(); ?>">
                    <div class="row">
                        <div class="col-9 col-md-10 pe-0">
                            <input type="text" name="s" class="form-control form-control-sm border-0" value="<?php echo $s;?>" required="">
                        </div>
                        <div class="col-3 col-md-2 ps-1">
                            <button type="submit" class="h-100 w-100 btn text-white btn-sm bg-theme border-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </form> 
            </div>
        </div>
    </div>
</div>

<div class="container px-0">
<nav id="main-navi" class="navbar navbar-expand-md d-block navbar-light pb-0 px-0 border border-light border-bottom-0 pt-0" aria-labelledby="main-nav-label">

    <h2 id="main-nav-label" class="screen-reader-text">
        <?php esc_html_e('Main Navigation', 'justg'); ?>
    </h2>

    <div class="row align-items-center m-0">
        <div class="col-3 px-0">
            <?php if (has_custom_logo()) {
                echo '<a href="'.get_home_url().'">';
                    echo get_custom_logo();
                echo '</a>';
            } ?>
        </div>
        <div class="col-9 px-0">

            <div class="offcanvas offcanvas-start" tabindex="-1" id="navbarNavOffcanvas">

                <div class="offcanvas-header justify-content-end">
                    <button type="button" class="btn-close btn-close-dark text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div><!-- .offcancas-header -->

                <!-- The WordPress Menu goes here -->
                <?php
                wp_nav_menu(
                    array(
                        'theme_location'  => 'primary',
                        'container_class' => 'offcanvas-body',
                        'container_id'    => '',
                        'menu_class'      => 'navbar-nav navbar-light justify-content-end flex-md-wrap flex-grow-1',
                        'fallback_cb'     => '',
                        'menu_id'         => 'primary-menu',
                        'depth'           => 4,
                        'walker'          => new justg_WP_Bootstrap_Navwalker(),
                    )
                ); ?>

            </div><!-- .offcanvas -->

            <div class="menu-header d-md-none position-relative text-end" data-bs-theme="dark">
                <button class="navbar-toggler bg-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarNavOffcanvas" aria-controls="navbarNavOffcanvas" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle navigation', 'justg'); ?>">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </div>
    </div>

</nav><!-- .site-navigation -->
</div>

<div class="container px-0 my-3">
    <?php if (has_header_image()) {
        echo '<a href="'.get_home_url().'">';
            echo '<img class="w-100 rounded" src="'.esc_url(get_header_image()).'" />';
        echo '</a>';
    } ?>
</div>