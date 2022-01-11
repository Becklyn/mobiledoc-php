2.0.2
=====

*   (improvement) Add support for PHP 8.0.
*   (internal) Replace TravisCI with GitHub Actions.
*   (internal) Bump minimum required version to PHP 7.4.
*   (internal) Add GitHub Code Owners + Pull Request Template.
*   (improvement) Use typed properties everywhere and remove redundant PhpDoc.


2.0.1
=====

*   (bug) Added missing class for heading tags (the class is equal to the tag name, e.g. `<h1>` will now be rendered as `<h1 class="h1">`)
*   (internal) Fixed tests that have previously gone red due to a bad merge


2.0.0
=====

*   (bc) Removed the `SimpleDocument`. The return type of `MobiledocRenderer::render()` changed because of this. It now just returns `?string` – the rendered HTML or `null`.
*   (feature) Added markup attribute visitors, that can modify the attributes before rendering


1.0.1
=====

*   (feature) Added a mobiledoc -> HTML parser
*   (feature) Added rendering of markup attributes


1.0.0
=====

*   (feature) Implements the whole mobiledoc spec for `0.3.1`.
