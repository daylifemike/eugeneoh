<?php
/**
 * The template for displaying the footer.
 */
$theme_url = get_template_directory_uri();
?>

            <footer class="row"></footer>
          
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

        <script src="<?php echo $theme_url ?>/js/plugins.js"></script>
        <script src="<?php echo $theme_url ?>/js/main.js"></script>
        <script src="<?php echo $theme_url ?>/includes/shadowbox/shadowbox.js"></script>
        <script>
            (function($) {

                $('.main').EugeneOh({
                    title_base : "<?php echo get_bloginfo('name') ?>"
                });

            }(jQuery));
        </script>
        <?php wp_footer(); ?>
    </body>
</html>