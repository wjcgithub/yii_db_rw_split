<?php

class testController extends CController {

    /**
     * @author jichao.wang
     * @info 职位类型列表
     */
    public function actionList() {

        //测试活跃记录的读取
        $obj = TestModule::model()->findByPk(1);
        print_r($obj->addTime);
        exit;

        //测试PDO模式的读取
        $connection=Yii::app()->db;
        $sql="SELECT * FROM {{t1}}";
        $rows=$connection->createCommand ($sql)->query();
        foreach ($rows as $k => $v ){
            echo $v['id'].'<br>';
        }

        $connection=Yii::app()->db;
        $sql="SELECT * FROM {{t1}}";
        //        $sql = 'insert into {{t1}} VALUES (null,"fdsafdsafds")';
        $rows=$connection->createCommand ($sql)->query();
        foreach ($rows as $k => $v ){
            echo $v['id'].'<br>';
        }

        //        $connection=Yii::app()->db;
        //        $m = T1::model();
        //        $m->forceUseMaster(1);
        //        $connection = $m->getDbConnection();
        //        $sql="SELECT * FROM {{t1}}";
        ////        $sql = 'insert into {{t1}} VALUES (null,"fdsafdsafds")';
        //        $rows=$connection->createCommand ($sql)->query();
        //        foreach ($rows as $k => $v ){
        //            echo $v['id'].'<br>';
        //        }


        // =================事务====================
//        $db = Yii::app()->db;
//        $dbTrans = $db->beginTransaction();
//
//        try{
//            $sql1  = 'insert into {{t1}} VALUES (null,"lisi1")';
//            Yii::app()->db->createCommand($sql1)->execute();
//
//
//            $sql2  = 'insert into {{t2}} VALUES (null,"lisi2")';
//            Yii::app()->db->createCommand($sql2)->execute();
//
//            $t1 = new T1();
//            $t1->name = '张三';
//            $t1->save();
//
//            $t2 = new T2();
//            $t2->name = '张三';
//            $t2->save();
//
//            $dbTrans->commit();
//        }catch (Exception $e){
//            $dbTrans->rollback();
//        }


        //======================修改=====================
//        $userLimitRet = T1::model()->findByPk (array (
//            'id' => 1,
//        ));
//        $userLimitRet->name = 'hahahaha';
//        if (! $userLimitRet->update ()) {
//        }



//die;














        //判断权限
        $this->checkAccess('www_positionType_list', 'manager/welcome');

        //筛选
        $whereConArr = PositionType::whereFilter();

        //order排序
        $orderArr = PositionType::orderFilter();

        //分页查询职位类型信息
        $rCriteria = PositionType::rCriteria(array(
                    'select' => 't.id ,t.title, t.language, t.addTime,t.addId,t.sortFlag,t2.trueName as addName',
                    'join' => 'left join {{admin}} t2 on t.addId=t2.id ',
                    'condition' => $whereConArr['whereCon'],
                    'order' => $orderArr['order'],
                    'params' => $whereConArr['whereParams'],
        ));

        $rPages = PositionType::rPages($rCriteria, 20);

        $positionTypeInfo = PositionType::model()->findAll($rCriteria);

        //引入页面需要的静态文件（包括css及js等）
        $clientScript = Yii::app()->clientScript;
        $clientScript->registerCssFile($this->_assetUrl . '/css/topage.css');
        $clientScript->registerScriptFile($this->_assetUrl . '/js/topage.js', CClientScript::POS_END);
        $clientScript->registerScriptFile($this->_assetUrl . '/js/jquery.editable-1.3.3.js', CClientScript::POS_END);
        $clientScript->registerScriptFile($this->_assetUrl . '/js/editer.js', CClientScript::POS_END);

        $this->_title = '睿仁医疗后台管理系统-职位类型列表';
        $this->render('list', array('positionTypeInfo' => $positionTypeInfo, 'rPages' => $rPages, 'sortArg' => $orderArr['sortArg']));
    }

    /**
     * @author jichao.wang
     * @info 添加职位类型信息
     */
    function actionAdd() {
        $this->checkAccess('www_positionType_add', 'positionType/list');
        $positionType = new PositionType();
        //添加操作
        if (isset($_POST['PositionType'])) {
            //开始全局赋值
            $positionType->attributes = $_POST['PositionType'];
            $positionType->addId = Yii::app()->user->getId();
            $positionType->addTime = time();
            $positionType->sortFlag = 99;
            $positionType->status = '1';
            if ($positionType->save()) {
                Yii::app()->user->setFlash('success', '职位类型添加成功！');
                $this->redirect(Yii::app()->createUrl('positionType/list'));
            } else {
                $res = $positionType->hasErrors();
                if (empty($res)) {
                    Yii::app()->user->setFlash('error', '职位类型添加失败，请联系管理员！');
                    $this->redirect(Yii::app()->createUrl('positionType/list'));
                }
            }
        }

        $this->_title = '睿仁医疗后台管理系统-添加职位类型';
        $this->render('add', array('positionType' => $positionType));
    }

    /**
     * @param $id
     * @author jichao.wang
     * @info 更新职位类型信息
     */
    function actionUpdate($id) {
        //判断权限
        $this->checkAccess('www_positionType_update', 'positionType/list');
        $positionType = PositionType::model()->findByPk($id);
        //编辑操作
        if (isset($_POST['PositionType'])) {
            //开始全局赋值
            $positionType->attributes = $_POST['PositionType'];
            $positionType->updateId = Yii::app()->user->getId();
            $positionType->updateTime = time();
            if ($positionType->save()) {
                Yii::app()->user->setFlash('success', '职位类型编辑成功！');
                $this->redirect(Yii::app()->createUrl('positionType/list'));
            } else {
                $res = $positionType->hasErrors();
                if (empty($res)) {
                    Yii::app()->user->setFlash('error', '职位类型编辑失败，请联系管理员！');
                    $this->redirect(Yii::app()->createUrl('positionType/list'));
                }
            }
        }

        $this->_title = '睿仁医疗后台管理系统-修改职位类型';
        $this->render('update', array('positionType' => $positionType));
    }

    /**
     * @author jichao.wang
     * @info 删除职位类型
     */
    function actionDel() {
        //判断权限
        $this->checkAccess('www_positionType_del', 'positionType/list');

        if (isset($_POST['checkbox'])) {
            $ids = $_POST['checkbox'];
            $positionTypeModel = PositionType::model();
            foreach ($ids as $key => $value) {
                $positionType = $positionTypeModel->findByPk($value);
                $positionType->status = '2';
                $positionType->updateId = Yii::app()->user->getId();
                $positionType->updateTime = time();
                $positionType->save();
            }
            Yii::app()->user->setFlash('success', '职位类型删除成功！');
        } else {
            Yii::app()->user->setFlash('error', '无效的参数！');
        }

        $this->redirect(Yii::app()->createUrl('positionType/list'));
    }

    /**
     * @authro jichao.wang
     * @info 更新排序
     */
    function actionSort() {
        if (Yii::app()->user->checkAccess('www_positionType_sort')) {
            $id = $_GET['id'];
            $sort = $_GET['sort'];
            $article = PositionType::model()->findByPk($id);
            $article->sortFlag = $sort;
            if ($article->save()) {
                //save success
                $json = array('ret' => 1);
            } else {
                //return mysql insert error
                $json = array('ret' => 2, 'msg' => $article->getErrors());
            }
        } else {
            //access_none
            $json = array('ret' => 3, 'msg' => '您没有权限操作！');
        }
        echo json_encode($json);
        exit;
    }

}
