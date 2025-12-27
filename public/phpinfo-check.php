<?php
// PHP Upload Settings Check
echo "<h1>PHP Upload Configuration</h1>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Setting</th><th>Value</th><th>Status</th></tr>";

$upload_max = ini_get('upload_max_filesize');
$post_max = ini_get('post_max_size');
$memory = ini_get('memory_limit');

echo "<tr><td>upload_max_filesize</td><td><strong>$upload_max</strong></td><td>" . (intval($upload_max) >= 15 ? "✅ OK" : "❌ TOO LOW") . "</td></tr>";
echo "<tr><td>post_max_size</td><td><strong>$post_max</strong></td><td>" . (intval($post_max) >= 20 ? "✅ OK" : "❌ TOO LOW") . "</td></tr>";
echo "<tr><td>memory_limit</td><td><strong>$memory</strong></td><td>✅ OK</td></tr>";
echo "</table>";

if (intval($upload_max) >= 15 && intval($post_max) >= 20) {
    echo "<h2 style='color: green;'>✅ Configuration is CORRECT! You can now upload files up to 15MB.</h2>";
} else {
    echo "<h2 style='color: red;'>❌ Configuration is INCORRECT! Please restart the server with the proper settings.</h2>";
    echo "<p><strong>To fix:</strong> Stop the server (Ctrl+C) and run:<br><code>./serve-with-config.sh</code></p>";
}
?>
