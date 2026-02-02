<?php
class AdminImportBookController extends Controller
{
    private $bookModel;

    public function __construct()
    {
        $this->bookModel = $this->model('Book');
    }

    /**
     * Hiển thị trang import sách
     */
    public function importBooks()
    {
        $data = [
            'title' => 'Import Books from CSV'
        ];
        
        $this->view('admin/books/import-books', $data);
    }

    /**
     * Xử lý import file CSV
     */
    public function process()
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $response = [
            'success' => false,
            'message' => '',
            'result' => []
        ];

        try {
            if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Please select a CSV file to upload');
            }

            $file = $_FILES['csv_file'];
            
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($fileExtension !== 'csv') {
                throw new Exception('Only CSV files are allowed (.csv)');
            }

            if ($file['size'] > 5 * 1024 * 1024) {
                throw new Exception('File is too large. Maximum size is 5MB');
            }

            // Clean encoding trước khi đọc
            $this->cleanCSVEncoding($file['tmp_name']);

            // Đọc file CSV
            $csvData = $this->readCSVFile($file['tmp_name']);

            if (empty($csvData)) {
                throw new Exception('CSV file is empty');
            }

            $result = $this->bookModel->importBooks($csvData);

            $response['success'] = true;
            $response['message'] = 'Import completed';
            $response['result'] = $result;

        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Clean BOM và convert encoding về UTF-8
     * Xử lý các ký tự đặc biệt như smart quotes
     */
    private function cleanCSVEncoding($filePath)
    {
        $content = file_get_contents($filePath);

        // Xóa BOM nếu có
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        // Nếu file là Windows-1252 (có smart quotes), convert sang UTF-8
        if (!mb_detect_encoding($content, 'UTF-8', true)) {
            $content = mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
        }

        // Replace các smart quotes còn lại về ký tự thường
        $content = str_replace([
            "\xe2\x80\x99", // ' (right single quote UTF-8)
            "\xe2\x80\x98", // ' (left single quote UTF-8)
            "\xe2\x80\x9c", // " (left double quote UTF-8)
            "\xe2\x80\x9d", // " (right double quote UTF-8)
            "\xe2\x80\x94", // — (em dash UTF-8)
            "\xe2\x80\x93", // – (en dash UTF-8)
        ], [
            "'", "'", '"', '"', '-', '-'
        ], $content);

        file_put_contents($filePath, $content);
    }

    /**
     * Đọc file CSV và chuyển thành mảng
     */
    private function readCSVFile($filePath)
    {
        $booksData = [];
        $headers = [];
        $rowIndex = 0;

        if (($handle = fopen($filePath, 'r')) !== false) {
            
            while (($row = fgetcsv($handle, 0, ',', '"')) !== false) {
                
                if ($rowIndex === 0) {
                    // Clean BOM từ header đầu tiên nếu còn
                    $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]);
                    $headers = array_map('trim', $row);
                    
                    if (!in_array('title', $headers) || !in_array('author', $headers)) {
                        fclose($handle);
                        throw new Exception('CSV file must contain the columns "title" and "author"');
                    }
                    
                    $rowIndex++;
                    continue;
                }

                if (empty(array_filter($row))) {
                    $rowIndex++;
                    continue;
                }

                $bookData = [];
                foreach ($headers as $index => $header) {
                    $bookData[$header] = isset($row[$index]) ? trim($row[$index]) : '';
                }

                $booksData[] = $bookData;
                $rowIndex++;
            }

            fclose($handle);
        } else {
            throw new Exception('Unable to read CSV file');
        }

        return $booksData;
    }

    /**
     * Tải file CSV mẫu
     */
    public function downloadTemplate()
    {
        $filename = 'book_import_template.csv';
        
        $headers = ['title', 'author', 'category_name', 'publisher', 'publish_year', 'description', 'url'];

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        
        // BOM
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, $headers);

        fputcsv($output, [
            'Đắc Nhân Tâm',
            'Dale Carnegie',
            'Kỹ năng sống',
            'NXB Tổng Hợp',
            '2020',
            'Sách về nghệ thuật giao tiếp và ứng xử',
            'https://example.com/image.jpg'
        ]);

        fclose($output);
        exit;
    }
}
?>