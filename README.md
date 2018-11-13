Mobiledoc PHP
=============

A PHP-based renderer for the mobiledoc format.


Rendering Mobiledoc
-------------------

```php
use Becklyn\Mobiledoc\Extension\ExtensionRegistry;
use Becklyn\Mobiledoc\Renderer\MobiledocRenderer;

$extensions = new ExtensionRegistry();
$renderer = new MobiledocRenderer($extensions);

// returns the rendered document
$document = $renderer->render([
    "version" => "0.3.1",
    // ... rest of the mobiledoc document
]);


// returns the mobiledoc
$document->getMobiledoc();

// returns the HTML
$document->getHtml();
(string) $document:
```



Registering Extensions
----------------------

Your extension must extend `RichTextExtensionInterface`. Cards and Atoms are both handled universally, so there is no separation in the code.
You can only have one of extension of any type for a given name.

```php
use Becklyn\Mobiledoc\Extension\ExtensionRegistry;
use Becklyn\Mobiledoc\Extension\RichTextExtensionInterface;


class IframeCard implements RichTextExtensionInterface
{
    /**
     * @inheritDoc
     */
    public function getName () : string
    {
        return "iframe";
    }


    /**
     * @inheritDoc
     */
    public function render (?string $content, array $payload) : string
    {
        return '<iframe src="' . $payload["src"] . '"></iframe>';
    }
}


$extensions = new ExtensionRegistry();
$extensions->registerExtension(new IframeCard());
```

* Atoms receive the text content in the `$content` parameter, cards will *always* receive `null` as content.
* Missing atoms fall back to their content as plain text.
* Missing cards fall back are not rendered.
