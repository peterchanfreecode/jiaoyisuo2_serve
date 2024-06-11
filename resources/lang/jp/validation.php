<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | following language lines contain default error messages used by
    | validator class. Some of these rules have multiple versions such
    | as size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':属性を受け入れる必要があります.',
    'active_url'           => ':属性は有効なURLではありません.',
    'after'                => ':属性：dateの後の日付である必要があります.',
    'after_or_equal'       => ':属性：date以降の日付である必要があります.',
    'alpha'                => ':属性には文字のみを含めることができます.',
    'alpha_dash'           => ':属性には、文字、数字、ダッシュのみを含めることができます.',
    'alpha_num'            => ':属性には文字と数字のみを含めることができます.',
    'array'                => ':属性は配列でなければなりません.',
    'before'               => ':属性は：dateより前の日付である必要があります.',
    'before_or_equal'      => ':属性は：date以前の日付である必要があります.',
    'between'              => [
        'numeric' => ':属性は:minと：maxの間にある必要があります.',
        'file'    => ':属性は：minから：maxキロバイトの間でなければなりません.',
        'string'  => ':属性は：min文字と：max文字の間にある必要があります.',
        'array'   => ':属性は：minと：maxの間にある必要があります.',
    ],
    'boolean'              => ':属性フィールドはtrueまたはfalseである必要があります.',
    'confirmed'            => ':属性の確認が一致しません.',
    'date'                 => ':属性は有効な日付ではありません.',
    'date_format'          => ':属性がformat：formatと一致しません.',
    'different'            => ':属性と：otherは異なっている必要があります.',
    'digits'               => ':属性は：digitsdigitsでなければなりません.',
    'digits_between'       => ':属性は：minと：maxの数字の間でなければなりません.',
    'dimensions'           => ':属性の画像のサイズが無効です.',
    'distinct'             => ':属性フィールドの値が重複しています.',
    'email'                => ':属性は有効なメールアドレスである必要があります.',
    'exists'               => '選択された：属性が無効です.',
    'file'                 => ':属性はファイルである必要があります.',
    'filled'               => ':属性フィールドには値が必要です.',
    'image'                => ':属性は画像である必要があります.',
    'in'                   => '選択された：属性が無効です.',
    'in_array'             => ':属性フィールドが：otherに存在しません.',
    'integer'              => ':属性は整数でなければなりません.',
    'ip'                   => ':属性は有効なIPアドレスである必要があります.',
    'ipv4'                 => ':属性は有効なIPv4アドレスである必要があります.',
    'ipv6'                 => ':属性は有効なIPv6アドレスである必要があります.',
    'json'                 => ':属性は有効なJSON文字列である必要があります.',
    'max'                  => [
        'numeric' => ':属性は：maxより大きくすることはできません.',
        'file'    => ':属性は：maxキロバイトを超えることはできません.',
        'string'  => ':属性は：max文字より大きくすることはできません.',
        'array'   => ':属性には：maxを超えるアイテムを含めることはできません.',
    ],
    'mimes'                => ':属性は次のタイプのファイルである必要があります:: values.',
    'mimetypes'            => ':属性は次のタイプのファイルである必要があります:: values.',
    'min'                  => [
        'numeric' => ':属性は少なくとも：minである必要があります.',
        'file'    => ':属性は少なくとも：minキロバイトである必要があります.',
        'string'  => ':属性は少なくとも：min文字である必要があります.',
        'array'   => ':属性には少なくとも：minアイテムが必要です.',
    ],
    'not_in'               => '選択された：属性が無効です.',
    'numeric'              => ':属性は数値でなければなりません.',
    'present'              => ':属性フィールドが存在する必要があります.',
    'regex'                => ':属性の形式が無効です.',
    'required'             => ':属性フィールドは必須です.',
    'required_if'          => ':otherが：valueの場合、属性フィールドは必須です.',
    'required_unless'      => ':otherが：valuesにない限り、属性フィールドは必須です.',
    'required_with'        => ':valuesが存在する場合、属性フィールドは必須です.',
    'required_with_all'    => ':valuesが存在する場合、属性フィールドは必須です.',
    'required_without'     => ':valuesが存在しない場合、属性フィールドは必須です.',
    'required_without_all' => ':valuesが存在しない場合は、属性フィールドが必要です.',
    'same'                 => ':属性と：otherは一致する必要があります.',
    'size'                 => [
        'numeric' => ':属性は：sizeである必要があります.',
        'file'    => ':属性は：sizeキロバイトでなければなりません.',
        'string'  => ':属性は：size文字である必要があります.',
        'array'   => ':属性には：sizeアイテムが含まれている必要があります.',
    ],
    'string'               => ':属性は文字列である必要があります.',
    'timezone'             => ':属性は有効なゾーンである必要があります.',
    'unique'               => ':属性はすでに取得されています.',
    'uploaded'             => ':属性のアップロードに失敗しました.',
    'url'                  => ':属性の形式が無効です.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'カスタムメッセージ',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];

