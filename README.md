<details open>
<summary>Українською</summary>

# EmboTheme
EmboTheme — тема WordPress з підтримкою Full Site Editing (FSE) на базі фреймворка Bulma. Вона демонструє об'єктно-орієнтований підхід до побудови сайту, який повністю керується через блоковий редактор. Тема містить набір динамічних блоків і шорткодів, реалізованих у вигляді PHP‑класів та доповнених невеликими JavaScript‑модулями.

## Про Full Site Editing
FSE дозволяє темам WordPress замінювати класичні PHP‑шаблони багаторазовими блоковими шаблонами. У EmboTheme ці шаблони розміщено в каталогах `block-templates/` та `block-template-parts/` і вони відповідають за всю розмітку сторінок. PHP‑класи з каталогу `inc/` забезпечують динамічні дані — наприклад, меню, хлібні крихти та списки коментарів — що дозволяє налаштовувати макет лише через редактор.

JavaScript у `src/js` доповнює роботу фронтенду. Скрипти взаємодіють із PHP‑класами: `header-ui.js` розширює навігацію, створену `class-dynamic-menus.php`, а `load-more.js` звертається до `class-ajax-load-more.php` для підвантаження додаткових записів.

## Огляд файлів
Структура файлів докладно описана у [вікі](wiki/uk/README.md). Коротко:

- `functions.php` підключає всі класи з `inc` та реєструє хуки.
- PHP‑класи у `inc/` забезпечують динамічні блоки й сервіси.
- Блокові шаблони та частини шаблонів у `block-templates/` та `block-template-parts/` визначають розмітку для FSE.
- Джерела JavaScript знаходяться у `src/js` і відповідають за поведінку інтерфейсу, наприклад, за меню та AJAX‑завантаження.
- Налаштування теми зберігаються у `theme.json`.

</details>

<details>
<summary>English</summary>

# EmboTheme
EmboTheme is an WordPress Full Site Editing (FSE) theme built on the Bulma framework. It demonstrates an object‑oriented approach to building a site fully managed through the block editor. The theme includes a set of dynamic blocks and shortcodes implemented as PHP classes and complemented by small JavaScript modules.

## About Full Site Editing
FSE allows WordPress themes to replace traditional PHP templates with reusable block templates. In EmboTheme these templates live under `block-templates/` and `block-template-parts/` and are responsible for all page markup. PHP classes from the `inc/` folder inject dynamic data into these templates – for example menus, breadcrumbs and comment lists – making it easy to adjust the layout solely through the editor.

JavaScript in `src/js` augments the front end. Scripts work together with the PHP classes: `header-ui.js` enhances navigation created by `class-dynamic-menus.php`, while `load-more.js` communicates with `class-ajax-load-more.php` to fetch additional posts.

## Files overview
The file structure is described in detail in the [wiki](wiki/en/README.md) folder. In short:

- `functions.php` loads all classes from `inc` and registers hooks.
- PHP classes in `inc/` provide dynamic blocks and services.
- Block templates and template parts under `block-templates/` and `block-template-parts/` define the markup for FSE.
- JavaScript sources are located in `src/js` and handle front‑end behaviour such as header UI and AJAX loading.
- Theme configuration is stored in `theme.json`.

</details>