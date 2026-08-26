<?php

return [
    'token' => env('TINYMCE_TOKEN', ''),
    'plugins' => [
        'anchor', 'autolink', 'autoresize', 'charmap', 'codesample', 'code', 'emoticons', 'image', 'link',
        'lists', 'advlist', 'media', 'searchreplace', 'table', 'wordcount', 'directionality',
        'fullscreen', 'help', 'nonbreaking', 'pagebreak', 'preview', 'visualblocks', 'visualchars'
    ],
    'menubar' => 'file edit insert view format table tools',
    'toolbar' => 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | '
        . 'link image media table tabledelete hr nonbreaking pagebreak | align lineheight | '
        . 'numlist bullist indent outdent | emoticons charmap | removeformat | codesample | ltr rtl | '
        . 'tableprops tablerowprops tablecellprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | '
        . 'tableinsertcolbefore tableinsertcolafter tabledeletecol | '
        . 'fullscreen preview print visualblocks visualchars code | help',
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
        'extended_valid_elements' => 'a[href|target|rel|title|class|id]',
    ],
    'callbacks' => [],
];
