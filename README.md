<details open>
<summary>English</summary>

# EmboTheme

[![CI](https://github.com/bogdan2143/embo-theme/actions/workflows/ci.yml/badge.svg)](https://github.com/bogdan2143/embo-theme/actions/workflows/ci.yml) [![Domain Build](https://github.com/bogdan2143/embo-theme/actions/workflows/build-domains.yml/badge.svg?branch=sity.top)](https://github.com/bogdan2143/embo-theme/actions/workflows/build-domains.yml)

EmboTheme is an WordPress Full Site Editing (FSE) theme built on the Bulma framework. It demonstrates an object‑oriented approach to building a site fully managed through the block editor. The theme includes a set of dynamic blocks and shortcodes implemented as PHP classes and complemented by small JavaScript modules.

## Theme Guidelines

- When starting development, follow the [object-oriented approach](wiki/en/files.md).
- Add new features as separate class files using the pattern in [the `inc` directory documentation](wiki/en/inc.md).
- To provide translations, see the workflow in [the `languages` folder](wiki/en/languages.md).
- Deployment instructions are described in [Deployment](#deployment).
- IDE recommendations are listed in [IDE Setup](#ide-setup).

## About Full Site Editing

FSE allows WordPress themes to replace traditional PHP templates with reusable block templates. In EmboTheme these templates live under `block-templates/` and `block-template-parts/` and are responsible for all page markup. PHP classes from the `inc/` folder inject dynamic data into these templates – for example menus, breadcrumbs and comment lists – making it easy to adjust the layout solely through the editor.

JavaScript in `src/js` augments the front end. Scripts work together with the PHP classes: `header-ui.js` enhances navigation created by `class-dynamic-menus.php`, while `load-more.js` communicates with `class-ajax-load-more.php` to fetch additional posts.

During rendering Gutenberg turns each `wp:group` into `<div style="...">` and inserts options from `theme.json` inline. This yields a large `<style>` tag in the `<head>` that is difficult to override.

To keep the markup clean EmboTheme uses custom `className`s whenever possible and collects remaining inline rules into a single block appended after all CSS. Many blocks are registered in PHP with a `render_callback`, while `aside.html`, `header.html` and `footer.html` are entirely custom along with the burger menu.

The theme relies on WordPress hooks, filters and the Gutenberg APIs. Additional configuration comes from our [EmboSettings](https://github.com/bogdan2143/EmboSettings) plugin. We initially considered serving the front end in JavaScript, but the available APIs proved inadequate, so this idea was postponed for now.

## Files overview

The file structure is described in detail in the [wiki](wiki/en/README.md) folder. In short:

- `functions.php` loads all classes from `inc` and registers hooks.
- PHP classes in [`inc/`](wiki/en/inc.md) provide dynamic blocks and services.
- Block templates and template parts under [`block-templates/`](wiki/en/block-templates.md) and [`block-template-parts/`](wiki/en/block-template-parts.md) define the markup for FSE.
- Translation files live in [`languages/`](wiki/en/languages.md).
- Documentation is stored in the [`wiki/`](wiki/en/wiki.md) directory.
- JavaScript sources are located in `src/js` and handle front‑end behaviour such as header UI and AJAX loading.
- Theme configuration is stored in `theme.json`.

## Plugin Dependencies

When the theme is activated a small installer automatically downloads and activates two plugins:

- **EmboSettings**
- **Git Updater**

Composer also installs these plugins during the build step so the domain workflow already has them available.

## Deployment

Branches match domain names (e.g. `sity.top`). Pushing to such a branch runs the [`build-domains.yml`](.github/workflows/build-domains.yml) workflow which:

- Installs Node packages from `package.json`.
- Installs PHP dependencies from `composer.json` and preloads plugins (`EmboSettings` and `Git Updater`).
- Runs `vendor/bin/phpcs` with `phpcs.xml` to check WordPress standards.
- Builds assets using Webpack defined in `webpack.config.js`.
- Injects `GitHub Theme URI` and the branch name into `style.css`.
- Pushes the change back to the branch. Accept the auto-generated commit through a pull request so maintainers can review the overwritten lines.

## Theme Updates

The build script sets the repository URL and branch in `style.css`. Increase the `Version` value and push changes whenever a release is ready. Afterwards open **Git Updater** in the WordPress admin and press **Check Again** to install the new version.

## IDE Setup

- On Windows use **GitHub Desktop** to switch branches.
- In Sublime Text use [Sublime Merge](https://www.sublimemerge.com) or the GitSavvy package.
- Always configure the IDE to display the current branch so you know which domain you are editing.
- Merge from `main` only if necessary and with care; large differences can make this approach obsolete.
</details>

<details>
<summary>Українською</summary>

# EmboTheme

[![CI](https://github.com/bogdan2143/embo-theme/actions/workflows/ci.yml/badge.svg)](https://github.com/bogdan2143/embo-theme/actions/workflows/ci.yml) [![Domain Build](https://github.com/bogdan2143/embo-theme/actions/workflows/build-domains.yml/badge.svg?branch=sity.top)](https://github.com/bogdan2143/embo-theme/actions/workflows/build-domains.yml)
EmboTheme — тема WordPress з підтримкою Full Site Editing (FSE) на базі фреймворка Bulma. Вона демонструє об'єктно-орієнтований підхід до побудови сайту, який повністю керується через блоковий редактор. Тема містить набір динамічних блоків і шорткодів, реалізованих у вигляді PHP‑класів та доповнених невеликими JavaScript‑модулями.

## Themes Guidelines

- Приступаючи до розробки, дотримуйтеся [об'єктно-орієнтованого підходу](wiki/uk/files.md).
- Нові можливості додавайте окремими файлами класів, як описано в [папці `inc`](wiki/uk/inc.md).
- Для перекладу текстів користуйтеся схемою з [каталогу `languages`](wiki/uk/languages.md).
- Інструкції щодо деплою знаходяться в розділі [Деплой](#деплой).
- Рекомендації по IDE описані в розділі [Налаштування IDE](#налаштування-ide).

## Про Full Site Editing

FSE дозволяє темам WordPress замінювати класичні PHP‑шаблони багаторазовими блоковими шаблонами. У EmboTheme ці шаблони розміщено в каталогах `block-templates/` та `block-template-parts/` і вони відповідають за всю розмітку сторінок. PHP‑класи з каталогу `inc/` забезпечують динамічні дані — наприклад, меню, хлібні крихти та списки коментарів — що дозволяє налаштовувати макет лише через редактор.

JavaScript у `src/js` доповнює роботу фронтенду. Скрипти взаємодіють із PHP‑класами: `header-ui.js` розширює навігацію, створену `class-dynamic-menus.php`, а `load-more.js` звертається до `class-ajax-load-more.php` для підвантаження додаткових записів.

Під час рендерингу Gutenberg перетворює кожен `wp:group` на `<div style="...">`, а властивості з `theme.json` додає інлайном. Це формує великий `<style>` у `<head>` і стилі важко перекривати.

У темі використано власні `className` де можливо, а всі інлайни збираються в єдиний блок після CSS. Блоки часто реєструються у PHP із `render_callback`, а файли `aside.html`, `header.html` і `footer.html` повністю кастомні, як і burger‑меню.

Ми спираємося на хуки, фільтри та API WordPress і Gutenberg. Для додаткових налаштувань існує плагін [EmboSettings](https://github.com/bogdan2143/EmboSettings). Ідея рендерити фронтенд на JavaScript відкладена через обмеження API, але в майбутньому це може змінитись.

## Огляд файлів

Структура файлів докладно описана у [вікі](wiki/uk/README.md). Коротко:

- `functions.php` підключає всі класи з `inc` та реєструє хуки.
- PHP‑класи у [`inc/`](wiki/uk/inc.md) забезпечують динамічні блоки й сервіси.
- Блокові шаблони та частини шаблонів у [`block-templates/`](wiki/uk/block-templates.md) та [`block-template-parts/`](wiki/uk/block-template-parts.md) визначають розмітку для FSE.
- Файли перекладів розміщено в [`languages/`](wiki/uk/languages.md).
- Документація міститься у каталозі [`wiki/`](wiki/uk/wiki.md).
- Джерела JavaScript знаходяться у `src/js` і відповідають за поведінку інтерфейсу, наприклад, за меню та AJAX‑завантаження.
- Налаштування теми зберігаються у `theme.json`.

## Залежні плагіни

Під час активації тема автоматично завантажує та активує два плагіни:

- **EmboSettings**
- **Git Updater**

Composer також встановлює ці плагіни під час збірки, тож доменний workflow одразу має їх напоготові.

## Деплой

Гілки називаються за доменами (наприклад, `sity.top`). Пуш у таку гілку запускає workflow [`build-domains.yml`](.github/workflows/build-domains.yml), який:

- встановлює Node‑пакети з `package.json`;
- встановлює залежності Composer з `composer.json` та попередньо ставить плагіни (`EmboSettings` і `Git Updater`);
- перевіряє код на стандарти WordPress за допомогою `phpcs.xml`;
- збирає assets через Webpack із `webpack.config.js`;
- додає рядки `GitHub Theme URI` та назву гілки в `style.css`.
- пушить оновлену гілку назад. Автоматичний коміт слід прийняти через pull request, щоб преємники підтвердили перезапис цих рядків.

## Оновлення теми

Під час збірки в `style.css` підставляються `GitHub Theme URI` та `GitHub Branch`. При випуску нової версії збільшуйте рядок `Version` і пуште зміни. Потім у адмінці WordPress відкрийте розділ **Git Updater** і натисніть **Check Again**, щоб встановити оновлення.

## Налаштування IDE

- Під Windows зручно перемикати гілки у **GitHub Desktop**.
- У Sublime Text можна скористатись [Sublime Merge](https://www.sublimemerge.com)
  або плагіном GitSavvy.
- Завжди вмикайте відображення поточної гілки, щоб знати який домен ви редагуєте.
- Злиття з `main` робіть обережно; при значних розбіжностях такий підхід може втратити актуальність.
</details>