<?php
defined( 'ABSPATH' ) || exit;
$title = trim( (string) ( $attributes['title'] ?? '' ) );
$body = trim( (string) ( $attributes['body'] ?? '' ) );
if ( '' === $title && '' === $body ) { return; }
?>
<aside <?php echo get_block_wrapper_attributes( [ 'class' => 'fp-service-note' ] ); ?>>
    <?php if ( '' !== $title ) : ?><h3><?php echo esc_html( $title ); ?></h3><?php endif; ?>
    <?php if ( '' !== $body ) : ?><p><?php echo esc_html( $body ); ?></p><?php endif; ?>
</aside>
