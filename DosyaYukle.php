<?php

include 'wp-config.php';

class FileUploadRemote {

    public function DosyaYukle($file_url, $baslik = NULL)
    {
        $baslik = ($baslik == NULL) ? md5(time()) : $baslik;
        $filename = basename($file_url);
        $filetype = wp_check_filetype($filename, null);
        $uzanti = $filetype['ext'];
        $filename = sanitize_title($baslik) . md5(rand(0, 50)) . "." . $uzanti;
    
        $upload_dir = wp_upload_dir();
        $target_file = $upload_dir['path'] . '/' . basename($filename);
    
        // Dosyayı yükleme işlemi
        $file_contents = file_get_contents($file_url);
        if (file_put_contents($target_file, $file_contents)) {
            $attachment = array(
                'guid'           => $upload_dir['url'] . '/' . basename($target_file),
                'post_mime_type' => $filetype['type'],
                'post_title'     => preg_replace('/\.[^.]+$/', '', basename($baslik)),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );
            $attach_id = wp_insert_attachment($attachment, $target_file, 0);
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            $attach_data = wp_generate_attachment_metadata($attach_id, $target_file);
            wp_update_attachment_metadata($attach_id, $attach_data);
    
            $data = array(
                'id' => $attach_id,
                'url' => $upload_dir['url'] . '/' . basename($target_file),
                'file' => $target_file,
                'boyutlar' => $attach_data['sizes'],
                'baslik' => $baslik,
                'filename' => $attach_data['file'],
                'uzanti' => pathinfo($target_file, PATHINFO_EXTENSION)
            );
            return $data;
        } else {
            return "Hata: Dosya yüklenirken bir sorun oluştu.";
        }
    }    
}

$yukle = new FileUploadRemote;

print_r($yukle->DosyaYukle("https://i.stack.imgur.com/cskyi.png"));
