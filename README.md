# yii_db_rw_split
yii1.x 实现读写分离代码

## 声明
> 1. 代码都是基于网络上不完善和有bug的代码修改而成， 并把ar pdo 方式组合到一起，做了一下归类， 如果不对敬请谅解，欢迎提出问题！
> 2. 代码均从项目中复制过来，并修改了敏感信息，最重要的是明白思路，才能配置出来
> 3. xmind 中为一些整理，可作为参考

## 思想
### 1. yii  AR 方式操作数据库
> 复写CActiveRecord类，通过 beforeSave  beforeDelete  beforeFind  三种事件来动态的修改`dbconnection` 来实现读写分离

### 2. yii  PDO 方式操作数据库
> 在 配置文件中的 db 组件中 createCommand 通过指定新的 db class 属性， 通过复写的 `createCommand` 来实现读写分离