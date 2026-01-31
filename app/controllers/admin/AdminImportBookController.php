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
            'title' => 'Import Sách từ CSV'
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
            // Kiểm tra file upload
            if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Vui lòng chọn file CSV để upload');
            }

            $file = $_FILES['csv_file'];
            
            // Kiểm tra định dạng file
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($fileExtension !== 'csv') {
                throw new Exception('Chỉ chấp nhận file CSV (.csv)');
            }

            // Kiểm tra kích thước file (tối đa 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                throw new Exception('File quá lớn. Kích thước tối đa 5MB');
            }

            // Đọc file CSV
            $csvData = $this->readCSVFile($file['tmp_name']);

            if (empty($csvData)) {
                throw new Exception('File CSV không có dữ liệu');
            }

            // Import sách
            $result = $this->bookModel->importBooks($csvData);

            $response['success'] = true;
            $response['message'] = 'Import hoàn tất';
            $response['result'] = $result;

        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
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
            
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                
                // Dòng đầu tiên là header
                if ($rowIndex === 0) {
                    $headers = array_map('trim', $row);
                    
                    // Kiểm tra header bắt buộc
                    if (!in_array('title', $headers) || !in_array('author', $headers)) {
                        fclose($handle);
                        throw new Exception('File CSV phải có cột "title" và "author"');
                    }
                    
                    $rowIndex++;
                    continue;
                }

                // Bỏ qua dòng trống
                if (empty(array_filter($row))) {
                    $rowIndex++;
                    continue;
                }

                // Kết hợp header với dữ liệu
                $bookData = [];
                foreach ($headers as $index => $header) {
                    $bookData[$header] = isset($row[$index]) ? trim($row[$index]) : '';
                }

                $booksData[] = $bookData;
                $rowIndex++;
            }

            fclose($handle);
        } else {
            throw new Exception('Không thể đọc file CSV');
        }

        return $booksData;
    }

    /**
     * Tải file CSV mẫu
     */
    public function downloadTemplate()
    {
        $filename = 'book_import_template.csv';
        
        // Tạo header CSV
        $headers = ['title', 'author', 'category_name', 'publisher', 'publish_year', 'description', 'url'];

        // Set headers để download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Tạo output stream
        $output = fopen('php://output', 'w');
        
        // Thêm BOM để Excel đọc UTF-8 đúng
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Ghi header
        fputcsv($output, $headers);

        // Ghi một vài dòng mẫu
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