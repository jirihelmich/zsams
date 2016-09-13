<?php get_header(); ?>

<main role="main">
    <!-- section -->
    <section id="content" class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6 col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <i class="material-icons">new_releases</i> Aktuálně
                    </div>
                    <div class="card-block">
                        <?php

                        $source = array("green" => 2, "red" => 4, "blue" => 3);

                        foreach ($source as $color => $key) {
                            ?>
                            <div>
                                <div>
                                    <?php latest_article($key, $color); ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-6 col-xl-4">
                <div class="card contacts">
                    <div class="card-header">
                        <i class="material-icons">contact_phone</i>  Kontakty
                        <!--<a href="#" class="apology">Omluvit žáka?</a>-->
                    </div>
                    <div class="card-block">
                        <div class="col-xs-12 col-md-12 col-lg-6">
                            <?php dynamic_sidebar('kontakty-widget'); ?>
                        </div>
                        <div class="col-lg-6 buttons col-xs-12 col-md-12">
                            <a href="/pedagogicky-sbor/#zs" class="green">Učitelé ZŠ</a>
                            <a href="/pedagogicky-sbor/#club" class="red">Vychovatelé ŠD</a>
                            <a href="/pedagogicky-sbor/#ms" class="blue">Učitelé MŠ</a>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="card upcoming-events">
                        <div class="card-header">
                            <i class="material-icons">event</i> Nadcházející akce
                        </div>
                        <div class="card-block">
                            <?php the_widget("Tribe__Events__List_Widget"); ?>
                        </div>
                    </div>
                </div>

                <div class="hidden-xs-up">
                    <div class="card">
                        <div class="card-header">
                            <i class="material-icons">restaurant</i> Dnešní jídelníček
                            <!--<small>Odhlásit oběd?</small>-->
                        </div>
                        <div class="card-block">
                            <?php the_widget("st_daily_tip_widget"); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 col-xl-2">
                <div class="card">
                    <div class="card-header">
                        <i class="material-icons">photo_library</i> Fotogalerie
                    </div>
                    <div class="card-block">
                        <div>
                            <div><?php the_widget("GalleryWidget", "album=2&includesubs=yes"); ?></div>
                        </div>
                        <div>
                            <div><?php the_widget("GalleryWidget", "album=4&includesubs=yes"); ?></div>
                        </div>
                        <div>
                            <div><?php the_widget("GalleryWidget", "album=3&includesubs=yes"); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <div class="card">
                    <div class="card-header green heading-grid">
                        Základní škola
                    </div>
                    <div class="card-block">
                        <?php elementary_school_nav() ?>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <div class="card">
                    <div class="card-header red heading-grid">
                        Mateřská škola
                    </div>
                    <div class="card-block">
                        <?php kindergarden_nav() ?>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <div class="card">
                    <div class="card-header blue heading-grid">
                        Školní družina
                    </div>
                    <div class="card-block">
                        <?php school_club_nav() ?>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <div class="card timetable">
                    <div class="card-header heading-grid">
                        Rozvrhy tříd
                    </div>
                    <div class="card-block">
                        <div class="row">
                            <span class="col-xs-12 col-sm-4">
                                <a href="/zakladni-skola/rozvrhy-trid/#1" class="btn btn-default green">1.</a>
                            </span>
                            <span class="col-xs-12 col-sm-4">
                                <a href="/zakladni-skola/rozvrhy-trid/#2A" class="btn btn-default green">2.A</a>
                            </span>
                            <span class="col-xs-12 col-sm-4">
                                <a href="/zakladni-skola/rozvrhy-trid/#2B" class="btn btn-default green">2.B</a>
                            </span>
                            <span class="col-xs-12 col-sm-4">
                                <a href="/zakladni-skola/rozvrhy-trid/#3" class="btn btn-default green">3.</a>
                            </span>
                            <span class="col-xs-12 col-sm-4">
                                <a href="/zakladni-skola/rozvrhy-trid/#4" class="btn btn-default green">4.</a>
                            </span>
                            <span class="col-xs-12 col-sm-4">
                                <a href="/zakladni-skola/rozvrhy-trid/#5" class="btn btn-default green">5.</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <div class="card">
                    <div class="card-header heading-grid">
                        Světýlko
                    </div>
                    <div class="card-block">
                        <?php parents_club_nav() ?>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <div class="card">
                    <div class="card-header heading-grid">
                        Jídelna
                    </div>
                    <div class="card-block">
                        <?php dining_nav() ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /section -->
</main>

<?php get_footer(); ?>
