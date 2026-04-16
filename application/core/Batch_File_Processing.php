<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Batch File Processing Trait
 * 
 * Provides reusable methods for batch file upload, processing, and download
 * to be used by Python-based controllers (Rtf, Ocr, etc.)
 * 
 * @package     CodeIgniter
 * @subpackage  Core
 * @category    Traits
 * @author      PASDA Team
 */
trait Batch_File_Processing
{
    /**
     * Validate uploaded files by extension
     * 
     * @param array $files_data Array of file data
     * @param array $allowed_ext Array of allowed extensions
     * @return array|false Returns error array on failure, true on success
     */
    protected function validate_files_extensions($files_data, $allowed_ext)
    {
        foreach ($files_data as $file) {
            if (!in_array($file['ext'], $allowed_ext)) {
                $error = 'Tipe berkas yang anda coba unggah tidak diperbolehkan. Tipe yang diizinkan: ' . strtoupper(implode(', ', $allowed_ext));
                log_message('error', 'Upload Error - Invalid extension: ' . $file['ext']);
                return [
                    'success' => false,
                    'message' => $error
                ];
            }
        }
        return true;
    }

    /**
     * Detect actual file type using magic bytes
     *
     * @param string $file_path Path to uploaded file
     * @param string $ext Original extension
     * @return string Detected extension
     */
    protected function detect_file_type($file_path, $ext)
    {
        // Read first few bytes
        $handle = fopen($file_path, 'rb');
        if (!$handle) {
            return $ext;
        }

        $bytes = fread($handle, 16);
        fclose($handle);

        if ($bytes === false || strlen($bytes) < 8) {
            return $ext;
        }

        // Check for DOCX (ZIP-based format) - starts with PK (0x50 0x4B)
        if (substr($bytes, 0, 2) === "PK") {
            // If extension is .doc but magic bytes show PK, it's actually DOCX
            if ($ext === 'doc') {
                return 'docx';
            }
            return $ext;
        }

        // Check for RTF (starts with {\rtf)
        if (substr($bytes, 0, 5) === "{\\rtf") {
            return 'rtf';
        }

        // Check for OLE compound document (DOC files)
        // Starts with D0 CF 11 E0 A1 B1 1A E1
        $ole_signature = "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1";
        if (substr($bytes, 0, 8) === $ole_signature) {
            return 'doc';
        }

        // Check for newer OLE format (also used by DOC)
        if ($ext === 'doc') {
            return 'doc';
        }

        return $ext;
    }

