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

        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap-theme.min.css">
        <link href='http://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>
        <!--
        <link rel="stylesheet" href="<?php echo $theme_url ?>/includes/fontAwesome/css/font-awesome.min.css" />
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/3.2.0/css/font-awesome.css" />
        -->
        <link rel="stylesheet" href="<?php echo $theme_url ?>/includes/shadowbox/shadowbox.css" />
        <link rel="stylesheet" href="<?php echo $theme_url ?>/style.css" />

        <?php echo eyo_pageMetaTags(); ?>

        <?php wp_head(); ?>
    </head>

    <body>

        <?php if ( current_user_can( 'edit_posts' ) ) : ?>

            <div class="admin-banner">
                <a href="<?php echo get_bloginfo('url') . '?flush=1' ?>">Refresh Data</a>
            </div>

        <?php endif; // end current_user_can() check ?>

        <div class="container">
        
            <header class="row">
                <h1><?php bloginfo( 'title' ); ?></h1>
                <h2><?php echo html_entity_decode( get_bloginfo( 'description' ) ); ?></h2>
            </header>