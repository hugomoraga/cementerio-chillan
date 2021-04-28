    <?php
        /**
         * Template Name: Template: Init
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
                        <div class="">
                        <article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

                        <header class="entry-header">


                        </header><!-- .entry-header -->


                        <div class="entry-content">


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

                
                    <section id="intro">
                        <div class="container">
                            <div class="row"> 
                            <article class="col-md-4">
                                <div class="card m-2 p-2">
                                    <h3 class="heading">Quisque vehicula urna amet</h3>
                                        <ul class="nospace">
                                            <li>ullamcorper mauris sit amet</li>
                                            <li>sed eget ultricies sem</li>
                                            <li>proin quis lacus egestas</li>
                                            <li>adipis cing ornare</li>
                                            <li>donec luctus convallis rhoncus</li>
                                        </ul>
                                </div>
                            </article>
                            <article class="col-md-4">
                                <div class="card m-2 p-2 ">
                                    <h3 class="heading">Aliquam purus urna porta faucibus</h3>
                                        <ul class="nospace">
                                            <li>ullamcorper mauris sit amet</li>
                                            <li>sed eget ultricies sem</li>
                                            <li>proin quis lacus egestas</li>
                                            <li>adipis cing ornare</li>
                                            <li>donec luctus convallis rhoncus</li>
                                        </ul>
                                </div>
                            </article>
                            <article class="col-md-4">
                                <div class="card m-2 p-2 ">
                                <h3 class="heading">Proin ultricies dui leo egestas augue</h3>
                                <ul class="nospace">
                                <li>ullamcorper mauris sit amet</li>
                                <li>sed eget ultricies sem</li>
                                <li>proin quis lacus egestas</li>
                                <li>adipis cing ornare</li>
                                <li>donec luctus convallis rhoncus</li>
                                </ul>
                                <div>
                            </article>
                            </div>
                        </div>
                    </section>

                    <br>
            <section id="hold-slider">
                <div class="row">
                    <div class="col-md-8">
                        <div class="m-3" style="height: 300px;">
                            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                                    <ol class="carousel-indicators">
                                        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                                        <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                                        <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                                    </ol>
                            </div>
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img class="" src="https://via.placeholder.com/700x300/09f/fff.png" alt="First slide">
                                </div>
                                <div class="carousel-item">
                                    <img class="" src="https://via.placeholder.com/700x300/008/fff.png" alt="Second slide">
                                </div>
                                <div class="carousel-item">
                                    <img class="" src="https://via.placeholder.com/700x300/ff2/fff.png> "alt="Third slide">
                                </div>
                            </div>

                            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="shadow-lg p-3 mb-5 card rounded" style="height: 300px;">
                           <p class="p-3"> Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, sequi itaque quas fugit quasi sint similique earum amet nam cum molestias hic libero explicabo odit ratione recusandae voluptate voluptatem nesciunt. </p>
                        </div>
                    <div>


                    

                </div>
            </section>

            </div>
        </div>
   
    