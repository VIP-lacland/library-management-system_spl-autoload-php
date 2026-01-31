<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/admin_dashboard.css') ?>">
    <title><?= $title ?? 'Import Books' ?></title>
    <style>
        .import-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 20px;
        }
        .file-upload-area {
            border: 3px dashed #dee2e6;
            border-radius: 10px;
            padding: 60px 20px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s;
            cursor: pointer;
            margin: 20px 0;
        }
        .file-upload-area:hover {
            border-color: #0dcaf0;
            background: #e7f6fd;
        }
        .file-upload-area.active {
            border-color: #0dcaf0;
            background: #d1ecf1;
        }
        .file-upload-area i {
            font-size: 60px;
            color: #0dcaf0;
            margin-bottom: 20px;
        }
        .result-box {
            margin-top: 30px;
            padding: 25px;
            border-radius: 10px;
            display: none;
        }
        .result-box.success {
            background: #d4edda;
            border: 2px solid #c3e6cb;
        }
        .result-box.error {
            background: #f8d7da;
            border: 2px solid #f5c6cb;
        }
        .stats {
            display: flex;
            gap: 15px;
            margin: 25px 0;
        }
        .stat-item {
            flex: 1;
            text-align: center;
            padding: 25px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-item.success {
            border-left: 5px solid #28a745;
        }
        .stat-item.warning {
            border-left: 5px solid #ffc107;
        }
        .stat-item.error {
            border-left: 5px solid #dc3545;
        }
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #6c757d;
            font-size: 14px;
        }
        .error-list {
            max-height: 350px;
            overflow-y: auto;
            margin-top: 20px;
        }
        .error-item {
            padding: 12px 15px;
            background: white;
            margin: 8px 0;
            border-radius: 6px;
            border-left: 4px solid #dc3545;
            font-size: 14px;
        }
        .loading {
            display: none;
            text-align: center;
            padding: 50px;
        }
        .loading.active {
            display: block;
        }
        .spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #0dcaf0;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #0dcaf0;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
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
                            <i class="fas fa-file-import text-info"></i> Import Sách từ CSV
                        </h2>
                        <p class="text-muted mb-0">Upload file CSV để thêm nhiều sách cùng lúc vào hệ thống</p>
                    </div>
                    <a href="<?= url('admin.php?action=book-management') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay Lại
                    </a>
                </div>

                <!-- Main Content -->
                <div class="import-container">
                    <!-- Info Box -->
                    <div class="info-box">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle fa-2x text-info me-3"></i>
                            <div>
                                <strong>Hướng dẫn:</strong> File CSV phải có cột <code>title</code> và <code>author</code>. 
                                Các cột khác: <code>category_name</code>, <code>publisher</code>, <code>publish_year</code>, <code>description</code>, <code>url</code>.
                            </div>
                        </div>
                    </div>

                    <!-- Download Template Button -->
                    <div class="text-center mb-4">
                        <a href="<?= url('admin.php?action=import-books-download-template') ?>" class="btn btn-outline-info">
                            <i class="fas fa-download"></i> Tải File CSV Mẫu
                        </a>
                    </div>

                    <!-- Upload Form -->
                    <form id="importForm" enctype="multipart/form-data">
                        <div class="file-upload-area" id="fileUploadArea">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <h4 class="mb-3">Chọn file CSV hoặc kéo thả vào đây</h4>
                            <p class="text-muted mb-0">Chỉ chấp nhận file .csv (Tối đa 5MB)</p>
                            <input type="file" id="csv_file" name="csv_file" accept=".csv" style="display: none;">
                            <p class="text-success mt-3 mb-0 fw-bold" id="fileName" style="display: none;"></p>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5" id="importBtn">
                                <i class="fas fa-upload"></i> Bắt Đầu Import
                            </button>
                        </div>
                    </form>

                    <!-- Loading -->
                    <div class="loading" id="loading">
                        <div class="spinner"></div>
                        <h5 class="text-primary">Đang xử lý dữ liệu...</h5>
                        <p class="text-muted">Vui lòng đợi trong giây lát, đừng tắt trang này</p>
                    </div>

                    <!-- Result Box -->
                    <div class="result-box" id="resultBox">
                        <h4 class="mb-3" id="resultTitle"></h4>
                        
                        <div class="stats" id="stats" style="display: none;">
                            <div class="stat-item success">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <div class="stat-number text-success" id="successCount">0</div>
                                <div class="stat-label">Thành Công</div>
                            </div>
                            <div class="stat-item warning">
                                <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                                <div class="stat-number text-warning" id="skippedCount">0</div>
                                <div class="stat-label">Bỏ Qua (Trùng)</div>
                            </div>
                            <div class="stat-item error">
                                <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                                <div class="stat-number text-danger" id="failedCount">0</div>
                                <div class="stat-label">Lỗi</div>
                            </div>
                        </div>

                        <div class="error-list" id="errorList"></div>

                        <div class="text-center mt-4">
                            <button class="btn btn-primary me-2" onclick="location.reload()">
                                <i class="fas fa-redo"></i> Import File Khác
                            </button>
                            <a href="<?= url('admin.php?action=book-management') ?>" class="btn btn-success">
                                <i class="fas fa-list"></i> Xem Danh Sách Sách
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
                alert('Vui lòng chọn file CSV!');
            }
        });

        // Submit form
        importForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!fileInput.files || !fileInput.files[0]) {
                alert('Vui lòng chọn file CSV!');
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
                showResult({ errors: ['Lỗi kết nối: ' + error.message] }, 'error');
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