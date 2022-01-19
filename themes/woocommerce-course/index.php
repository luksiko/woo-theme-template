<?php get_header() ?>

<?php if (have_posts()):
    while (have_posts()): ?>
        <?php the_post() ?>
			<h1><?php the_title() ?></h1>
        <?php the_content() ?>
    <?php endwhile; ?>
<?php else : ?>
	<p>No content found</p>
<?php endif; ?>


<?php get_footer() ?>
