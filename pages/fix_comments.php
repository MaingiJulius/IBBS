<?php
$files = glob("*.php");
foreach ($files as $file) {
    $content = file_get_contents($file);
    $newContent = preg_replace_callback('/<!--(.*?)-->/s', function($matches) {
        $commentText = $matches[1];
        $commentText = str_replace(["<?=", "<?php", "?>"], ["[echo]", "[php]", "[?]"], $commentText);
        return "<!--" . $commentText . "-->";
    }, $content);
    if ($content !== $newContent) {
        file_put_contents($file, $newContent);
        echo "Fixed $file\n";
    }
}