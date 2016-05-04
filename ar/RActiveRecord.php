<?php

class RActiveRecord extends CActiveRecord
{
    //model配置
    public $modelConfig = '';
    //数据库配置
    public $dbConfig = '';
    //定义一个多数据库集合
    public static $dataBase = array();
    //当前数据库名称
    public $dbName = '';
    //定义库类型(读或写)
    public $dbType = 'read'; //'read' or 'write'
    //强制读主
    public $forceUseMaster = false;

    /**
     * 在原有基础上添加了一个dbname参数.
     *
     * @param string $scenario Model的应用场景
     * @param string $dbname   数据库名称
     */
    public function __construct($scenario = 'insert', $dbname = '')
    {
        if (!empty($dbname)) {
            $this->dbName = $dbname;
        }
        parent::__construct($scenario);
    }

    /**
     * 强制使用Master，为避免主库过大压力，请随用随关
     * 【注意】除非你有足够的理由，否则请勿使用.
     * @author: jichao.wang <braveontheroad@gmail.com>
     *
     * @param bool|FALSE $value
     */
    public function forceUseMaster($value = false)
    {
        $this->forceUseMaster = $value;
    }

    /**
     * 重写父类的getDbConnection方法
     * 多库和主从都在这里切换.
     */
    public function getDbConnection($dbtype='')
    {
        if(!empty($dbtype)){
            $this->dbType = $dbtype;
        }

        //如果指定的数据库对象存在则直接返回
        if (isset(self::$dataBase[$this->dbName]) && self::$dataBase[$this->dbName] !== null) {
            return self::$dataBase[$this->dbName];
        }
        if ($this->dbName == 'db') {
            self::$dataBase[$this->dbName] = Yii::app()->getDb();
        } else {
            //寻找真正的db
            $this->changeConn($this->dbType);
        }
        if (self::$dataBase[$this->dbName] instanceof CDbConnection) {
            self::$dataBase[$this->dbName]->setActive(true);
            return self::$dataBase[$this->dbName];
        } else {
            throw new CDbException(Yii::t('yii', 'Model requires a "db" CDbConnection application component.'));
        }
    }
    /**
     * 获取配置文件.
     *
     * @param unknown_type $type
     * @param unknown_type $key
     */
    private function getConfig($type = 'modelConfig', $key = '')
    {
        $config = Yii::app()->params[$type];
        if ($key) {
            $config = $config[$key];
        }

        return $config;
    }

    /**
     * 获取数据库名称 根据 modelConfig 配置文件
     *
     * @author: jichao.wang <braveontheroad@gmail.com>
     * @return int|string
     */
    private function getDbName()
    {
        $dbName = '';

        if ($this->dbName) {
            return $this->dbName;
        }
        $modelName = get_class($this->model());
        $this->modelConfig = $this->getConfig('modelConfig');

        //获取model所对应的数据库名
        if ($this->modelConfig) {
            foreach ($this->modelConfig as $key => $val) {
                if (in_array($modelName, $val)) {
                    $dbName = $key;
                    break;
                }
            }
        }

        //默认db 使用 db标识
        if(empty($dbName)){
            $this->dbName = 'db';
        }else{
            $this->dbName = $dbName;
        }

        return $dbName;
    }

    /**
     * 切换数据库连接，使用指定的dbName, 和 dbType(read|write)
     * @author: jichao.wang <braveontheroad@gmail.com>
     *
     * @param string $dbtype
     *
     * @return mixed
     * @throws CDbException
     */
    protected function changeConn($dbtype = 'read')
    {
        if ($this->dbType == $dbtype && isset(self::$dataBase[$this->dbName]) && self::$dataBase[$this->dbName] !== null) {
            return self::$dataBase[$this->dbName];
        }

        $this->getDbName();

        //判断是否注册到了系统中
        if (Yii::app()->getComponent($this->dbName.'_'.$dbtype) !== null) {
            self::$dataBase[$this->dbName] = Yii::app()->getComponent($this->dbName.'_'.$dbtype);

            return self::$dataBase[$this->dbName];
        }

        //获取db的配置
        $this->dbConfig = $this->getConfig('dbConfig', $this->dbName);
        //跟据类型取对应的配置(从库是随机值)
        if ($dbtype == 'write') {
//            $dbtype = 'write';
            $config = $this->dbConfig[$dbtype];
        } else {
            $slavekey = array_rand($this->dbConfig[$dbtype]);
            $config = $this->dbConfig[$dbtype][$slavekey];
        }

        //将数据库配置加到component中
        if ($dbComponent = Yii::createComponent($config)) {
            Yii::app()->setComponent($this->dbName.'_'.$dbtype, $dbComponent);
            self::$dataBase[$this->dbName] = Yii::app()->getComponent($this->dbName.'_'.$dbtype);
            $this->dbType = $dbtype;
            return self::$dataBase[$this->dbName];

        } else {
            throw new CDbException(Yii::t('yii', 'Model requires a "changeConn" CDbConnection application component.'));
        }
    }
    /**
     * 保存数据前选择 主 数据库.
     */
    protected function beforeSave()
    {
        parent::beforeSave();
        $this->changeConn('write');

        return true;
    }
    /**
     * 删除数据前选择 主 数据库.
     */
    protected function beforeDelete()
    {
        parent::beforeDelete();
        $this->changeConn('write');

        return true;
    }
    /**
     * 读取数据选择 从 数据库.
     */
    protected function beforeFind()
    {
        parent::beforeFind();
        $this->changeConn('read');

        return true;
    }
    /**
     * 获取master库对象
     */
    public function dbWrite()
    {
        return $this->changeConn('write');
    }
    /**
     * 获取slave库对象
     */
    public function dbRead()
    {
        return $this->changeConn('read');
    }
}
