# Directory `block-templates`

Page templates used by WordPress in place of classic PHP files. Each template defines the overall layout for a particular context. They are built from the parts stored in `block-template-parts/` and can be customised through the editor.

## Contents

- `index.html` – Base template showing the post list in two columns with a sidebar.
- `front-page.html` – The home page layout featuring the informer block.
- `archive.html` – Template for category and date archives.
- `single.html` – Layout for single posts including the comment section.
- `search.html` – Displays search results.

These templates showcase the inheritance concept typical of OOP: common sections are shared via template parts, reducing duplication while keeping responsibilities well defined.
