<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Quail Forever
 */
?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="site-info">
			<a href="<?php echo esc_url( __( 'http://wordpress.org/', 'quail-forever' ) ); ?>"><?php printf( __( 'Proudly powered by %s', 'quail-forever' ), 'WordPress' ); ?></a>
			<span class="sep"> | </span>
			<?php printf( __( 'Theme: %1$s by %2$s.', 'quail-forever' ), 'Quail Forever', '<a href="http://underscores.me/" rel="designer">Mitchell Barron</a>' ); ?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
