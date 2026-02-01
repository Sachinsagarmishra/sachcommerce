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

</body>

</html>