<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Handles uploads error with translation texts
 * @param  mixed $error type of error
 * @return mixed
 */
function _perfex_upload_error_so($error)
{
    $phpFileUploadErrors = array(
        0 => _l('file_uploaded_success'),
        1 => _l('file_exceeds_max_filesize'),
        2 => _l('file_exceeds_maxfile_size_in_form'),
        3 => _l('file_uploaded_partially'),
        4 => _l('file_not_uploaded'),
        6 => _l('file_missing_temporary_folder'),
        7 => _l('file_failed_to_write_to_disk'),
        8 => _l('file_php_extension_blocked'),
    );

    if (isset($phpFileUploadErrors[$error]) && $error != 0) {
        return $phpFileUploadErrors[$error];
    }

    return false;
}

/**
 * Handles upload for project files
 * @param  mixed $project_id project id
 * @return boolean
 */
function handle_project_file_uploads_so($project_id)
{
	
   $filesIDS = array();
    $errors = array();

    if (isset($_FILES['file']['name'])
        && ($_FILES['file']['name'] != '' || is_array($_FILES['file']['name']) && count($_FILES['file']['name']) > 0)) {
        //echo do_action('before_upload_project_attachment', $project_id);
        do_action('before_upload_saleopp_attachment', $project_id);

        if (!is_array($_FILES['file']['name'])) {
            $_FILES['file']['name'] = array($_FILES['file']['name']);
            $_FILES['file']['type'] = array($_FILES['file']['type']);
            $_FILES['file']['tmp_name'] = array($_FILES['file']['tmp_name']);
            $_FILES['file']['error'] = array($_FILES['file']['error']);
            $_FILES['file']['size'] = array($_FILES['file']['size']);
        }


       $path        = get_upload_path_by_type_so('saleopp') . $project_id . '/';
    

        for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
            if (_perfex_upload_error_so($_FILES['file']['error'][$i])) {
                $errors[$_FILES['file']['name'][$i]] = _perfex_upload_error_so($_FILES['file']['error'][$i]);
                continue;
            }

            // Get the temp file path
            $tmpFilePath = $_FILES['file']['tmp_name'][$i]; 
            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                _maybe_create_upload_path_so($path);
                $filename    = unique_filename_so($path, $_FILES["file"]["name"][$i]);
                $newFilePath = $path . $filename;
                // Upload the file into the company uploads dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $CI =& get_instance();
                    if (is_client_logged_in()) {
                        $contact_id = get_contact_user_id();
                        $staffid = 0;
                    } else {
                        $staffid = get_staff_user_id();
                        $contact_id = 0;
                    }
                        $data = array(
                            'project_id' => $project_id,
                            'file_name' => $filename,
                            'filetype' => $_FILES["file"]["type"][$i],
                            'dateadded' => date('Y-m-d H:i:s'),
                            'staffid' => $staffid,
                            'contact_id' => $contact_id,
                            'subject' => $filename,
                        );
                    if (is_client_logged_in()) {
                        $data['visible_to_customer'] = 1;
                    } else {
                        $data['visible_to_customer'] = ($CI->input->post('visible_to_customer') == 'true' ? 1 : 0);
                    }
                    $CI->db->insert('tblsalefiles', $data);

                    $insert_id = $CI->db->insert_id();
                    if ($insert_id) {
                        if (is_image($newFilePath)) {
                            create_img_thumb_so($path, $filename);
                        }
                        array_push($filesIDS, $insert_id);
                    } else {
                        unlink($newFilePath);

                        return false;
                    }
                }
            }
        }
    }

    if (count($filesIDS) > 0) {
        $CI->load->model('sales_model');
        end($filesIDS);
        $lastFileID = key($filesIDS);
        $CI->sales_model->new_project_file_notification($filesIDS[$lastFileID], $project_id);
    }

    if(count($errors) > 0){
        $message = '';
        foreach($errors as $filename => $error_message){
            $message .= $filename . ' - ' . $error_message .'<br />';
        }
        header('HTTP/1.0 400 Bad error');
        echo $message;
        die;
    }

    if(count($filesIDS) > 0){
        return true;
    }

    return false;
}


