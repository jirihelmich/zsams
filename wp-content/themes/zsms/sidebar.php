<!-- sidebar -->
<aside class="sidebar hidden-md-down" role="complementary">
    <div class="card contacts">
        <div class="card-header heading-grid">
            Kontakty
            <!--<a href="#" class="apology">Omluvit žáka?</a>-->
        </div>
        <div class="card-block">
            <?php dynamic_sidebar('kontakty-widget'); ?>
        </div>
    </div>

    <div class="sidebar-widget">
        <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('widget-area-1')) ?>
    </div>

    <div class="sidebar-widget">
        <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('widget-area-2')) ?>
    </div>

</aside>
<!-- /sidebar -->
