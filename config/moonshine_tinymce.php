<?php

return [
    'token' => env('TINYMCE_TOKEN', ''),
    'plugins' => [
        'anchor', 'autolink', 'autoresize', 'charmap', 'codesample', 'code', 'emoticons', 'image', 'link',
        'lists', 'advlist', 'media', 'searchreplace', 'table', 'wordcount', 'directionality',
        'fullscreen', 'help', 'nonbreaking', 'pagebreak', 'preview', 'visualblocks', 'visualchars',
    ],
    'menubar' => 'file edit insert view format table tools',
    'toolbar' => 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | '
        .'link image media table tabledelete hr nonbreaking pagebreak | align lineheight | '
        .'numlist bullist indent outdent | emoticons charmap | removeformat | codesample | ltr rtl | '
        .'tableprops tablerowprops tablecellprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | '
        .'tableinsertcolbefore tableinsertcolafter tabledeletecol | '
        .'fullscreen preview print visualblocks visualchars code | help',
    'options' => [
        // Shows "Link Attribute" (rel) dropdown in Ctrl+K / Insert link dialog
        'link_default_target' => '_blank',
        'link_assume_external_targets' => true,
        'link_title' => true,
        'target_list' => [
            ['title' => 'None', 'value' => ''],
            ['title' => 'New window', 'value' => '_blank'],
            ['title' => 'Same window', 'value' => '_self'],
        ],
        'link_rel_list' => [
            ['title' => 'DoFollow', 'value' => ''],
            ['title' => 'NoFollow', 'value' => 'nofollow'],
            ['title' => 'Sponsored', 'value' => 'sponsored'],
            ['title' => 'UGC', 'value' => 'ugc'],
            ['title' => 'NoFollow + Sponsored', 'value' => 'nofollow sponsored'],
            ['title' => 'NoFollow + UGC', 'value' => 'nofollow ugc'],
        ],
        'extended_valid_elements' => 'a[href|target|rel|title|class|id],img[src|alt|title|class|width|height]',
        'toolbar_sticky' => true,
        'toolbar_sticky_offset' => 0,
        'min_height' => 420,
        'max_height' => 720,
        'image_title' => true,
        'image_description' => true,
        'image_dimensions' => false,
        'image_advtab' => false,
        'image_uploadtab' => true,
        'automatic_uploads' => true,
        'paste_data_images' => true,
        'images_file_types' => 'jpeg,jpg,jpe,jfi,jif,jfif,png,gif,webp',
        'file_picker_types' => 'image',
        'images_upload_url' => '/cms/tinymce/upload',
        'images_upload_credentials' => true,
    ],
    'callbacks' => [
        'images_upload_handler' => <<<'JS'
(blobInfo, progress) => new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/cms/tinymce/upload');
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        xhr.setRequestHeader('X-CSRF-TOKEN', token);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');
    }
    xhr.upload.onprogress = (e) => {
        if (e.lengthComputable && typeof progress === 'function') {
            progress(e.loaded / e.total * 100);
        }
    };
    xhr.onload = () => {
        if (xhr.status === 401 || xhr.status === 419) {
            reject('Please log in again, then retry the upload.');
            return;
        }
        if (xhr.status < 200 || xhr.status >= 300) {
            reject('Image upload failed (' + xhr.status + '). Use jpg, png, webp or gif up to 5MB.');
            return;
        }
        let json;
        try {
            json = JSON.parse(xhr.responseText);
        } catch (e) {
            reject('Invalid upload response.');
            return;
        }
        if (!json || !json.location) {
            reject((json && json.message) ? json.message : 'Invalid upload response.');
            return;
        }
        resolve(json.location);
    };
    xhr.onerror = () => reject('Image upload failed.');
    const formData = new FormData();
    formData.append('file', blobInfo.blob(), blobInfo.filename());
    xhr.send(formData);
})
JS,
        'file_picker_callback' => <<<'JS'
(callback, value, meta) => {
    if (meta.filetype !== 'image') {
        return;
    }
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/jpeg,image/png,image/webp,image/gif';
    input.onchange = () => {
        const file = input.files && input.files[0];
        if (!file) {
            return;
        }
        const reader = new FileReader();
        reader.onload = () => {
            const editor = tinymce.activeEditor;
            const blobCache = editor.editorUpload.blobCache;
            const base64 = reader.result.toString().split(',')[1];
            const blobInfo = blobCache.create('blobid' + Date.now(), file, base64);
            blobCache.add(blobInfo);
            callback(blobInfo.blobUri(), {
                title: file.name,
                alt: file.name.replace(/\.[^.]+$/, ''),
            });
        };
        reader.readAsDataURL(file);
    };
    input.click();
}
JS,
    ],
];