function create_img_thumb_so($path, $filename, $width = 300, $height = 300)
{
    $CI = &get_instance();

    $source_path = rtrim($path, '/') . '/' . $filename;
    $target_path = $path;
    $config_manip = array(
        'image_library' => 'gd2',
        'source_image' => $source_path,
        'new_image' => $target_path,
        'maintain_ratio' => true,
        'create_thumb' => true,
        'thumb_marker' => '_thumb',
        'width' => $width,
        'height' => $height
    );

    $CI->image_lib->initialize($config_manip);
    $CI->image_lib->resize();
    $CI->image_lib->clear();
}



/**
 * Check if path exists if not exists will create one
 * This is used when uploading files
 * @param  string $path path to check
 * @return null
 */
function _maybe_create_upload_path_so($path)
{
    if (!file_exists($path)) {
        mkdir($path);
        fopen($path . 'index.html', 'w');
    }
}

/**
 * Function that return full path for upload based on passed type
 * @param  string $type
 * @return string
 */
function get_upload_path_by_type_so($type)
{
    switch ($type) {
        case 'lead':
            return LEAD_ATTACHMENTS_FOLDER;
        break;
        case 'expense':
            return EXPENSE_ATTACHMENTS_FOLDER;
        break;
        case 'project':
            return PROJECT_ATTACHMENTS_FOLDER;
        break;
		case 'saleopp':
            return SALE_OPPORTUNITY_ATTACHMENTS_FOLDER;
        break;
        case 'proposal':
            return PROPOSAL_ATTACHMENTS_FOLDER;
        break;
        case 'estimate':
            return ESTIMATE_ATTACHMENTS_FOLDER;
        break;
        case 'invoice':
            return INVOICE_ATTACHMENTS_FOLDER;
        break;
        case 'credit_note':
            return CREDIT_NOTES_ATTACHMENTS_FOLDER;
        break;
        case 'task':
            return TASKS_ATTACHMENTS_FOLDER;
        break;
        case 'contract':
            return CONTRACTS_UPLOADS_FOLDER;
        break;
        case 'customer':
            return CLIENT_ATTACHMENTS_FOLDER;
        break;
        case 'staff':
        return STAFF_PROFILE_IMAGES_FOLDER;
        break;
        case 'company':
        return COMPANY_FILES_FOLDER;
        break;
        case 'ticket':
        return TICKET_ATTACHMENTS_FOLDER;
        break;
        case 'contact_profile_images':
        return CONTACT_PROFILE_IMAGES_FOLDER;
        break;
        case 'newsfeed':
        return NEWSFEED_FOLDER;
        break;
        default:
        return false;
    }
}



/**
 * Unique filename based on folder
 * @since  Version 1.0.1
 * @param  string $dir      directory to compare
 * @param  string $filename filename
 * @return string           the unique filename
 */
function unique_filename_so($dir, $filename)
{
    // Separate the filename into a name and extension.
    $info     = pathinfo($filename);
    $ext      = !empty($info['extension']) ? '.' . $info['extension'] : '';
    $filename = sanitize_file_name_so($filename);
    $number   = '';
    // Change '.ext' to lower case.
    if ($ext && strtolower($ext) != $ext) {
        $ext2      = strtolower($ext);
        $filename2 = preg_replace('|' . preg_quote($ext) . '$|', $ext2, $filename);
        // Check for both lower and upper case extension or image sub-sizes may be overwritten.
        while (file_exists($dir . "/$filename") || file_exists($dir . "/$filename2")) {
            $filename  = str_replace(array(
                "-$number$ext",
                "$number$ext",
            ), "-$new_number$ext", $filename);
            $filename2 = str_replace(array(
                "-$number$ext2",
                "$number$ext2",
            ), "-$new_number$ext2", $filename2);
            $number    = $new_number;
        }

        return $filename2;
    }
    while (file_exists($dir . "/$filename")) {
        if ('' == "$number$ext") {
            $filename = "$filename-" . ++$number;
        } else {
            $filename = str_replace(array(
                "-$number$ext",
                "$number$ext",
            ), "-" . ++$number . $ext, $filename);
        }
    }

    return $filename;
}
/**
 * Sanitize file name
 * @param  string $filename filename
 * @return mixed
 */
