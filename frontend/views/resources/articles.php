<?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php';
/**
 * PSM System - Expert Articles
 * Frontend View: Resources Module
 */

$page_title = "Expert Articles & Tips - PSM System";
require_once BASE_PATH . '/backend/includes/auth_check.php';

requireLogin(); // Accessible to all logged in users (Mother/Professional/Admin)
require_once BASE_PATH . '/backend/includes/header.php';
?>

<style>
    .page-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 1rem;
        text-align: center;
    }

    .articles-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .article-card {
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.02);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
        text-align: left;
        cursor: pointer;
        display: flex;
        align-items: flex-start;
        gap: 1.5rem;
    }

    .article-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(107, 142, 130, 0.15);
    }

    .article-icon {
        min-width: 60px;
        height: 60px;
        background: #F8F8F8;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #8DA399;
    }

    .article-content { flex: 1; }

    .article-card h3 {
        font-size: 1.25rem;
        color: #2C2C2C;
        margin-bottom: 0.5rem;
        font-weight: 600;
        font-family: 'Playfair Display', serif;
    }

    .article-card p {
        font-size: 0.95rem;
        color: #788580;
        line-height: 1.6;
        margin-bottom: 0;
    }

    .read-more {
        margin-top: 1rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #8DA399;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>

<!-- Decoration -->
<div class="blob blob-2" style="top: -100px; right: -100px; opacity: 0.4;"></div>

<div class="page-container">
    <div class="d-flex align-items-center mb-5">
        <a href="<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo getDashboardUrl(); ?>" class="btn btn-outline-secondary me-3 rounded-circle shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; position: relative; z-index: 10;" title="Back to Dashboard">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="mb-0" style="font-family: 'Playfair Display', serif; color: #4A5D53;">
                <i class="fas fa-book-reader me-2" style="color: #B4C5BD;"></i> Expert Insights & Community
            </h1>
            <p class="text-muted mb-0">Curated articles and local resources for you.</p>
        </div>
    </div>

    <div class="articles-grid">
        <!-- Dynamic Uploaded Resources -->
        <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php';
        // Fetch resources (Exclude videos)
        $resQuery = "SELECT * FROM resources WHERE file_path NOT LIKE '%.mp4' AND file_path NOT LIKE '%.avi' AND file_path NOT LIKE '%.mov' ORDER BY created_at DESC";
        $resResult = mysqli_query($conn, $resQuery);

        if ($resResult && mysqli_num_rows($resResult) > 0) {
            while ($row = mysqli_fetch_assoc($resResult)) {
                $fileExt = pathinfo($row['file_path'], PATHINFO_EXTENSION);
                $icon = 'fa-file-alt'; // Default doc
                if (in_array($fileExt, ['pdf'])) { $icon = 'fa-file-pdf'; }
                
                // Construct download link
                $link = BASE_URL . $row['file_path'];
                ?>
                <div class="article-card" onclick="window.open('<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo $link; ?>', '_blank')">
                    <div class="article-icon"><i class="fas <?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo $icon; ?>"></i></div>
                    <div class="article-content">
                        <h3><?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo escape($row['title']); ?></h3>
                        <p><?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; echo escape($row['description']); ?></p>
                        <div class="read-more">View Resource <i class="fas fa-external-link-alt"></i></div>
                    </div>
                </div>
                <?php
require_once dirname(__FILE__, 3) . '/backend/config/database.php';
            }
        }
        ?>

        <!-- Article Card 1 -->
        <div class="article-card" onclick="openExternalLink('https://www.nhs.uk/mental-health/conditions/post-natal-depression/symptoms/')">
            <div class="article-icon"><i class="fas fa-brain"></i></div>
            <div class="article-content">
                <h3>Mental Wellness Guide</h3>
                <p>NHS overview of postpartum depression symptoms, from low mood to difficulty bonding.</p>
                <div class="read-more">Read on NHS UK <i class="fas fa-external-link-alt"></i></div>
            </div>
        </div>

        <!-- Article Card 2 -->
        <div class="article-card" onclick="openExternalLink('https://www.healthline.com/health/postpartum-recovery-timeline')">
            <div class="article-icon"><i class="fas fa-clock"></i></div>
            <div class="article-content">
                <h3>Postpartum Recovery Timeline</h3>
                <p>Healthline's guide on what to expect physically and emotionally in the weeks after birth.</p>
                <div class="read-more">Read on Healthline <i class="fas fa-external-link-alt"></i></div>
            </div>
        </div>

        <!-- Article Card 3: Bonding -->
        <div class="article-card" onclick="openExternalLink('https://raisingchildren.net.au/newborns/connecting-communicating/bonding/bonding-newborns')">
            <div class="article-icon"><i class="fas fa-heart"></i></div>
            <div class="article-content">
                <h3>Bonding with Your Newborn</h3>
                <p>Practical tips on building a strong, loving connection through touch, talk, and care.</p>
                <div class="read-more">Read on Raising Children <i class="fas fa-external-link-alt"></i></div>
            </div>
        </div>

        <!-- IBU Family Resource Group -->
        <div class="article-card" onclick="openExternalLink('https://www.ibufamily.org/')">
            <div class="article-icon" style="background: #E8F5E9; color: #2E7D32;"><i class="fas fa-hands-helping"></i></div>
            <div class="article-content">
                <h3>IBU Family Resource Group</h3>
                <p>A Malaysian volunteer organization providing support for pregnancy, birth, and parenting since 1989.</p>
                <div class="read-more">Visit IBU Family <i class="fas fa-external-link-alt"></i></div>
            </div>
        </div>

        <!-- TheAsianParent -->
        <div class="article-card" onclick="openExternalLink('https://my.theasianparent.com/')">
            <div class="article-icon" style="background: #FFF3E0; color: #EF6C00;"><i class="fas fa-users"></i></div>
            <div class="article-content">
                <h3>The Asian Parent</h3>
                <p>Asia's largest specialized community for parents with local advice and confinement tips.</p>
                <div class="read-more">Visit Community <i class="fas fa-external-link-alt"></i></div>
            </div>
        </div>
    </div>
</div>

<script>
    function openExternalLink(url) {
        window.open(url, '_blank');
    }
</script>

<?php
require_once dirname(__DIR__, 3) . '/backend/config/database.php'; require_once BASE_PATH . '/backend/includes/footer.php'; ?>


