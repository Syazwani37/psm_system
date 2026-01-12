<?php
/**
 * PSM System - Resource Library
 * Frontend View: Resources Module (Professional)
 */

$page_title = "Resource Library - PSM System";
require_once BASE_PATH . '/backend/includes/auth_check.php';
requireLogin(); // Allows all authenticated users 
require_once BASE_PATH . '/backend/includes/header.php';
?>

<style>
    .page-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 1rem;
        text-align: center;
    }

    .resource-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .resource-card {
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.02);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
        text-align: left;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .resource-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 150, 136, 0.15);
        border-color: #B2DFDB;
    }

    .icon-box {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .res-pdf { background: #FFEBEE; color: #D32F2F; }
    .res-doc { background: #E3F2FD; color: #1976D2; }
    .res-vid { background: #E0F2F1; color: #00695C; }

    .resource-title {
        font-weight: 600;
        color: #263238;
        font-size: 1.15rem;
        line-height: 1.3;
        font-family: 'Playfair Display', serif;
    }

    .resource-desc {
        font-size: 0.95rem;
        color: #78909C;
        line-height: 1.5;
        flex: 1;
    }

    .download-btn {
        width: 100%;
        padding: 0.75rem;
        background: #FDFBF7;
        color: #00796B;
        border: 1px solid #B2DFDB;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .download-btn:hover {
        background: #009688;
        border-color: #009688;
        color: white;
    }
</style>

<!-- Decoration -->
<div class="blob blob-1" style="background: linear-gradient(135deg, #E0F2F1 0%, #B2DFDB 100%); top: -150px; right: -150px; width: 400px; height: 400px; opacity: 0.5;"></div>

<div class="page-container">
    <div class="d-flex align-items-center mb-5">
        <a href="<?php echo getDashboardUrl(); ?>" class="btn btn-outline-secondary me-3 rounded-circle shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; position: relative; z-index: 10;" title="Back to Dashboard">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="mb-0" style="font-family: 'Playfair Display', serif; color: #00695C;">
                <i class="fas fa-book-medical me-2"></i> Resource Library
            </h1>
            <p class="text-muted mb-0">Manage resources for mothers.</p>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php displayFlashMessage(); ?>

    <!-- Professional Actions -->
    <?php if ($_SESSION['role'] === 'professional'): ?>
    <div class="mb-5 d-flex gap-3">
        <button class="btn btn-outline-danger rounded-pill py-3 flex-grow-1" onclick="openUploadModal('pdf')">
            <i class="fas fa-file-pdf fa-lg mb-1 d-block"></i> Upload PDF
        </button>
        <button class="btn btn-outline-primary rounded-pill py-3 flex-grow-1" onclick="openUploadModal('word')">
            <i class="fas fa-file-word fa-lg mb-1 d-block"></i> Upload Document
        </button>
    </div>
    <?php endif; ?>

    <div class="resource-grid">
        <?php
        require_once BASE_PATH . '/backend/config/database.php';
        
        $query = "SELECT * FROM resources ORDER BY created_at DESC";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $fileExt = pathinfo($row['file_path'], PATHINFO_EXTENSION);
                
                // Icon Logic
                $iconClass = 'res-doc';
                $icon = 'fa-file-alt';
                
                if (in_array($fileExt, ['pdf'])) { 
                    $iconClass = 'res-pdf'; 
                    $icon = 'fa-file-pdf'; 
                } elseif (in_array($fileExt, ['mp4', 'avi', 'mov'])) { 
                    $iconClass = 'res-vid'; 
                    $icon = 'fa-video'; 
                } elseif (in_array($fileExt, ['doc', 'docx'])) { 
                    $iconClass = 'res-doc'; 
                    $icon = 'fa-file-word'; 
                }
                ?>
                <div class="resource-card">
                    <div class="icon-box <?php echo $iconClass; ?>"><i class="fas <?php echo $icon; ?>"></i></div>
                    <div class="resource-title"><?php echo escape($row['title']); ?></div>
                    <div class="resource-desc"><?php echo escape($row['description']); ?></div>
                    
                    <a href="<?php echo BASE_URL . $row['file_path']; ?>" class="download-btn mb-2" download>
                        <i class="fas fa-download"></i> Download
                    </a>

                    <!-- Delete Option (Professional Only) -->
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'professional'): ?>
                    <form action="<?php echo BASE_URL; ?>/backend/api/delete_resource.php" method="POST" class="w-100" onsubmit="return confirm('Are you sure you want to delete this resource?');">
                        <input type="hidden" name="resource_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100 rounded-pill border-0" style="background: #FFEBEE; color: #D32F2F;">
                            <i class="fas fa-trash-alt me-1"></i> Delete
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
                <?php
            }
        } else {
            echo '<div class="col-12 text-center text-muted py-5">No resources found. Upload one to get started!</div>';
        }
        ?>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: #00695C;" id="uploadModalTitle">Upload Resource</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="<?php echo BASE_URL; ?>/backend/api/upload_resource.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Title</label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Weekly Nutrition Plan">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Category</label>
                        <select name="category" class="form-select">
                            <option value="expert_article">Expert Article</option>
                            <option value="nutrition_plan">Nutrition Plan</option>
                            <option value="exercise_plan">Exercise Plan</option>
                            <option value="support_resource">Support Resource</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief description..."></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold" id="fileInputLabel">File</label>
                        <input type="file" name="resource_file" id="resourceFileInput" class="form-control" required>
                        <div class="form-text" id="fileHelpText">Max size: 50MB</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill" style="background: #009688; border: none;">
                        Upload File
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openUploadModal(type) {
        const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
        const title = document.getElementById('uploadModalTitle');
        const fileInput = document.getElementById('resourceFileInput');
        const fileLabel = document.getElementById('fileInputLabel');
        const helpText = document.getElementById('fileHelpText');
        
        // Reset settings
        fileInput.value = '';
        
        switch(type) {
            case 'pdf':
                title.innerText = 'Upload PDF Guide';
                fileLabel.innerText = 'Select PDF File';
                fileInput.accept = '.pdf';
                helpText.innerText = 'Allowed: .pdf (Max 50MB)';
                break;
            case 'word':
                title.innerText = 'Upload Document';
                fileLabel.innerText = 'Select Word Document';
                fileInput.accept = '.doc,.docx';
                helpText.innerText = 'Allowed: .doc, .docx (Max 50MB)';
                break;
        }
        
        modal.show();
    }
</script>

<?php require_once BASE_PATH . '/backend/includes/footer.php'; ?>


