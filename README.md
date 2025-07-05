# EmboTheme
EmboTheme is an experimental WordPress Full Site Editing (FSE) theme built on the Bulma framework. It demonstrates an object‑oriented approach to building a site that is fully managed via the block editor. The theme includes a set of dynamic blocks and shortcodes implemented as PHP classes and complemented by small JavaScript modules.

## About Full Site Editing
FSE allows WordPress themes to replace traditional PHP templates with reusable block templates. In EmboTheme these templates live under `block-templates/` and `block-template-parts/` and are responsible for all page markup. PHP classes from the `inc/` folder inject dynamic data into these templates – for example menus, breadcrumbs and comment lists – making it easy to adjust the layout solely through the editor.

JavaScript in `src/js` augments the front end. Scripts work together with the PHP classes: `header-ui.js` enhances navigation created by `class-dynamic-menus.php`, while `load-more.js` communicates with `class-ajax-load-more.php` to fetch additional posts.

## Files overview
The file structure is described in detail in the [`wiki`](wiki/README.md) folder. In short:

- `functions.php` loads all classes from `inc` and registers hooks.
- PHP classes in `inc/` provide dynamic blocks and services.
- Block templates and template parts under `block-templates/` and
  `block-template-parts/` define the markup for FSE.
- JavaScript sources are located in `src/js` and handle front‑end behaviour
  such as header UI and AJAX loading.
- Theme configuration is stored in `theme.json`.