<?php
// Get site settings (already loaded in header, but ensure they exist)
if (!isset($site_settings)) {
    $site_settings = [
        'site_name' => get_site_setting('site_name', SITE_NAME ?? 'TrendsOne'),
        'site_tagline' => get_site_setting('site_tagline', SITE_TAGLINE ?? ''),
        'site_logo' => get_site_setting('site_logo', ''),
        'support_email' => get_site_setting('support_email', SITE_EMAIL ?? ''),
        'support_phone' => get_site_setting('support_phone', SITE_PHONE ?? ''),
        'whatsapp_number' => get_site_setting('whatsapp_number', ''),
        'site_address' => get_site_setting('site_address', SITE_ADDRESS ?? ''),
        'facebook_url' => get_site_setting('facebook_url', FACEBOOK_URL ?? ''),
        'instagram_url' => get_site_setting('instagram_url', INSTAGRAM_URL ?? ''),
        'twitter_url' => get_site_setting('twitter_url', TWITTER_URL ?? ''),
        'youtube_url' => get_site_setting('youtube_url', YOUTUBE_URL ?? ''),
        'linkedin_url' => get_site_setting('linkedin_url', ''),
        'pinterest_url' => get_site_setting('pinterest_url', ''),
        'custom_footer_scripts' => get_site_setting('custom_footer_scripts', ''),
    ];
}
?>
<!-- Footer -->
<footer class="footer bg-dark text-white pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row">
            <!-- About -->
            <div class="col-md-3 mb-4">
                <?php if ($site_settings['site_logo']): ?>
                    <a href="<?php echo SITE_URL; ?>">
                        <img src="<?php echo SITE_URL; ?>/uploads/logos/<?php echo $site_settings['site_logo']; ?>"
                            alt="<?php echo htmlspecialchars($site_settings['site_name']); ?>"
                            style="max-height: 50px; margin-bottom: 15px; filter: brightness(0) invert(1);">
                    </a>
                <?php else: ?>
                    <h5 class="mb-3"><?php echo htmlspecialchars($site_settings['site_name']); ?></h5>
                <?php endif; ?>

                <?php if ($site_settings['site_tagline']): ?>
                    <p class="text-white-50"><?php echo htmlspecialchars($site_settings['site_tagline']); ?></p>
                <?php endif; ?>

                <div class="social-links mt-3">
                    <?php if ($site_settings['facebook_url']): ?>
                        <a href="<?php echo htmlspecialchars($site_settings['facebook_url']); ?>" class="text-white me-3"
                            target="_blank" rel="noopener">
                            <i class="fab fa-facebook-f fa-lg"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ($site_settings['instagram_url']): ?>
                        <a href="<?php echo htmlspecialchars($site_settings['instagram_url']); ?>" class="text-white me-3"
                            target="_blank" rel="noopener">
                            <i class="fab fa-instagram fa-lg"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ($site_settings['twitter_url']): ?>
                        <a href="<?php echo htmlspecialchars($site_settings['twitter_url']); ?>" class="text-white me-3"
                            target="_blank" rel="noopener">
                            <i class="fab fa-twitter fa-lg"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ($site_settings['youtube_url']): ?>
                        <a href="<?php echo htmlspecialchars($site_settings['youtube_url']); ?>" class="text-white me-3"
                            target="_blank" rel="noopener">
                            <i class="fab fa-youtube fa-lg"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ($site_settings['linkedin_url']): ?>
                        <a href="<?php echo htmlspecialchars($site_settings['linkedin_url']); ?>" class="text-white me-3"
                            target="_blank" rel="noopener">
                            <i class="fab fa-linkedin-in fa-lg"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ($site_settings['pinterest_url']): ?>
                        <a href="<?php echo htmlspecialchars($site_settings['pinterest_url']); ?>" class="text-white"
                            target="_blank" rel="noopener">
                            <i class="fab fa-pinterest-p fa-lg"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-md-3 mb-4">
                <h5 class="mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?php echo SITE_URL; ?>/about"
                            class="text-white-50 text-decoration-none">About Us</a></li>
                    <li class="mb-2"><a href="<?php echo SITE_URL; ?>/shop"
                            class="text-white-50 text-decoration-none">Shop</a></li>
                    <li class="mb-2"><a href="<?php echo SITE_URL; ?>/contact"
                            class="text-white-50 text-decoration-none">Contact Us</a></li>
                    <li class="mb-2"><a href="<?php echo SITE_URL; ?>/faq"
                            class="text-white-50 text-decoration-none">FAQ</a></li>
                </ul>
            </div>

            <!-- Customer Service -->
            <div class="col-md-3 mb-4">
                <h5 class="mb-3">Customer Service</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?php echo SITE_URL; ?>/my-account"
                            class="text-white-50 text-decoration-none">My Account</a></li>
                    <li class="mb-2"><a href="<?php echo SITE_URL; ?>/orders"
                            class="text-white-50 text-decoration-none">Track Order</a></li>
                    <li class="mb-2"><a href="<?php echo SITE_URL; ?>/return-policy"
                            class="text-white-50 text-decoration-none">Return Policy</a></li>
                    <li class="mb-2"><a href="<?php echo SITE_URL; ?>/shipping-policy"
                            class="text-white-50 text-decoration-none">Shipping Policy</a></li>
                    <li class="mb-2"><a href="<?php echo SITE_URL; ?>/terms-conditions"
                            class="text-white-50 text-decoration-none">Terms & Conditions</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-md-3 mb-4">
                <h5 class="mb-3">Contact Us</h5>
                <ul class="list-unstyled text-white-50">
                    <?php if ($site_settings['site_address']): ?>
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <?php echo nl2br(htmlspecialchars($site_settings['site_address'])); ?>
                        </li>
                    <?php endif; ?>
                    <?php if ($site_settings['support_phone']): ?>
                        <li class="mb-2">
                            <i class="fas fa-phone-alt me-2"></i>
                            <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $site_settings['support_phone']); ?>"
                                class="text-white-50 text-decoration-none"><?php echo htmlspecialchars($site_settings['support_phone']); ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if ($site_settings['support_email']): ?>
                        <li class="mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            <a href="mailto:<?php echo htmlspecialchars($site_settings['support_email']); ?>"
                                class="text-white-50 text-decoration-none"><?php echo htmlspecialchars($site_settings['support_email']); ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if ($site_settings['whatsapp_number']): ?>
                        <li class="mb-2">
                            <i class="fab fa-whatsapp me-2"></i>
                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $site_settings['whatsapp_number']); ?>"
                                class="text-white-50 text-decoration-none" target="_blank">WhatsApp Chat</a>
                        </li>
                    <?php endif; ?>
                </ul>

            </div>
        </div>

        <hr class="bg-white-50 my-4">

        <!-- Payment Methods & Copyright -->
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="mb-0 text-white-50">
                    &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($site_settings['site_name']); ?>. All
                    rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <img src="<?php echo SITE_URL; ?>/uploads/img/payments.webp" alt="Payment Methods" height="15"
                    class="opacity-105">
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="backToTop" class="btn btn-primary btn-floating" style="display: none;">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Custom JS -->
<script src="<?php echo SITE_URL; ?>/assets/js/main.js?v=<?php echo time(); ?>"></script>

