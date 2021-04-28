    <?php
    /**
     * Template Name: Template: Home
     *
     * Template for displaying a page without sidebar even if a sidebar widget is published.
     *
     * @package UnderStrap
     */

    // Exit if accessed directly.
    defined( 'ABSPATH' ) || exit;

    get_header();
    ?>
    ?>

    <div class="home-banner">

        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="contenido p-5">
                    <article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

                    <header class="entry-header">

                        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

                    </header><!-- .entry-header -->

                    <?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>

                    <div class="entry-content">

                        <?php the_content(); ?>

                        <?php
                        wp_link_pages(
                            array(
                                'before' => '<div class="page-links">' . __( 'Pages:', 'understrap' ),
                                'after'  => '</div>',
                            )
                        );
                        ?>

                    </div><!-- .entry-content -->

                    <footer class="entry-footer">

                        <?php edit_post_link( __( 'Edit', 'understrap' ), '<span class="edit-link">', '</span>' ); ?>

                    </footer><!-- .entry-footer -->

                    </article><!-- #post-## -->
                    <div/>

                </div>

                
            </div>
        </div>
    </div>

   