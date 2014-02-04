<?php
/**
 * The main template file.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 */

get_header();
$theme_url = get_template_directory_uri();
$posts = eyo_getData();
?>
    <section class="main row">

        <!-- this is the reel -->
        <?php foreach ($posts as $post) : ?>
            <?php if ( $post->type == 'video' && $post->is_reel ) : ?>
                <div class="item reel col-md-3 col-sm-4 col-xs-12" data-caption="<?php echo strip_tags( $post->{'video-caption'} ); ?>">
                    <div class="video thumbnail" id="<?php echo $post->id; ?>">
                        <a href="<?php echo $post->wordpress_url; ?>" data-url="<?php echo $post->youtube->embed_url; ?>">
                            <?php if ( !empty($post->custom_thumbnail) ) : ?>
                                <img class="img-responsive" src="<?php echo $post->custom_thumbnail; ?>" alt="" title=""/>
                            <?php else : ?>
                                <img class="img-responsive" src="<?php echo $post->youtube->thumbnail; ?>" alt="" title=""/>
                            <?php endif; ?>
                            <?php if ( strip_tags( $post->{'video-caption'} ) ) : ?>
                                <div class="reel-badge">
                                    <p>
                                        <?php echo strip_tags( $post->{'video-caption'} ); ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach ;?>

        <?php foreach ($posts as $post) : ?>
            <?php if ( !$post->is_reel ) : ?>
                <div class="item col-md-3 col-sm-4 col-xs-12">

                    <?php if ( $post->type == 'photo' ) : ?>
                        <div class="photo thumbnail" id="<?php echo $post->id; ?>">
                            <a href="<?php echo $post->wordpress_url; ?>" data-url="<?php echo $post->{'photo-url-1280'}; ?>">
                                <img class="img-responsive" src="<?php echo $post->{'photo-url-500'}; ?>" alt="" title=""/>
                                <?php if ( strip_tags( $post->{'photo-caption'} ) ) : ?>
                                    <div class="caption">
                                        <p>
                                            <?php echo strip_tags( $post->{'photo-caption'} ); ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </a>
                        </div>

                    <?php elseif ( $post->type == 'video' && $post->youtube ) : ?>
                        <div class="video thumbnail" id="<?php echo $post->id; ?>">
                            <a href="<?php echo $post->wordpress_url; ?>" data-url="<?php echo $post->youtube->embed_url; ?>">
                                <?php if ( !empty($post->custom_thumbnail) ) : ?>
                                    <img class="img-responsive" src="<?php echo $post->custom_thumbnail; ?>" alt="" title=""/>
                                <?php else : ?>
                                    <img class="img-responsive" src="<?php echo $post->youtube->thumbnail; ?>" alt="" title=""/>
                                <?php endif; ?>
                                <?php if ( strip_tags( $post->{'video-caption'} ) ) : ?>
                                    <div class="caption">
                                        <p>
                                            <?php echo strip_tags( $post->{'video-caption'} ); ?> (<?php echo $post->youtube->duration ?>)
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </a>
                        </div>

                    <?php elseif ( $post->type == 'video' && !$post->youtube ) : ?>
                        <!-- placeholder for private video -->
                        <div class="null thumbnail">
                            <div class="placeholder">
                                <img class="img-responsive" src="<?php echo $theme_url ?>/img/no-image.png" alt="" title=""/>
                                <div class="caption">
                                    <p>This video is private.</p>
                                </div>
                            </div>
                        </div>

                    <?php elseif ( $post->type == 'link' ) : ?>
                        <div class="link thumbnail">
                            <div>
                                <a href="<?php echo $post->{'link-url'}; ?>">
                                    <?php echo $post->slug; ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endif; ?>
        <?php endforeach ;?>

    </section><!-- .main -->

<?php get_footer(); ?>