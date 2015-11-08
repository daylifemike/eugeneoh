<?php
/**
 * The Header for our theme.
 */
$theme_url = get_template_directory_uri();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title><?php bloginfo( 'title' ); ?></title>

        <link rel="profile" href="http://gmpg.org/xfn/11" />
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" />
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css" />
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Raleway" />
        <link rel="stylesheet" href="<?php echo $theme_url ?>/includes/shadowbox/shadowbox.css" />
        <link rel="stylesheet" href="<?php echo $theme_url ?>/css/normalize.css" />
        <link rel="stylesheet" href="<?php echo $theme_url ?>/style.css" />

        <?php echo eyo_pageMetaTags(); ?>

        <?php wp_head(); ?>
    </head>

    <body>

        <div class="container">

            <header class="row">
                <h1><?php bloginfo( 'title' ); ?></h1>
                <h2><?php echo html_entity_decode( get_bloginfo( 'description' ) ); ?></h2>
                <div class="social">
                    <a href="https://www.facebook.com/eugene.oh.58?fref=ts" target="_blank" class="facebook" title="Facebook">
                        <i class="fa fa-facebook"></i>
                    </a>
                    <a href="https://twitter.com/eugeyoung" target="_blank" class="twitter" title="Twitter">
                        <i class="fa fa-twitter"></i>
                    </a>
                    <a href="http://www.imdb.me/eugeneyoung" target="_blank" class="imdb" title="IMDb">
                        <i class="fa fa-video-camera"></i>
                    </a>
                    <a href="http://eugeneyoung.tumblr.com" target="_blank" class="tumblr" title="Tumblr">
                        <i class="fa fa-tumblr"></i>
                    </a>
                    <a href="mailto:realeugeneyoung@gmail.com" target="_blank" class="contact" title="Contact Me">
                        <i class="fa fa-envelope"></i>
                    </a>
                </div>
            </header>