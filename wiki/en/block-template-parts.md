# Directory `block-template-parts`

This folder holds reusable HTML snippets that assemble the site's layout. Each file represents a template part inserted into multiple page templates. Because the theme relies on WordPress Full Site Editing, these parts replace what would traditionally be PHP includes.

## Contents

- `header.html` – The site header containing the logo, search form and navigation menus. Its structure is complemented by `class-dynamic-menus.php` and enhanced with the `header-ui.js` script.
- `aside.html` – The sidebar used on index and archive pages. It normally displays the post feed along with any widgets that you add in the editor.
- `footer.html` – The footer section with menus and a copyright note.

The parts are referenced by templates from `block-templates/` and can be modified in the block editor. They illustrate how the theme separates concerns: layout pieces are stored as individual HTML files while dynamic data comes from PHP classes in `inc/`.