function sanitize_file_name_so($filename)
{
    $special_chars = array(
        "?",
        "[",
        "]",
        "/",
        "\\",
        "=",
        "<",
        ">",
        ":",
        ";",
        ",",
        "'",
        "\"",
        "&",
        "$",
        "#",
        "*",
        "(",
        ")",
        "|",
        "~",
        "`",
        "!",
        "{",
        "}",
        "%",
        "+",
        chr(0),
    );
    $filename      = str_replace($special_chars, '', $filename);
    $filename      = str_replace(array(
        '%20',
        '+',
    ), '-', $filename);
    $filename      = preg_replace('/[\r\n\t -]+/', '-', $filename);
    $filename      = trim($filename, '.-_');
    // Split the filename into a base and extension[s]
    $parts         = explode('.', $filename);
    // Return if only one extension
    if (count($parts) <= 2) {
        return $filename;
    }
    // Process multiple extensions
    $filename  = array_shift($parts);
    $extension = array_pop($parts);

    $filename .= '.' . $extension;
    $CI =& get_instance();
    $filename = $CI->security->sanitize_filename($filename);

    return $filename;
}


function project_file_url_so($file, $preview = false)
{
    $path = 'uploads/saleopp/'.$file['project_id'].'/';
    $fullPath = FCPATH.$path.$file['file_name'];
    $url = base_url($path.$file['file_name']);

    if (!empty($file['external']) && !empty($file['thumbnail_link'])) {
        $url = $file['thumbnail_link'];
    } else {
        if ($preview) {
            $fname = pathinfo($fullPath, PATHINFO_FILENAME);
            $fext = pathinfo($fullPath, PATHINFO_EXTENSION);
            $thumbPath = pathinfo($fullPath, PATHINFO_DIRNAME).'/'.$fname.'_thumb.'.$fext;
            if (file_exists($thumbPath)) {
                $url = base_url('uploads/saleopp/'.$file['project_id'].'/'.$fname.'_thumb.'.$fext);
            }
        }
    }

    return $url;
}







//discussion attachment below 

/**
 * Handle upload for project discussions comment
 * Function for jquery-comment plugin
 * @param  mixed $discussion_id discussion id
 * @param  mixed $post_data     additional post data from the comment
 * @param  array $insert_data   insert data to be parsed if needed
 * @return arrray
 */
function handle_project_discussion_comment_attachments_so($discussion_id, $post_data, $insert_data)
{
    if (isset($_FILES['file']['name']) && _perfex_upload_error_so($_FILES['file']['error'])) {
        header('HTTP/1.0 400 Bad error');
        echo json_encode(array('message'=>_perfex_upload_error_so($_FILES['file']['error'])));
        die;
    }

    if (isset($_FILES['file']['name'])) {
        do_action('before_upload_project_discussion_comment_attachment');
        //$path = PROJECT_DISCUSSION_ATTACHMENT_FOLDER .$discussion_id . '/';
        $path = SALEOPP_DISCUSSION_ATTACHMENT_FOLDER .$discussion_id . '/';
        // Check for all cases if this extension is allowed
        if (!_upload_extension_allowed_so($_FILES["file"]["name"])) {
            header('HTTP/1.0 400 Bad error');
            echo json_encode(array('message'=>_l('file_php_extension_blocked')));
            die;
        }

        // Get the temp file path
        $tmpFilePath = $_FILES['file']['tmp_name'];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            _maybe_create_upload_path_so($path);
            $filename    = unique_filename_so($path, $_FILES['file']['name']);
            $newFilePath = $path . $filename;
            // Upload the file into the temp dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                $insert_data['file_name'] = $filename;

                if (isset($_FILES['file']['type'])) {
                    $insert_data['file_mime_type'] = $_FILES['file']['type'];
                } else {
                    $insert_data['file_mime_type'] = get_mime_by_extension($filename);
                }
            }
        }
    }

    return $insert_data;
}



/**
 * Check if extension is allowed for upload
 * @param  string $filename filename
 * @return boolean
 */
function _upload_extension_allowed_so($filename)
{
    $path_parts         = pathinfo($filename);
    $extension          = $path_parts['extension'];
    $extension = strtolower($extension);
    $allowed_extensions = explode(',', get_option('allowed_files'));
    $allowed_extensions = array_map('trim', $allowed_extensions);
    // Check for all cases if this extension is allowed
    if (!in_array('.'.$extension, $allowed_extensions)) {
        return false;
    }

    return true;
}


