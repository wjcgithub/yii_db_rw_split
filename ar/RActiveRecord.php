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




    //查询
    public static function rCriteria($rCriteriaArray)
    {
        $rCriteria = new CDbCriteria();
        $rCriteria->select = $rCriteriaArray['select'];
        if (isset($rCriteriaArray['distinct']) && !empty($rCriteriaArray['distinct'])) {
            $rCriteria->distinct = $rCriteriaArray['distinct'];
        }
        if (isset($rCriteriaArray['join']) && !empty($rCriteriaArray['join'])) {
            $rCriteria->join = $rCriteriaArray['join'];
        }
        if (isset($rCriteriaArray['with']) && !empty($rCriteriaArray['with'])) {
            $rCriteria->with = $rCriteriaArray['with'];
        }
        if (isset($rCriteriaArray['together ']) && !empty($rCriteriaArray['together '])) {
            $rCriteria->together = $rCriteriaArray['together '];
        }
        if (isset($rCriteriaArray['condition']) && !empty($rCriteriaArray['condition'])) {
            $rCriteria->condition = $rCriteriaArray['condition'];
        }
        if (isset($rCriteriaArray['order']) && !empty($rCriteriaArray['order'])) {
            $rCriteria->order = $rCriteriaArray['order'];
        }
        if (isset($rCriteriaArray['group']) && !empty($rCriteriaArray['group'])) {
            $rCriteria->group = $rCriteriaArray['group'];
        }
        if (isset($rCriteriaArray['having']) && !empty($rCriteriaArray['having'])) {
            $rCriteria->having = $rCriteriaArray['having'];
        }
        if (isset($rCriteriaArray['params']) && !empty($rCriteriaArray['params'])) {
            $rCriteria->params = $rCriteriaArray['params'];
        }
        if (isset($rCriteriaArray['limit']) && !empty($rCriteriaArray['limit'])) {
            $rCriteria->limit = $rCriteriaArray['limit'];
        }
        return $rCriteria;
    }

    //获得分页信息
    public static function rPages($rCriteria, $pageSize = 10, $model = null)
    {
        if ($model == null) {
            $model = ucwords(Yii::app()->controller->id);
        }
        $rowCount = $model::model()->count($rCriteria);  //获得总条数
        $rPages = new CPagination($rowCount);
        $rPages->pageSize = $pageSize; //每页显示多少条
        $rPages->applyLimit($rCriteria);

        return $rPages;
    }

    //生成uuid和token
    public static function createGuid($namespace = '')
    {
        $guid = '';
        $uid = uniqid("", true);
        $data = $namespace;
        $data .= time();
        $data .= Yii::app()->request->userAgent;
        $data .= Yii::app()->request->userHostAddress;


        $hash = strtoupper(hash('ripemd128', $uid . md5($data)));
        $guid = substr($hash, 0, 8) .
            '-' .
            substr($hash, 8, 4) .
            '-' .
            substr($hash, 12, 4) .
            '-' .
            substr($hash, 16, 4) .
            '-' .
            substr($hash, 20, 12);

        return $guid;
    }

    /**
     * @param $fileType
     * @param $nid
     * @param $col
     * @param array $fidarr
     */
    function delExistFileByFid($fileType, $nid, $col, $fidarr = array(), $thumbWidth=array(), $thumbHeight=array())
    {
        if (count($fidarr) > 0) {
            $fileRelation = UploadFileRelation::model()->deleteAll(array(
                'select' => 'id, fid',
                'condition' => 'type=:type AND nid=:nid AND col=:col AND fid in (' . implode(',', $fidarr) . ')',
                'params' => array(':type' => $fileType, ':nid' => $nid, ':col' => $col),
            ));

            foreach ($fidarr as $key => $fid) {
                $uploadFile = UploadFile::model()->find(array(
                    'select' => 'id,uploadFileUrl,uploadFileName',
                    'condition' => 'id=:id',
                    'params' => array(':id' => $fid),
                ));
                if(is_object($uploadFile)){

                    $fileUrl = $_SERVER['DOCUMENT_ROOT'] . $uploadFile->uploadFileUrl .DIRECTORY_SEPARATOR. $uploadFile->uploadFileName;
                    Yii::log($fileUrl,'info','uploadFileDel');
                    //删除原文件
                    if (is_file($fileUrl)){
                        @unlink($fileUrl);
                    }

                    //删除缩略图
                    foreach ($thumbWidth as $key => $value) {
                        $fileUrl = $_SERVER['DOCUMENT_ROOT'] . $uploadFile->uploadFileUrl .DIRECTORY_SEPARATOR.'thumb'.DIRECTORY_SEPARATOR. $value.'x'.$this->thumbHeight[$key].DIRECTORY_SEPARATOR.$uploadFile->uploadFileName;
                        if (is_file($fileUrl)){
                            unlink($fileUrl);
                        }
                    }

                    //删除上传文件记录
                    if (!UploadFile::model()->deleteByPk($fid))
                        Yii::log('delete uploadFileRelation ' . $fid . ' failure', 'info', 'uploadFileDel');
                }
            }
        }
    }

    /**
     * 签名生成算法
     *
     * @param  array  $params API调用的请求参数集合的关联数组，不包含sign参数
     * @param  string $secret 签名的密钥.即平台约定的 rToken
     *
     * @return string 返回参数签名值
     */
    public function getSignature ( $params = array(), $secret = '' )
    {
        $str = '';
        ksort( $params, SORT_STRING );
        foreach ($params as $k => $v) {
            $str .= "{$k}={$v}";
        }
        $str .= $secret;

        return sha1( $str );
    }
}
