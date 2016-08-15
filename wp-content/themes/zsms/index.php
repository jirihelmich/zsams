<?php get_header(); ?>

<main role="main">
    <!-- section -->
    <section id="content" class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header heading-grid">
                        Aktuálně
                    </div>
                    <div class="card-block">
                        <?php

                        $source = array("green" => 2, "red" => 3, "blue" => 4);

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

            <div class="col-sm-4">
                <div class="card contacts">
                    <div class="card-header heading-grid">
                        Kontakty
                        <!--<a href="#" class="apology">Omluvit žáka?</a>-->
                    </div>
                    <div class="card-block">
                        <div class="row">
                            <div class="col-sm-6">
                                <p>
                                    <strong>Ředitelka </strong>
                                    Mgr. Jana Kuhnová <br />
                                    <a href="mailto:kuhnova@zsamszlicin.cz">kuhnova@zsamszlicin.cz </a><br />
                                    +420 222 333 444  
                                </p>
                                <p>
                                    <strong>Odhlášení obědů </strong>
                                    Alena Rücklová <br />
                                    po - čt 6:30 - 8:00 <br />
                                    pá 7:00 - 8:00 <br />
                                     <a href="mailto:obedy@zsamszlicin.cz"> obedy@zsamszlicin.cz</a><br />
                                    +420 222 333 445
                                </p>
                            </div>
                            <div class="col-sm-6 buttons">
                                <a href="#" class="green">Učitelé ZŠ</a>
                                <a href="#" class="red">Vychovatelé ŠD</a>
                                <a href="#" class="blue">Učitelé MŠ</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="card upcoming-events">
                        <div class="card-header heading-grid">
                            Nadcházející akce
                        </div>
                        <div class="card-block">
                            <?php the_widget("Tribe__Events__List_Widget"); ?>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="card">
                        <div class="card-header heading-grid">
                            Dnešní jídelníček
                            <!--<small>Odhlásit oběd?</small>-->
                        </div>
                        <div class="card-block">
                            <!--<?php the_widget("st_daily_tip_widget"); ?>-->
                            <p class="text-xs-center">Obědy budou vydávány v průběhu školního roku.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-2">
                <div class="card">
                    <div class="card-header heading-grid">
                        Fotogalerie
                    </div>
                    <div class="card-block">
                        <div>
                            <div><?php //the_widget("LasTenWidget", "album=2"); ?></div>
                        </div>
                        <div>
                            <div><?php //the_widget("LasTenWidget", "album=3"); ?></div>
                        </div>
                        <div>
                            <div><?php //the_widget("LasTenWidget", "album=4"); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-sm-4">
                <div class="card">
                    <div class="card-header green heading-grid">
                        Základní škola
                    </div>
                    <div class="card-block">
                        <?php elementary_school_nav() ?>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="card">
                    <div class="card-header red heading-grid">
                        Mateřská škola
                    </div>
                    <div class="card-block">
                        <?php kindergarden_nav() ?>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="card">
                    <div class="card-header blue heading-grid">
                        Školní družina
                    </div>
                    <div class="card-block">
                        <?php school_club_nav() ?>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-header heading-grid">
                        Rozvrhy tříd
                    </div>
                    <div class="card-block">
                        <p class="text-xs-center">
                            Rozvrhy tříd připravujeme.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="card">
                    <div class="card-header heading-grid">
                        Světýlko
                    </div>
                    <div class="card-block">
                        <?php parents_club_nav() ?>
                    </div>
                </div>
            </div>

            <div class="col-sm-4">
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

<?php get_sidebar(); ?>

<?php get_footer(); ?>
