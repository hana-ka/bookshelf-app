<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | バリデーションルールごとの共通メッセージを定義する。
    |
    */

    'accepted' => ':attributeを承認してください。',

    'array' => ':attributeは配列で入力してください。',

    'before_or_equal' => ':attributeは:date以前の日付を指定してください。',

    'date' => ':attributeは有効な日付形式で入力してください。',

    'email' => ':attributeはメール形式で入力してください。',

    'exists' => '選択された:attributeは存在しません。',

    'integer' => ':attributeは整数で入力してください。',

    'max' => [
        'array' => ':attributeは:max件以内で選択してください。',
        'file' => ':attributeは:max KB以内のファイルを指定してください。',
        'numeric' => ':attributeは:max以下で入力してください。',
        'string' => ':attributeは:max文字以内で入力してください。',
    ],

    'min' => [
        'array' => ':attributeは:min件以上選択してください。',
        'file' => ':attributeは:min KB以上のファイルを指定してください。',
        'numeric' => ':attributeは:min以上で入力してください。',
        'string' => ':attributeは:min文字以上で入力してください。',
    ],

    'required' => ':attributeを入力してください。',

    'confirmed' => ':attribute（確認用）と一致しません。',

    'string' => ':attributeは文字列で入力してください。',

    'unique' => 'その:attributeは既に使用されています。',

    'url' => ':attributeは有効なURL形式で入力してください。',

    /*
    |--------------------------------------------------------------------------
    | Password Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Password::default() / Password::min() などで使用されるメッセージ。
    |
    */

    'password' => [
        'letters' => ':attributeには少なくとも1文字の英字を含めてください。',
        'mixed' => ':attributeには大文字と小文字をそれぞれ1文字以上含めてください。',
        'numbers' => ':attributeには少なくとも1文字の数字を含めてください。',
        'symbols' => ':attributeには少なくとも1文字の記号を含めてください。',
        'uncompromised' => '入力された:attributeは情報漏えいの可能性があります。別のものを指定してください。',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | 項目ごとに標準メッセージを上書きしたい場合に使用する。
    |
    */

    'custom' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | :attribute に表示する項目名を日本語へ変換する。
    |
    */

    'attributes' => [
        'name' => 'お名前',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'password_confirmation' => '確認用パスワード',

        'title' => 'タイトル',
        'author' => '著者名',
        'isbn' => 'ISBN',
        'published_date' => '出版日',
        'description' => '説明',
        'image_url' => '画像URL',
        'genres' => 'ジャンル',
        'genres.*' => 'ジャンル',

        'rating' => '評価',
        'comment' => 'コメント',

        'user_id' => '登録者ID',
        'genre_id' => 'ジャンルID',
        'book_id' => '書籍',
        'target_date' => '期日',
    ],

];