    /**
     * Upload files and create batch record
     * 
     * @param string $file_field Name of the file field ('document' or 'documents')
     * @param array $allowed_ext Array of allowed file extensions
     * @param string $output_suffix Suffix for output filename (e.g., '_fixed', '_ocr')
     * @param string $output_extension Extension for output files (e.g., 'rtf', 'docx')
     * @param callable $file_type_detector Optional callback to detect file type
     * @return array JSON response
     */
    protected function batch_upload(
        $file_field = 'documents',
        $allowed_ext = ['rtf', 'doc', 'docx'],
        $output_suffix = '_fixed',
        $output_extension = 'rtf',
        $file_type_detector = null
    ) {
        // Only handle AJAX requests
        if (!$this->input->is_ajax_request()) {
            $this->session->set_flashdata('error', 'Invalid request method');
            redirect(current_url());
        }

        // Check if we're receiving multiple files
        $is_multiple = isset($_FILES[$file_field]) && is_array($_FILES[$file_field]['name']);
        $upload_field = $is_multiple ? $file_field : 'document';

        // Collect file data
        $files_data = [];
        if (isset($_FILES[$upload_field]['name'])) {
            if ($is_multiple) {
                $file_count = count($_FILES[$file_field]['name']);
                for ($i = 0; $i < $file_count; $i++) {
                    $ext = strtolower(pathinfo($_FILES[$file_field]['name'][$i], PATHINFO_EXTENSION));
                    $size = $_FILES[$file_field]['size'][$i];
                    $error = $_FILES[$file_field]['error'][$i];

                    if ($error === UPLOAD_ERR_OK && $size > 0) {
                        $files_data[] = [
                            'name' => $_FILES[$file_field]['name'][$i],
                            'type' => $_FILES[$file_field]['type'][$i],
                            'tmp_name' => $_FILES[$file_field]['tmp_name'][$i],
                            'error' => $error,
                            'size' => $size,
                            'ext' => $ext
                        ];
                    }
                }
            } else {
                $ext = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));
                $size = $_FILES['document']['size'];

                if ($size > 0) {
                    $files_data[] = [
                        'name' => $_FILES['document']['name'],
                        'type' => $_FILES['document']['type'],
                        'tmp_name' => $_FILES['document']['tmp_name'],
                        'error' => $_FILES['document']['error'],
                        'size' => $size,
                        'ext' => $ext
                    ];
                }
            }
        }

        // Validate file extensions
        $validation = $this->validate_files_extensions($files_data, $allowed_ext);
        if (is_array($validation)) {
            return $this->set_content_type($validation);
        }

        // Configure upload
        $config['upload_path'] = $this->upload_path;
        $config['allowed_types'] = '*';
        $config['max_size'] = 10240; // 10MB
        $config['encrypt_name'] = TRUE;
        $config['remove_spaces'] = TRUE;
        $config['overwrite'] = FALSE;
        $config['file_ext_tolower'] = TRUE;

        $this->upload->initialize($config);

        // Generate batch ID
        $batch_id = 'batch_' . uniqid() . '_' . time();
        $batch_files = [];
        $upload_errors = [];

        // Upload files
        if ($is_multiple) {
            $file_count = count($_FILES[$file_field]['name']);
            for ($i = 0; $i < $file_count; $i++) {
                if ($_FILES[$file_field]['error'][$i] !== UPLOAD_ERR_OK) {
                    $upload_errors[] = [
                        'index' => $i,
                        'name' => $_FILES[$file_field]['name'][$i],
                        'error' => 'Upload error code: ' . $_FILES[$file_field]['error'][$i]
                    ];
                    continue;
                }

                $_FILES['single_file'] = [
                    'name' => $_FILES[$file_field]['name'][$i],
                    'type' => $_FILES[$file_field]['type'][$i],
                    'tmp_name' => $_FILES[$file_field]['tmp_name'][$i],
                    'error' => $_FILES[$file_field]['error'][$i],
                    'size' => $_FILES[$file_field]['size'][$i]
                ];

                if (!$this->upload->do_upload('single_file')) {
                    $upload_errors[] = [
                        'index' => $i,
                        'name' => $_FILES[$file_field]['name'][$i],
                        'error' => $this->upload->display_errors('', '')
                    ];
                    continue;
                }

                $upload_data = $this->upload->data();
                $file_path = $upload_data['full_path'];
                $original_name = $upload_data['client_name'];
                $file_ext = strtolower($upload_data['file_ext']);

                // Detect file type
                $actual_ext = $file_type_detector 
                    ? call_user_func($file_type_detector, $file_path, $file_ext)
                    : $this->detect_file_type($file_path, $file_ext);

                // Generate output filename
                $output_filename = pathinfo($original_name, PATHINFO_FILENAME) . $output_suffix . '.' . $output_extension;
                $output_path = rtrim($this->output_path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $output_filename;

                $batch_files[] = [
                    'index' => $i,
                    'original_name' => $original_name,
                    'file_path' => $file_path,
                    'output_path' => $output_path,
                    'output_filename' => $output_filename,
                    'file_ext' => $actual_ext,
                    'status' => 'pending',
                    'error' => null
                ];
            }
        } else {
            if (!$this->upload->do_upload($upload_field)) {
                $error = $this->upload->display_errors();
                log_message('error', 'Upload Error: ' . $error);
                return $this->set_content_type([
                    'success' => false,
                    'message' => $error
                ]);
            }

            $upload_data = $this->upload->data();
            $file_path = $upload_data['full_path'];
            $original_name = $upload_data['client_name'];
            $file_ext = strtolower($upload_data['file_ext']);

            // Detect file type
            $actual_ext = $file_type_detector 
                ? call_user_func($file_type_detector, $file_path, $file_ext)
                : $this->detect_file_type($file_path, $file_ext);

            // Generate output filename
            $output_filename = pathinfo($original_name, PATHINFO_FILENAME) . $output_suffix . '.' . $output_extension;
            $output_path = rtrim($this->output_path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $output_filename;

            $batch_files[] = [
                'index' => 0,
                'original_name' => $original_name,
                'file_path' => $file_path,
                'output_path' => $output_path,
                'output_filename' => $output_filename,
                'file_ext' => $actual_ext,
                'status' => 'pending',
                'error' => null
            ];
        }

        // Check if any files were uploaded successfully
        if (empty($batch_files)) {
            $error_msg = 'Tidak ada file yang berhasil diunggah';
            if (!empty($upload_errors)) {
                $error_msg .= '. Errors: ' . implode(', ', array_column($upload_errors, 'error'));
            }
            log_message('error', 'Upload Error: No files uploaded successfully');
            return $this->set_content_type([
                'success' => false,
                'message' => $error_msg
            ]);
        }

        // Store batch in session
        $batch_data = [
            'batch_id' => $batch_id,
            'files' => $batch_files,
            'total' => count($batch_files),
            'processed' => 0,
            'success' => 0,
            'failed' => 0,
            'status' => 'uploaded',
            'created_at' => time()
        ];

        $this->session->set_tempdata($batch_id, $batch_data, 600); // 10 minutes

        return $this->set_content_type([
            'success' => true,
            'batch_id' => $batch_id,
            'file_count' => count($batch_files),
            'files' => array_map(function($f) {
                return [
                    'index' => $f['index'],
                    'name' => $f['original_name'],
                    'status' => $f['status']
                ];
            }, $batch_files),
            'message' => count($batch_files) . ' file(s) berhasil diunggah'
        ]);
    }

    /**
     * Download a specific file from a batch
     * 
     * @param string $batch_id Batch ID
     * @param int $file_index File index in batch
     * @return void
     */
    protected function download_batch_file($batch_id, $file_index)
    {
        $batch_data = $this->session->tempdata($batch_id);

        if (!$batch_data || !isset($batch_data['files'][$file_index])) {
            show_404();
        }

        $file_info = $batch_data['files'][$file_index];
        $output_path = $file_info['output_path'];
        $output_filename = basename($file_info['output_filename']);

        if (!$output_path || !file_exists($output_path)) {
            log_message('error', 'Download batch file error: File not found at ' . $output_path);
            show_404();
        }

        if (filesize($output_path) === 0) {
            log_message('error', 'Download batch file error: File is empty at ' . $output_path);
            show_404();
        }

        // Set download headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $output_filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($output_path));

        ob_clean();
        flush();
        readfile($output_path);
        exit;
    }

    /**
     * Download all processed files as ZIP archive
     * 
     * @param string $batch_id Batch ID
     * @param string $zip_filename_prefix Prefix for ZIP filename
     * @return array JSON response
     */
    protected function download_all_batch($batch_id, $zip_filename_prefix = 'files')
    {
        // Only handle AJAX requests
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if (!$batch_id) {
            return $this->set_content_type([
                'success' => false,
                'message' => 'Batch ID tidak ditemukan'
            ]);
        }

        $batch_data = $this->session->tempdata($batch_id);

        if (!$batch_data || !isset($batch_data['files'])) {
            return $this->set_content_type([
                'success' => false,
                'message' => 'Batch data tidak ditemukan'
            ]);
        }

        // Load ZIP library
        $this->load->library('zip');

        $added_count = 0;
        $failed_files = [];

        foreach ($batch_data['files'] as $file_info) {
            if ($file_info['status'] === 'success' && file_exists($file_info['output_path'])) {
                $this->zip->read_file($file_info['output_path'], $file_info['output_filename']);
                $added_count++;
            } else {
                $failed_files[] = $file_info['original_name'];
            }
        }

        if ($added_count === 0) {
            return $this->set_content_type([
                'success' => false,
                'message' => 'Tidak ada file yang berhasil diproses untuk diunduh'
            ]);
        }

        // Generate ZIP filename
        $zip_filename = $zip_filename_prefix . '_' . date('Ymd_His') . '.zip';
        $temp_zip_path = $this->output_path . DIRECTORY_SEPARATOR . $zip_filename;
        $this->zip->archive($temp_zip_path);

        // Read ZIP data
        $zip_data = file_get_contents($temp_zip_path);

        // Delete temp ZIP file
        @unlink($temp_zip_path);

        return $this->set_content_type([
            'success' => true,
            'zip_data' => base64_encode($zip_data),
            'zip_filename' => $zip_filename,
            'file_count' => $added_count,
            'message' => $added_count . ' file(s) siap untuk diunduh'
        ]);
    }

    /**
     * Process a batch of files
     * 
     * @param string $batch_id Batch ID
     * @param callable $processor Callback function to process each file
     * @param array $error_messages Array of custom error message mappings
     * @param int $session_ttl Session TTL in seconds after processing
     * @return array JSON response
     */
    protected function process_batch(
        $batch_id,
        callable $processor,
        $error_messages = [],
        $session_ttl = 300
    ) {
        $batch_data = $this->session->tempdata($batch_id);

        if (!$batch_data || !isset($batch_data['files'])) {
            return $this->set_content_type([
                'success' => false,
                'message' => 'Batch data tidak ditemukan'
            ]);
        }

        $results = [];
        $processed = 0;
        $success = 0;
        $failed = 0;

        foreach ($batch_data['files'] as $index => $file_info) {
            $file_path = $file_info['file_path'];
            $output_path = $file_info['output_path'];
            $original_name = $file_info['original_name'];
            $file_ext = $file_info['file_ext'];

            // Run processor
            $result = call_user_func($processor, $file_path, $output_path, $file_ext);

            $processed++;

            if ($result['success']) {
                // Clean up input file
                if (file_exists($file_path)) {
                    unlink($file_path);
                }

                $success++;
                $batch_data['files'][$index]['status'] = 'success';
                $batch_data['files'][$index]['error'] = null;

                $results[] = [
                    'index' => $index,
                    'name' => $original_name,
                    'status' => 'success',
                    'message' => isset($result['message']) ? $result['message'] : 'File ' . strtoupper($file_ext) . ' berhasil diproses'
                ];
            } else {
                // Clean up input file
                if (file_exists($file_path)) {
                    unlink($file_path);
                }

                $failed++;
                $batch_data['files'][$index]['status'] = 'failed';

                // Format error message
                $error_message = $result['error'];
                $error_message = $this->format_process_error($error_message, $error_messages, $file_ext);

                $batch_data['files'][$index]['error'] = $error_message;

                $results[] = [
                    'index' => $index,
                    'name' => $original_name,
                    'status' => 'failed',
                    'message' => $error_message
                ];
            }
        }

        // Update batch data
        $batch_data['processed'] = $processed;
        $batch_data['success'] = $success;
        $batch_data['failed'] = $failed;
        $batch_data['status'] = 'completed';

        // Save back to session
        $this->session->set_tempdata($batch_id, $batch_data, $session_ttl);

        return $this->set_content_type([
            'success' => true,
            'batch_id' => $batch_id,
            'results' => $results,
            'processed' => $processed,
            'total' => $batch_data['total'],
            'success_count' => $success,
            'failed_count' => $failed,
            'message' => $success . ' dari ' . $processed . ' file berhasil diproses'
        ]);
    }

    /**
     * Format error message based on patterns
     * 
     * @param string $error_message Original error message
     * @param array $error_messages Custom error message mappings
     * @param string $file_ext File extension
     * @return string Formatted error message
     */
    protected function format_process_error($error_message, $error_messages, $file_ext = '')
    {
        // Check custom error mappings first
        foreach ($error_messages as $pattern => $message) {
            if (strpos($error_message, $pattern) !== false) {
                return $message;
            }
        }

        // Default patterns
        if (strpos($error_message, 'DOC to RTF conversion failed') !== false) {
            if (strpos($error_message, 'DOCX') !== false || strpos($error_message, '.docx') !== false) {
                return 'File ini sebenarnya adalah format DOCX tetapi menggunakan ekstensi .doc. Harap ubah ekstensi file menjadi .docx dan coba lagi.';
            } elseif (strpos($error_message, 'rusak') !== false) {
                return 'File DOC rusak atau tidak dapat dibaca.';
            } elseif (strpos($error_message, 'kata sandi') !== false) {
                return 'File DOC dilindungi kata sandi.';
            } elseif (strpos($error_message, 'terlalu kecil') !== false) {
                return 'File DOC terlalu kecil dan mungkin rusak.';
            } else {
                return 'Gagal mengkonversi file DOC ke RTF.';
            }
        } elseif (strpos($error_message, 'corrupt') !== false) {
            return 'File rusak atau tidak dapat dibaca.';
        } elseif (strpos($error_message, 'password') !== false || strpos($error_message, 'protected') !== false) {
            return 'File dilindungi kata sandi.';
        }

        return $error_message;
    }
}