<?php if (isset($extra_js)): ?>
    <?php foreach ($extra_js as $js): ?>
        <script src="<?php echo $js; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Custom Footer Scripts from Admin -->
<?php if (!empty($site_settings['custom_footer_scripts'])): ?>
    <?php echo $site_settings['custom_footer_scripts']; ?>
<?php endif; ?>

<!-- Fake Purchase Notifications -->
<?php
$fake_enabled = get_site_setting('fake_notif_enabled', '0');
$fake_products_ids = get_site_setting('fake_notif_products', '');
$fake_interval = get_site_setting('fake_notif_interval', '10');
$fake_duration = get_site_setting('fake_notif_duration', '5');

if ($fake_enabled === '1' && !empty($fake_products_ids)):
    $ids = explode(',', $fake_products_ids);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $stmt = $pdo->prepare("SELECT id, name, slug, 
                         (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
                         FROM products p WHERE id IN ($placeholders) AND status = 'active'");
    $stmt->execute($ids);
    $fake_products_list = $stmt->fetchAll();

    if (!empty($fake_products_list)):
        ?>
        <div id="fakeNotification" class="fake-notification">
            <div class="fake-notif-content">
                <div class="fake-notif-image">
                    <img id="fakeNotifImg" src="" alt="Product">
                    <span class="fake-notif-badge"><i class="fas fa-check"></i></span>
                </div>
                <div class="fake-notif-details">
                    <p class="fake-notif-title">Someone recently bought</p>
                    <a id="fakeNotifLink" href="#" class="fake-notif-product-name text-truncate d-block"></a>
                    <p class="fake-notif-location">in <span id="fakeNotifLoc"></span></p>
                    <div class="fake-notif-meta">
                        <span class="fake-notif-verified text-success">Verified</span>
                        <span id="fakeNotifTime" class="fake-notif-time text-muted ms-2">just now</span>
                    </div>
                </div>
                <button type="button" class="fake-notif-close" onclick="closeFakeNotif()">&times;</button>
            </div>
            <div class="fake-notif-progress">
                <div id="fakeNotifProgress"></div>
            </div>
        </div>

        <style>
        .fake-notification {
            position: fixed;
            bottom: -150px;
            left: 20px;
            width: 330px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            z-index: 9999;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            overflow: hidden;
            border: 1px solid #eee;
        }
        .fake-notification.show {
            bottom: 20px;
        }
        .fake-notif-content {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            gap: 12px;
            position: relative;
        }
        .fake-notif-image {
            width: 55px;
            height: 55px;
            flex-shrink: 0;
            position: relative;
        }
        .fake-notif-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }
        .fake-notif-badge {
            position: absolute;
            top: -5px;
            left: -5px;
            background: #28a745;
            color: #fff;
            width: 16px;
            height: 16px;
            font-size: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 2px solid #fff;
        }
        .fake-notif-details {
            flex-grow: 1;
            min-width: 0;
        }
        .fake-notif-title {
            margin: 0;
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1;
        }
        .fake-notif-product-name {
            margin: 2px 0;
            font-weight: 700;
            color: #333;
            text-decoration: none;
            font-size: 13px;
            line-height: 1.3;
        }
        .fake-notif-product-name:hover {
            color: var(--primary-color, #007bff);
        }
        .fake-notif-location {
            margin: 0;
            font-size: 11px;
            color: #444;
            line-height: 1.2;
        }
        .fake-notif-location strong {
            color: #000;
        }
        .fake-notif-meta {
            margin-top: 2px;
            display: flex;
            align-items: center;
            line-height: 1;
        }
        .fake-notif-verified {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .fake-notif-time {
            font-size: 9px;
        }
        .fake-notif-close {
            position: absolute;
            top: 5px;
            right: 8px;
            background: none;
            border: none;
            font-size: 18px;
            color: #ccc;
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }
        .fake-notif-close:hover {
            color: #333;
        }
        .fake-notif-progress {
            height: 2px;
            width: 100%;
            background: #f0f0f0;
        }
        #fakeNotifProgress {
            height: 100%;
            width: 0%;
            background: var(--primary-color, #007bff);
        }

        @media (max-width: 576px) {
            .fake-notification {
                width: 280px;
                left: 15px;
                padding: 0;
            }
            .fake-notif-content {
                padding: 10px;
                gap: 10px;
            }
            .fake-notif-image {
                width: 45px;
                height: 45px;
            }
            .fake-notif-product-name {
                font-size: 12px;
            }
            .fake-notif-location {
                font-size: 10px;
            }
            .fake-notification.show {
                bottom: 15px;
            }
        }
    </style>

        <script>
            const fakeProducts = <?php echo json_encode($fake_products_list); ?>;
            const fakeCities = ["Mumbai, Maharashtra", "Delhi, NCR", "Bengaluru, Karnataka", "Hyderabad, Telangana", "Ahmedabad, Gujarat", "Chennai, Tamil Nadu", "Kolkata, West Bengal", "Surat, Gujarat", "Pune, Maharashtra", "Jaipur, Rajasthan", "Lucknow, Uttar Pradesh", "Kanpur, Uttar Pradesh", "Nagpur, Maharashtra", "Indore, Madhya Pradesh", "Thane, Maharashtra", "Bhopal, Madhya Pradesh", "Visakhapatnam, Andhra Pradesh", "Pimpri-Chinchwad, Maharashtra", "Patna, Bihar", "Vadodara, Gujarat", "Ghaziabad, Uttar Pradesh", "Ludhiana, Punjab", "Agra, Uttar Pradesh", "Nashik, Maharashtra", "Faridabad, Haryana", "Meerut, Uttar Pradesh", "Rajkot, Gujarat", "Kalyan-Dombivli, Maharashtra", "Vasai-Virar, Maharashtra", "Varanasi, Uttar Pradesh"];
            const fakeInterval = <?php echo (int) $fake_interval * 1000; ?>;
            const fakeDuration = <?php echo (int) $fake_duration * 1000; ?>;
            let fakeNotifTimer;

            function showFakeNotif() {
                if (fakeProducts.length === 0) return;

                const product = fakeProducts[Math.floor(Math.random() * fakeProducts.length)];
                const city = fakeCities[Math.floor(Math.random() * fakeCities.length)];
                const timeAgo = (Math.floor(Math.random() * 20) + 1) + " minutes ago";

                document.getElementById('fakeNotifImg').src = product.image ? '<?php echo PRODUCT_IMAGE_URL; ?>' + product.image : 'https://via.placeholder.com/60';
                document.getElementById('fakeNotifLink').href = '<?php echo SITE_URL; ?>/products/' + product.slug;
                document.getElementById('fakeNotifLink').innerText = product.name;
                document.getElementById('fakeNotifLoc').innerHTML = '<strong>' + city + '</strong>';
                document.getElementById('fakeNotifTime').innerText = Math.random() > 0.5 ? 'just now' : timeAgo;

                const notif = document.getElementById('fakeNotification');
                const progress = document.getElementById('fakeNotifProgress');

                notif.classList.add('show');

                // Progress Bar Animation
                progress.style.transition = 'none';
                progress.style.width = '0%';
                setTimeout(() => {
                    progress.style.transition = `width ${fakeDuration}ms linear`;
                    progress.style.width = '100%';
                }, 50);

                setTimeout(() => {
                    notif.classList.remove('show');
                }, fakeDuration);
            }

            function closeFakeNotif() {
                document.getElementById('fakeNotification').classList.remove('show');
            }

            function startFakeNotifLoop() {
                // Initial delay
                setTimeout(() => {
                    showFakeNotif();
                    setInterval(showFakeNotif, fakeInterval + fakeDuration);
                }, 5000); // Start after 5 seconds on page load
            }

            document.addEventListener('DOMContentLoaded', startFakeNotifLoop);
        </script>
    <?php endif; ?>
<?php endif; ?>

</body>

</html>