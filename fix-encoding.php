<?php
/**
 * ALTERNATIVE ENCODING FIX - No Database ALTER Required
 * This handles encoding at PHP level if you can't change database
 * 
 * Save as: fix-encoding.php
 * Run once: https://trendsone.buildmyngo.space/fix-encoding.php
 * DELETE after running!
 */

// Password protect this script
define('FIX_PASSWORD', 'admin123'); // Change this!

// Check password
if (!isset($_GET['password']) || $_GET['password'] !== FIX_PASSWORD) {
    die('Access Denied. Use: ?password=admin123');
}

// Force UTF-8
mb_internal_encoding('UTF-8');
header('Content-Type: text/html; charset=UTF-8');

// Load config
require_once __DIR__ . '/config/config.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Encoding Fix - Alternative Method</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #333; }
        .success { 
            background: #d4edda; 
            color: #155724; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 10px 0;
            border-left: 4px solid #28a745;
        }
        .error { 
            background: #f8d7da; 
            color: #721c24; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 10px 0;
            border-left: 4px solid #dc3545;
        }
        .info { 
            background: #d1ecf1; 
            color: #0c5460; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 10px 0;
            border-left: 4px solid #17a2b8;
        }
        .step {
            background: #f8f9fa;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .step h3 {
            margin-top: 0;
            color: #007bff;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 10px 5px;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th {
            background: #007bff;
            color: white;
            padding: 10px;
            text-align: left;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        table tr:hover {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Alternative Encoding Fix</h1>
        <p>This method fixes encoding issues without requiring database ALTER permissions.</p>

        <?php
        $action = $_GET['action'] ?? 'info';
        
        if ($action === 'info') {
        ?>
            <div class="info">
                <strong>ℹ️ About This Fix:</strong><br>
                Since you can't ALTER the database directly, we'll fix encoding at the PHP level using connection settings.
            </div>

            <div class="step">
                <h3>Step 1: Test Current Encoding</h3>
                <p>Let's see how your database currently handles the rupee symbol.</p>
                <a href="?password=<?php echo FIX_PASSWORD; ?>&action=test" class="btn">Run Test</a>
            </div>

            <div class="step">
                <h3>Step 2: View Current Products</h3>
                <p>Check how product prices are currently displayed.</p>
                <a href="?password=<?php echo FIX_PASSWORD; ?>&action=view" class="btn">View Products</a>
            </div>

            <div class="step">
                <h3>Step 3: Apply PHP-Level Fix</h3>
                <p>The new config.php file already includes this fix! Just upload it.</p>
                <code style="display: block; padding: 10px; margin: 10px 0;">
                    $pdo->exec("SET NAMES utf8mb4");<br>
                    $pdo->exec("SET character_set_connection=utf8mb4");<br>
                    $pdo->exec("SET character_set_client=utf8mb4");<br>
                    $pdo->exec("SET character_set_results=utf8mb4");
                </code>
            </div>

        <?php
        } elseif ($action === 'test') {
        ?>
            <h2>🧪 Encoding Test Results</h2>
            
            <?php
            try {
                // Test 1: Direct symbol test
                echo '<div class="step">';
                echo '<h3>Test 1: Direct Rupee Symbol</h3>';
                echo '<p style="font-size: 24px;">Direct output: ₹</p>';
                echo '<p style="font-size: 24px;">From PHP constant: ' . CURRENCY_SYMBOL . '</p>';
                echo '<p style="font-size: 24px;">HTML entity: &#8377;</p>';
                echo '</div>';

                // Test 2: Database connection charset
                echo '<div class="step">';
                echo '<h3>Test 2: Database Connection Settings</h3>';
                $stmt = $pdo->query("SHOW VARIABLES LIKE 'character_set%'");
                $charset_vars = $stmt->fetchAll();
                
                echo '<table>';
                echo '<tr><th>Variable</th><th>Value</th><th>Status</th></tr>';
                foreach ($charset_vars as $var) {
                    $is_utf8 = (strpos($var['Value'], 'utf8') !== false);
                    $status = $is_utf8 ? '✅' : '❌';
                    echo '<tr>';
                    echo '<td>' . $var['Variable_name'] . '</td>';
                    echo '<td>' . $var['Value'] . '</td>';
                    echo '<td>' . $status . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
                echo '</div>';

                // Test 3: Sample query with rupee
                echo '<div class="step">';
                echo '<h3>Test 3: Database Query with Rupee Symbol</h3>';
                $stmt = $pdo->query("SELECT '₹' as rupee_test, CONCAT('₹', '999') as price_test");
                $result = $stmt->fetch();
                
                echo '<p>Rupee from query: <span style="font-size: 24px; font-weight: bold;">' . 
                     $result['rupee_test'] . '</span></p>';
                echo '<p>Formatted price: <span style="font-size: 24px; font-weight: bold; color: #007bff;">' . 
                     $result['price_test'] . '</span></p>';
                
                if ($result['rupee_test'] === '₹') {
                    echo '<div class="success">✅ Database is returning rupee symbol correctly!</div>';
                } else {
                    echo '<div class="error">❌ Database is not handling rupee symbol correctly.</div>';
                }
                echo '</div>';

                echo '<a href="?password=' . FIX_PASSWORD . '&action=info" class="btn">Back</a>';
                
            } catch (Exception $e) {
                echo '<div class="error">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            ?>

        <?php
        } elseif ($action === 'view') {
        ?>
            <h2>📦 Current Product Prices</h2>
            
            <?php
            try {
                // Force UTF-8 for this query
                $pdo->exec("SET NAMES utf8mb4");
                
                $stmt = $pdo->query("SELECT id, name, price, sale_price FROM products LIMIT 10");
                $products = $stmt->fetchAll();
                
                echo '<table>';
                echo '<tr>';
                echo '<th>ID</th>';
                echo '<th>Product Name</th>';
                echo '<th>Price (Raw)</th>';
                echo '<th>Sale Price (Raw)</th>';
                echo '<th>Formatted Display</th>';
                echo '</tr>';
                
                foreach ($products as $product) {
                    echo '<tr>';
                    echo '<td>' . $product['id'] . '</td>';
                    echo '<td>' . htmlspecialchars($product['name']) . '</td>';
                    echo '<td>' . $product['price'] . '</td>';
                    echo '<td>' . $product['sale_price'] . '</td>';
                    echo '<td style="font-weight: bold; color: #28a745;">';
                    echo format_price($product['sale_price']);
                    echo '</td>';
                    echo '</tr>';
                }
                
                echo '</table>';
                
                echo '<div class="success">';
                echo '<strong>✅ Check the "Formatted Display" column above.</strong><br>';
                echo 'If you see ₹ followed by numbers (like ₹24,990.00), it\'s working correctly!<br>';
                echo 'If you see garbled text, the encoding fix is needed.';
                echo '</div>';
                
                echo '<a href="?password=' . FIX_PASSWORD . '&action=info" class="btn">Back</a>';
                
            } catch (Exception $e) {
                echo '<div class="error">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            ?>

        <?php
        }
        ?>

        <hr style="margin: 30px 0;">

        <div class="step">
            <h3>📋 Quick Fix Checklist</h3>
            <ol style="line-height: 2;">
                <li>✅ Upload the new <code>config.php</code> file</li>
                <li>✅ Upload the new <code>.htaccess</code> file</li>
                <li>✅ Make sure all PHP files are saved as <strong>UTF-8 without BOM</strong></li>
                <li>✅ Clear browser cache (Ctrl+Shift+Delete)</li>
                <li>✅ Test the website</li>
                <li>⚠️ <strong>DELETE THIS FILE (fix-encoding.php) after testing!</strong></li>
            </ol>
        </div>

        <div class="step">
            <h3>🎯 What Should Work After Fix</h3>
            <div style="background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">
                <p style="font-size: 20px; margin: 10px 0;">
                    Product Price: <strong style="color: #28a745;">₹24,990.00</strong>
                </p>
                <p style="font-size: 20px; margin: 10px 0;">
                    Original: <strong style="color: #6c757d; text-decoration: line-through;">₹29,990.00</strong>
                </p>
                <p style="font-size: 20px; margin: 10px 0;">
                    You Save: <strong style="color: #dc3545;">₹5,000.00 (17% OFF)</strong>
                </p>
            </div>
        </div>

        <div class="error">
            <strong>⚠️ SECURITY WARNING:</strong><br>
            <strong>DELETE THIS FILE immediately after testing!</strong><br>
            This file can expose sensitive information if left on your server.
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="?password=<?php echo FIX_PASSWORD; ?>&action=info" class="btn">Back to Info</a>
            <a href="/" class="btn">Go to Homepage</a>
        </div>
    </div>

    <div style="text-align: center; color: #666; margin-top: 20px; font-size: 12px;">
        TrendsOne Encoding Fix v2.0 | <?php echo date('Y-m-d H:i:s'); ?>
    </div>
</body>
</html>