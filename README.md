Yii2商城后台相关
=========
以[yii2-admin](https://github.com/nadirvishun/yii2-admin)这个为基础后台,这里只是写下简单的功能练手而已

### 一些问题
- 目前只想将后台相关逻辑写出来，至于前台暂不考虑
- 商品规格、参数等由于目前是b2c，而不是b2b2c，所以直接填写，而不与商品分类挂钩，优点是灵活，适用于品类少的情况，缺点就是缺少规范和复用，同时相关的检索缺失。
- `npm-asset/ipinyinjs`扩展在window下无法安装，但是在linux下是可以安装。
    - 初步判断原因：有的文件名中带有中文字符。
    - 暂时解决方法是：先在linux更新后，将`vendor/nmp-asset/ipinyinjs`文件夹拷贝到windows同位置中，并参照linux中的`vendor/composer/installed.json`文件来增加windows中此文件关于此扩展内容，最后`composer update`即可。
    - 目前此问题提交到composer项目的github中：[Extraction from phar failed in Windows 10 when the file name has unicode char](https://github.com/composer/composer/issues/7474),看后续如何解决吧。