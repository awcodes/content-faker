<?php

return [
    'inline_decorations' => true,

    'inline_probability' => 14,

    'link_probability' => 4,

    'code_probability' => 4,

    'max_inline_decorations_per_paragraph' => 3,

    'markdown' => [
        'inline_types' => [
            'bold',
            'italic',
            'bold_italic',
            'strikethrough',
            'code',
            'link',
        ],

        'alert_types' => [
            'NOTE',
            'TIP',
            'IMPORTANT',
            'WARNING',
            'CAUTION',
        ],
    ],

    'html' => [
        'inline_types' => [
            'strong',
            'em',
            'strong_em',
            's',
            'code',
            'link',
        ],

        'alert_types' => [
            'note',
            'tip',
            'important',
            'warning',
            'caution',
        ],
    ],

    'rich_editor' => [
        'merge_tags' => [
            'first_name',
            'last_name',
            'full_name',
            'email',
            'company_name',
            'unsubscribe_url',
            'app_name',
        ],

        'filament_block_wrapper_class' => 'filament-block',

        'button_class' => 'button',

        'columns_class' => 'columns',

        'callout_class' => 'callout',
    ],
];
