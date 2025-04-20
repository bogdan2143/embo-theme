<?php
/**
 * Клас MyBlockTheme_InformerShortcode
 *
 * Реєструє шорткод інформера.
 */

class MyBlockTheme_InformerShortcode {

    /**
     * Обробляє шорткод [informer] та виводить інформер із постами.
     */
    public function informer_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'category'    => 'news',
            'per_page'    => 4,
            'button_text' => __( 'Читати далі', 'myblocktheme' ),
        ), $atts, 'informer' );

        // Перший запит: вивід одного поста (featured)
        $query_featured = new WP_Query( array(
            'posts_per_page' => 1,
            'post_type'      => 'post',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => $atts['category'],
                ),
            ),
        ) );

        // Другий запит: вивід решти (per_page - 1) постів зі зсувом 1
        $query_small = new WP_Query( array(
            'posts_per_page' => intval( $atts['per_page'] ) - 1,
            'offset'         => 1,
            'post_type'      => 'post',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => $atts['category'],
                ),
            ),
        ) );

        ob_start();
        ?>
        <div class="informer-block">
          <h2 class="title is-4"><?php _e( 'Новини', 'myblocktheme' ); ?></h2>
          <div class="columns">
            <!-- Featured пост -->
            <div class="column is-two-thirds featured-item">
              <?php if ( $query_featured->have_posts() ) : $query_featured->the_post(); ?>
                <figure class="image is-4by3">
                  <a href="<?php the_permalink(); ?>">
                  <?php
                  if ( has_post_thumbnail() ) {
                      the_post_thumbnail( 'large' );
                  } else {
                      echo '<img src="https://via.placeholder.com/600x400" alt="' . esc_attr__( 'Основна новина', 'myblocktheme' ) . '">';
                  }
                  ?>
                  </a>
                </figure>
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <div class="excerpt"><?php the_excerpt(); ?></div>
              <?php endif; wp_reset_postdata(); ?>
            </div>
            <!-- /Featured пост -->
            <!-- Маленькі пости -->
            <div class="column is-one-third">
              <?php if ( $query_small->have_posts() ) : ?>
                <?php while ( $query_small->have_posts() ) : $query_small->the_post(); ?>
                  <div class="small-item">
                    <figure class="image is-4by3">
                      <a href="<?php the_permalink(); ?>">
                      <?php
                      if ( has_post_thumbnail() ) {
                          the_post_thumbnail( 'medium' );
                      } else {
                          echo '<img src="https://via.placeholder.com/300x200" alt="' . esc_attr__( 'Новина', 'myblocktheme' ) . '">';
                      }
                      ?>
                      </a>
                    </figure>
                    <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                    <?php the_excerpt(); ?>
                  </div>
                <?php endwhile; wp_reset_postdata(); ?>
              <?php endif; ?>
            </div>
            <!-- /Маленькі пости -->
          </div>
          <div class="has-text-left">
            <?php 
            $cat_id = get_cat_ID( $atts['category'] );
            if ( $cat_id ) {
                $cat_link = get_category_link( $cat_id );
                $cat_name = get_cat_name( $cat_id );
                if ( strpos( $atts['button_text'], '%s' ) !== false ) {
                    $button_text = sprintf( $atts['button_text'], $cat_name );
                } else {
                    $button_text = $atts['button_text'];
                }
                printf( '<a href="%s" class="is-link">%s</a>', esc_url( $cat_link ), $button_text );
            } else {
                echo '<a href="#" class="is-link">' . __( 'Читати далі', 'myblocktheme' ) . '</a>';
            }
            ?>
          </div>
        </div>
        <?php
        return ob_get_clean();
    }
}