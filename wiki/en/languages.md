# Directory `languages`

Holds translation files for the theme. Using standard WordPress localisation, text strings from both PHP and HTML templates can be translated into different languages.

## Contents

- `myblocktheme-uk_UA.po` – Source strings and Ukrainian translations.
- `myblocktheme-uk_UA.mo` – Machine-compiled version of the same translations.

The `class-template-translations.php` class loads these files and ensures that any text hard-coded in template parts is properly localised. You can add additional `.po/.mo` pairs for other languages following the same naming pattern.
