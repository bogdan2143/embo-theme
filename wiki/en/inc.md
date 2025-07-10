# Directory `inc`

Contains the PHP classes that supply dynamic behaviour for the theme. The classes follow an object-oriented structure where each feature is encapsulated in its own class and instantiated from `functions.php`.

## Contents

- `class-theme-setup.php` – Boots the theme: registers features, enqueues assets and creates the front page if necessary.
- `class-block-patterns.php` – Declares block patterns available in the editor.
- `class-informer-shortcode.php` – Implements the `[informer]` shortcode showing highlighted news posts.
- `class-dynamic-breadcrumbs.php` – Provides a breadcrumbs block complete with schema markup.
- `class-load-toggle.php` – Switches between pagination and AJAX loading, coordinating with `class-ajax-load-more.php`.
- `class-ajax-load-more.php` – Handles AJAX requests to fetch additional posts.
- `class-gutenberg-reset.php` – Cleans up the editor options when activating the theme.
- `class-cleanup.php` – Removes redundant tags and inlined styles from the output.
- `class-dynamic-menus.php` – Registers dynamic menus for the header and footer.
- `class-custom-logo-block.php` – Outputs the logo as a dynamic block.
- `class-dynamic-comments.php` – Renders the comment form if comments are enabled.
- `class-category-list-shortcode.php` – Shortcode to list categories as tags.
- `class-post-tags-block.php` – Displays post tags in a dynamic block.
- `class-related-posts-block.php` – Shows related posts based on shared tags.
- `class-template-translations.php` – Supplies translations for strings within HTML templates.

By organising features into classes, the theme promotes separation of concerns and reuse – core OOP principles.
