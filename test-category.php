<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

// Test category fetching
$test_slugs = ['electronics', 'fashion', 'home-living', 'books', 'sports'];

echo "<h2>Category Test Results:</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Slug</th><th>Category Found?</th><th>Category Name</th><th>Category ID</th></tr>";

foreach ($test_slugs as $slug) {
    $category = get_category_by_slug($slug);
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($slug) . "</td>";
    echo "<td>" . ($category ? 'YES' : 'NO') . "</td>";
    echo "<td>" . ($category ? htmlspecialchars($category['name']) : 'N/A') . "</td>";
    echo "<td>" . ($category ? $category['id'] : 'N/A') . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<hr>";
echo "<h2>Test URLs:</h2>";
echo "<ul>";
foreach ($test_slugs as $slug) {
    echo "<li><a href='shop.php?category=$slug'>Shop - $slug</a></li>";
}
echo "</ul>";

echo "<hr>";
echo "<h2>Current GET Parameters:</h2>";
echo "<pre>";
print_r($_GET);
echo "</pre>";
?>
