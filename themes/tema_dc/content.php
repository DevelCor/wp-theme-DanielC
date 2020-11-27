<div class="content">
    <main class="columns">
        <?php if(is_archive()):  ?>
            <h1 class="archive"><?php the_archive_title();?></h1>
            <div><?php the_archive_description();?></div>
        <?php endif;?>

        <?php if(is_search()):?>
            <h1>
                Resultados para: <?php echo get_search_query();?>
            </h1>
        <?php endif?>

        <?php if(is_author()) :?>
            <div class="author-info">
                <h2>Post publicados por</h2>
                <div class="author-details">
                    <div>
                        <?php echo get_avatar( get_the_author_meta( 'user_email',50 ) ); ?>
                    </div>
                    <div class="author-description">
                        <h3> <?php the_author();?></h3>
                    </div>
                </div>
            </div>
        <?php endif; ?>



        <?php if(have_posts() ):?>
            <?php while(have_posts()):the_post() ?>
            <article id="post-<?php the_ID();?>" <?php post_class('post-class');?>>
                <header>
                    <?php if( is_single() ):?>
                        <h1 class="post-title"><?php the_title() ;?></h1>
                    <?php else:?>
                        <h2><a href="<?php the_permalink();?>" class="post-title"> <?php the_title() ;?> </a> </h2>
                        <p><?php echo get_the_category_list( ' / ' )?></p>  
                    <?php endif;?>             
                </header>
                <time>
                    <?php the_date();?>
                </time>
                <span>
                    | <a href="<?php echo get_author_posts_url( get_the_author_meta('ID') );?>"><?php echo get_the_author()?></a>
                </span>
                    <?php if(is_single()) : ?>
                        <?php the_content(); ?>
                    <?php else: ?>
                        <?php the_excerpt() ; ?>
                    <?php endif;?>
                <div>
                    <footer>
                        <ul><?php the_tags('<li>','</li><li>','</li>'); ?></ul>
                        <span><?php edit_post_link('Editar'); ?> </span>
                    </footer>
                </div>
            </article>
            <?php endwhile?>
            <nav>
                <div>
                    <?php previous_post_link();?>
                </div>
                
                <div>    
                    <?php next_post_link(); ?>
                </div>
            </nav>
        <?php endif?>
    </main>

    <?php if(is_active_sidebar( 'sidebar-widgets' )):?>
        <aside>
            <?php dynamic_sidebar('sidebar-widgets'); ?>
        </aside>
    <?php endif;?>
</div>