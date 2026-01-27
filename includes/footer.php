<!-- Footer -->
<footer class="footer bg-dark text-white pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row">
            <!-- About -->
            <div class="col-md-3 mb-4">
                <h5 class="mb-3"><i class="fas fa-store"></i> <?php echo SITE_NAME; ?></h5>
                <p class="text-white-50"><?php echo SITE_TAGLINE; ?></p>
                <div class="social-links mt-3">
                    <a href="<?php echo FACEBOOK_URL; ?>" class="text-white me-3"><i
                            class="fab fa-facebook fa-lg"></i></a>
                    <a href="<?php echo INSTAGRAM_URL; ?>" class="text-white me-3"><i
                            class="fab fa-instagram fa-lg"></i></a>
                    <a href="<?php echo TWITTER_URL; ?>" class="text-white me-3"><i
                            class="fab fa-twitter fa-lg"></i></a>
                    <a href="<?php echo YOUTUBE_URL; ?>" class="text-white"><i class="fab fa-youtube fa-lg"></i></a>
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
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <?php echo SITE_ADDRESS; ?>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone me-2"></i>
                        <a href="tel:<?php echo SITE_PHONE; ?>"
                            class="text-white-50 text-decoration-none"><?php echo SITE_PHONE; ?></a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:<?php echo SITE_EMAIL; ?>"
                            class="text-white-50 text-decoration-none"><?php echo SITE_EMAIL; ?></a>
                    </li>
                </ul>

                <!-- Newsletter -->
                <div class="mt-3">
                    <h6>Newsletter</h6>
                    <form action="<?php echo SITE_URL; ?>/api/newsletter-subscribe.php" method="POST"
                        class="newsletter-form">
                        <div class="input-group input-group-sm">
                            <input type="email" class="form-control" name="email" placeholder="Your email" required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <hr class="bg-white-50 my-4">

        <!-- Payment Methods & Copyright -->
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="mb-0 text-white-50">
                    &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <img src="<?php echo SITE_URL; ?>/assets/images/payment-methods.png" alt="Payment Methods" height="30"
                    class="opacity-75">
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="backToTop" class="btn btn-primary btn-floating" style="display: none;">
    <i class="fas fa-arrow-up"></i>
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

</body>

</html>