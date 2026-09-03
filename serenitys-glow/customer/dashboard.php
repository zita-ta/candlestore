<?php
require_once '../includes/auth_check.php';
requireRole('customer');
require_once '../config/db.php';

// Produk featured buat slideshow, sama kayak beranda
$stmtFeatured = $conn->query("SELECT * FROM products WHERE is_featured = 1 ORDER BY id ASC");
$featuredProducts = $stmtFeatured->fetchAll(PDO::FETCH_ASSOC);
$slideCount = count($featuredProducts);
$secondsPerSlide = 4;
$totalDuration = $slideCount > 0 ? $slideCount * $secondsPerSlide : $secondsPerSlide;

// Produk best seller (paling banyak terjual dari pesanan yang sudah selesai)
$stmtAll = $conn->query("
    SELECT p.*, COALESCE(SUM(oi.quantity), 0) AS total_terjual
    FROM products p
    LEFT JOIN order_items oi ON oi.product_id = p.id
    LEFT JOIN orders o ON o.id = oi.order_id AND o.status = 'selesai'
    GROUP BY p.id
    ORDER BY total_terjual DESC, p.id DESC
    LIMIT 4
");
$allProducts = $stmtAll->fetchAll(PDO::FETCH_ASSOC);

// Testimoni terbaru, sama kayak beranda
$stmtReviews = $conn->query("SELECT * FROM reviews ORDER BY created_at DESC LIMIT 4");
$reviews = $stmtReviews->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Dashboard - Serenity's Glow";
require_once '../includes/customer_nav.php';
?>

    <!-- ================= HERO + SLIDESHOW ================= -->
    <section class="hero">
        <h1>Halo, <?= htmlspecialchars($_SESSION['name']) ?> — Nyalakan Ketenangan Hari Ini</h1>
        <p>Lilin aromaterapi handmade untuk menemani waktu istirahatmu</p>

        <?php if ($slideCount > 0): ?>
        <div class="slideshow">
            <?php foreach ($featuredProducts as $i => $p):
                $delay = -($i * $secondsPerSlide);
                $imgSrc = $p['image'] ? '../assets/img/' . htmlspecialchars($p['image']) : 'https://placehold.co/900x380/a8c9a1/ffffff?text=' . urlencode($p['name']);
            ?>
            <div class="slide" style="animation-duration: <?= $totalDuration ?>s; animation-delay: <?= $delay ?>s;">
                <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                <div class="slide-caption">
                    <h3><?= htmlspecialchars($p['name']) ?></h3>
                    <span>Rp <?= number_format($p['price'], 0, ',', '.') ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>

    <!-- ================= SEKILAS KATALOG ================= -->
    <section class="section" id="katalog">
        <div class="section-title">
            <h2>Best Seller</h2>
            <p>4 produk paling laris pilihan pelanggan kami</p>
        </div>

        <div class="product-grid">
            <?php if (count($allProducts) === 0): ?>
                <p style="text-align:center; color:#999;">Belum ada produk.</p>
            <?php endif; ?>

            <?php foreach ($allProducts as $p):
                $imgSrc = $p['image'] ? '../assets/img/' . htmlspecialchars($p['image']) : 'https://placehold.co/300x180/f3c9d4/ffffff?text=' . urlencode($p['name']);
            ?>
            <a href="product_detail.php?id=<?= $p['id'] ?>" class="product-card">
                <div class="product-img" style="background-image: url('<?= $imgSrc ?>');"></div>
                <div class="product-info">
                    <h4><?= htmlspecialchars($p['name']) ?></h4>
                    <p class="desc"><?= htmlspecialchars($p['description']) ?></p>
                    <div class="price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                    <span class="stock-badge">
                        <?= $p['stock'] > 0 ? 'Stok: ' . $p['stock'] : 'Habis' ?>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <div style="text-align:center; margin-top:30px;">
            <a href="katalog.php" class="btn-add" style="display:inline-block;">Lihat Semua Produk</a>
        </div>
    </section>

    <!-- ================= TESTIMONI ================= -->
    <?php if (count($reviews) > 0): ?>
    <section class="section" id="testimoni">
        <div class="section-title">
            <h2>Kata Mereka</h2>
            <p>Pengalaman belanja customer Serenity's Glow</p>
        </div>

        <div class="testimonial-grid">
            <?php foreach ($reviews as $r): ?>
            <div class="testimonial-card">
                <p class="testimonial-name"><?= htmlspecialchars($r['customer_name']) ?></p>
                <div class="star-rating-display">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star <?= $i <= $r['rating'] ? 'filled' : '' ?>">★</span>
                    <?php endfor; ?>
                </div>
                <p class="testimonial-comment">"<?= nl2br(htmlspecialchars($r['comment'])) ?>"</p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ================= CONTACT ================= -->
    <section class="section" id="kontak" style="background-color:#fbf6f3;">
        <div class="section-title">
            <h2>Hubungi Kami</h2>
            <p>Ada pertanyaan seputar produk? Hubungi kami lewat kontak berikut</p>
        </div>

        <div class="contact-wrapper">
            <div class="contact-item"><strong>Alamat:</strong> Magetan, Jawa Timur, Indonesia</div>
            <div class="contact-item"><strong>WhatsApp:</strong> 0896-7205-633</div>
            <div class="contact-item"><strong>Email:</strong>serenitysglow@email.com</div>
            <div class="contact-item"><strong>Instagram:</strong> @serenitysglow</div>
        </div>
    </section>

</body>
</html>
