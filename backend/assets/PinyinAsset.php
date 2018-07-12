<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * 汉字转拼音js引入
 */
class PinyinAsset extends AssetBundle
{
    public $sourcePath = '@npm/ipinyinjs';
    public $baseUrl = '@web';
    public $js = [
        'dict/pinyin_dict_firstletter.js',//首字母
        'pinyinUtil.js'
    ];
}
