# Theme file overview

This document explains the contents of **EmboTheme** and how the components interact. The theme is built on WordPress FSE features and uses an object-oriented approach: each feature is implemented as a class from the `inc` directory, while JavaScript modules from `src/js` complement them on the client.

## Theme root

### `functions.php`
Central entry point that loads all classes from `inc`, initializes them and registers the necessary hooks. It also connects the PHP classes with front-end scripts (`header-ui.js`, `load-more.js`).

### `style.css`
Describes the WordPress theme and provides the base CSS. Includes minified Bulma styles and custom rules.

### `theme.json`
FSE configuration: color palettes, block settings and registration of template part areas. Used by PHP classes as well as block templates.

## Directory `inc`
Classes for the theme's dynamic blocks and services.

### `class-theme-setup.php`
Configures the theme: adds WordPress feature support, creates the front page and enqueues styles/scripts. Other classes rely on its hooks to operate.

### `class-block-patterns.php`
Defines block patterns for informers and other elements. The templates are used in the editor when creating content.

### `class-informer-shortcode.php`
Implements the `[informer]` shortcode that displays one large and several small news items by category. Used in `front-page.html`.

### `class-dynamic-breadcrumbs.php`
Dynamic breadcrumbs block with Schema.org markup. Integrates into templates and shows the link chain for the current record.

### `class-load-toggle.php`
Block for switching between pagination and AJAX loading. Serves as the interface for `class-ajax-load-more.php` and the `load-more.js` script.

### `class-ajax-load-more.php`
Handles AJAX requests returning post HTML. Works with `load-more.js` to fetch additional posts dynamically.

### `class-gutenberg-reset.php`
Utility for resetting Gutenberg settings and clearing template data when the theme is activated.

### `class-cleanup.php`
Removes unnecessary tags and styles from the output and consolidates inline CSS.

### `class-dynamic-menus.php`
Registers dynamic menu blocks for the header and footer. Their markup is provided via `header.html` and is controlled on the client by `header-ui.js`.

### `class-custom-logo-block.php`
Dynamic logo block. Takes the URL from plugin settings or `theme.json`, or falls back to the site title.

### `class-dynamic-comments.php`
Block that displays the standard comment form if comments are enabled.

### `class-category-list-shortcode.php`
Shortcode `[category_list]` that outputs a list of categories formatted as tags.

### `class-post-tags-block.php`
Dynamic block showing the tags of the current post.

### `class-related-posts-block.php`
Dynamic block that displays related posts based on tags.

## Directory `block-template-parts`
HTML templates for site sections from which pages are assembled.

- `header.html` – header with logo, search and menus.
- `aside.html` – sidebar with the posts feed.
- `footer.html` – footer with menus and a note.

## Directory `block-templates`
FSE page templates used by WordPress instead of classic PHP templates.

- `index.html` – base template with columns for posts.
- `front-page.html` – front page featuring the informer.
- `archive.html` – archive listings.
- `single.html` – single post with comments.
- `search.html` – search results.

## Directory `src/js`
JavaScript files used by the theme.

- `header-ui.js` – manages menu overflow and mobile search; works together with blocks from `class-dynamic-menus.php`.
- `load-more.js` – requests additional posts via `class-ajax-load-more.php`.
- `screen-utils.js` – utility for watching screen width, used by other scripts.
