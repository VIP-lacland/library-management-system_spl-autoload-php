<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/admin_dashboard.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/import-books.css') ?>">
    <title><?= $title ?? 'Import Books' ?></title>
</head>

<body>
    <div class="d-flex" id="wrapper">
        <?php require_once __DIR__ . '/../components/sidebar.php'; ?>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                </div>
            </nav>

            <div class="container-fluid px-4">
                <!-- Header -->
                <div class="section-header mt-4">
                    <div>
                        <h2 class="mb-2">
                            <i class="fas fa-file-import text-info"></i> Import Books from CSV
                        </h2>
                        <p class="text-muted mb-0">Upload a CSV file to add multiple books to the system at once</p>
                    </div>
                    <a href="<?= url('admin.php?action=book-management') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Go Back
                    </a>
                </div>

                <!-- Main Content -->
                <div class="import-container">
                    <!-- Info Box -->
                    <div class="info-box">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle fa-2x text-info me-3"></i>
                            <div>
                                <strong>Instructions:</strong> CSV file must contain the column <code>title</code> and <code>author</code>. 
                                Other columns: <code>category_name</code>, <code>publisher</code>, <code>publish_year</code>, <code>description</code>, <code>url</code>.
                            </div>
                        </div>
                    </div>

                    <!-- Download Template Button -->
                    <div class="text-center mb-4">
                        <a href="<?= url('admin.php?action=import-books-download-template') ?>" class="btn btn-outline-info">
                            <i class="fas fa-download"></i> Download Sample CSV File
                        </a>
                    </div>

                    <!-- Upload Form -->
                    <form id="importForm" enctype="multipart/form-data">
                        <div class="file-upload-area" id="fileUploadArea">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <h4 class="mb-3">Select a CSV file or drag and drop it here</h4>
                            <p class="text-muted mb-0">Only .csv files are allowed (Maximum 5MB)</p>
                            <input type="file" id="csv_file" name="csv_file" accept=".csv" style="display: none;">
                            <p class="text-success mt-3 mb-0 fw-bold" id="fileName" style="display: none;"></p>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5" id="importBtn">
                                <i class="fas fa-upload"></i> Start Import
                            </button>
                        </div>
                    </form>

                    <!-- Loading -->
                    <div class="loading" id="loading">
                        <div class="spinner"></div>
                        <h5 class="text-primary">Processing data...</h5>
                        <p class="text-muted">Please wait a moment, do not close this page</p>
                    </div>

                    <!-- Result Box -->
                    <div class="result-box" id="resultBox">
                        <h4 class="mb-3" id="resultTitle"></h4>
                        
                        <div class="stats" id="stats" style="display: none;">
                            <div class="stat-item success">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <div class="stat-number text-success" id="successCount">0</div>
                                <div class="stat-label">Success</div>
                            </div>
                            <div class="stat-item warning">
                                <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                                <div class="stat-number text-warning" id="skippedCount">0</div>
                                <div class="stat-label">Skip (Duplicate)</div>
                            </div>
                            <div class="stat-item error">
                                <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                                <div class="stat-number text-danger" id="failedCount">0</div>
                                <div class="stat-label">Error</div>
                            </div>
                        </div>

                        <div class="error-list" id="errorList"></div>

                        <div class="text-center mt-4">
                            <button class="btn btn-primary me-2" onclick="location.reload()">
                                <i class="fas fa-redo"></i>Import Another File”
                            </button>
                            <a href="<?= url('admin.php?action=book-management') ?>" class="btn btn-success">
                                <i class="fas fa-list"></i> View Book List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sidebar toggle
        window.addEventListener('DOMContentLoaded', event => {
            const sidebarToggle = document.body.querySelector('#sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', event => {
                    event.preventDefault();
                    document.body.classList.toggle('sb-sidenav-toggled');
                });
            }
        });

        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileInput = document.getElementById('csv_file');
        const fileName = document.getElementById('fileName');
        const importForm = document.getElementById('importForm');
        const loading = document.getElementById('loading');
        const resultBox = document.getElementById('resultBox');

        // Click to select file
        fileUploadArea.addEventListener('click', () => {
            fileInput.click();
        });

        // File selected
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                fileName.innerHTML = '<i class="fas fa-check-circle"></i> Đã chọn: ' + this.files[0].name;
                fileName.style.display = 'block';
                fileUploadArea.classList.add('active');
            }
        });

        // Drag & Drop
        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('active');
        });

        fileUploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('active');
        });

        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('active');
            
            const files = e.dataTransfer.files;
            if (files.length > 0 && files[0].name.endsWith('.csv')) {
                fileInput.files = files;
                fileName.innerHTML = '<i class="fas fa-check-circle"></i> Đã chọn: ' + files[0].name;
                fileName.style.display = 'block';
            } else {
                alert('Please select a CSV file!');
            }
        });

        // Submit form
        importForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!fileInput.files || !fileInput.files[0]) {
                alert('Please select a CSV file!');
                return;
            }

            const formData = new FormData(importForm);

            // Show loading
            importForm.style.display = 'none';
            loading.classList.add('active');
            resultBox.style.display = 'none';

            try {
                const response = await fetch('<?= url('admin.php?action=import-books-process') ?>', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                // Hide loading
                loading.classList.remove('active');

                // Show result
                if (data.success) {
                    showResult(data.result, 'success');
                } else {
                    showResult({ errors: [data.message] }, 'error');
                }

            } catch (error) {
                loading.classList.remove('active');
                showResult({ errors: ['Connection error: ' + error.message] }, 'error');
            }
        });

        // Show result
        function showResult(result, type) {
            resultBox.className = 'result-box ' + type;
            resultBox.style.display = 'block';

            if (type === 'success') {
                document.getElementById('resultTitle').innerHTML = 
                    '<i class="fas fa-check-circle text-success"></i> Import Hoàn Tất!';
                
                document.getElementById('stats').style.display = 'flex';
                document.getElementById('successCount').textContent = result.success || 0;
                document.getElementById('skippedCount').textContent = result.skipped || 0;
                document.getElementById('failedCount').textContent = result.failed || 0;

                // Show errors if any
                const errorList = document.getElementById('errorList');
                if (result.errors && result.errors.length > 0) {
                    errorList.innerHTML = '<h6 class="mt-3 mb-3"><i class="fas fa-exclamation-triangle text-warning"></i> Chi tiết lỗi:</h6>' +
                        result.errors.map(err => `<div class="error-item"><i class="fas fa-exclamation-circle text-danger"></i> ${err}</div>`).join('');
                } else {
                    errorList.innerHTML = '<div class="alert alert-success mt-3"><i class="fas fa-check-circle"></i> Không có lỗi nào!</div>';
                }
            } else {
                document.getElementById('resultTitle').innerHTML = 
                    '<i class="fas fa-exclamation-triangle text-danger"></i> Có Lỗi Xảy Ra!';
                
                document.getElementById('stats').style.display = 'none';
                
                const errorList = document.getElementById('errorList');
                errorList.innerHTML = result.errors.map(err => 
                    `<div class="error-item"><i class="fas fa-times-circle text-danger"></i> ${err}</div>`
                ).join('');
            }

            resultBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    </script>
</body>
</html>