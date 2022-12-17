<?php

include 'wp-config.php';

class FileUploadRemote {

    public function DosyaYukle($dosya, $baslik = NULL)
    {
        $baslik = ($baslik == NULL) ? substr(md5(time()), 0, 10) : $baslik;
        $uzanti = explode('.', $dosya);
        $uzanti = end($uzanti);
        $filename = sanitize_title($baslik) . "." . $uzanti;
        $filetype = wp_check_filetype(basename($filename), null);
        $upload = wp_upload_bits($filename, $filetype, file_get_contents($dosya));
        if (empty($upload['error'])) {
            $attachment = array(
                'guid'           => $upload['url'],
                'post_mime_type' => $filetype['type'],
                'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );
            $attach_id = wp_insert_attachment($attachment, $upload['file'], 0);
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
            wp_update_attachment_metadata($attach_id, $attach_data);
            return array(
                'id' => $attach_id,
                'url' => $upload['url']
            );
        }
        return false;
    }
}

$yukle = new FileUploadRemote;

print_r($yukle->DosyaYukle("https://funart.pro/uploads/posts/2021-12/1640310911_78-funart-pro-p-foni-dlya-glavnikh-ekranov-80.jpg"));



