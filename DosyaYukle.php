<?php

include 'wp-config.php';

class FileUploadRemote {

    public function DosyaYukle($file, $baslik = NULL)
    {
        $file_headers = @get_headers($file);
        if ($file_headers) {
            if (strstr($file_headers[0], '200 OK')) {
                $baslik = ($baslik == NULL) ? md5(time()) : $baslik;
                echo $baslik;
                $uzantilar = ["jpg", "jpeg", "png", "webp"];
                foreach ($uzantilar as $uzanti_bul) {
                    if (strstr($file, $uzanti_bul)) {
                        $uzanti = $uzanti_bul;
                    }
                }
                $filename = sanitize_title($baslik) . "." . $uzanti;
                $filename = sanitize_title($baslik) . md5(rand(0, 50)) . "." . $uzanti;
                $filetype = wp_check_filetype(basename($filename), null);
                $upload = wp_upload_bits($filename, $filetype, file_get_contents($file));
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
                }
                return false;
            } else {
                return "false"; //File Does Not Exist, Access Denied, URL Moved, etc.
            }
        } else {
            return "false"; // The server did not respond. There are no titles or files to show.
        }
    }
}

$yukle = new FileUploadRemote;

print_r($yukle->DosyaYukle("https://i.stack.imgur.com/cskyi.png"));
