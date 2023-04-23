<?php

include 'wp-config.php';

class FileUploadRemote {

   public function DosyaYukle($file, $baslik = NULL)
    {

        $baslik = ($baslik == NULL) ? md5(time()) : $baslik;
        $filetype = wp_check_filetype(basename($file), null);
        $uzanti = $filetype['ext'];
        $filename = sanitize_title($baslik) . md5(rand(0, 50)) . "." . $uzanti;

        $upload = wp_upload_bits($filename, 'null', file_get_content($file));
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
            foreach ($attach_data['sizes'] as $boyutlar) {
                $boyut[] = $boyutlar['file'];
            }
            wp_update_attachment_metadata($attach_id, $attach_data);
            return array(
                'id' => $attach_id,
                'url' => $upload['url'],
                'file' => $upload['file'],
                'boyutlar' => $boyut,
                'baslik' => $baslik . substr(md5(time()), 0, 4),
                'rbaslik' => $attach_data['file'],
                'uzanti' => $uzanti
            );
        } else {
            return "false"; //File Does Not Exist, Access Denied, URL Moved, etc.
        }
    }
}

$yukle = new FileUploadRemote;

print_r($yukle->DosyaYukle("https://i.stack.imgur.com/cskyi.png"));
