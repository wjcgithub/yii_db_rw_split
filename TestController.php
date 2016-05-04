<?php

class testController extends CController {

    public function actiontest()
    {

        //测试活跃记录 (ar) 方式的读取
        $obj = T1::model()->findByPk( 1 );
        print_r( $obj->addTime );
        exit;

        //活跃记录（ar）方式写入
        $obj = new T1();
        $obj->name = 'rw_split';
        $obj->save();


        //活跃记录(ar)修改
        $obj = T1::model()->findByPk( array('id' => 1,) );
        $obj->name = 'hahahaha';
        $obj->update();


        //测试PDO模式的读取
        $connection = Yii::app()->db;
        $sql = "SELECT * FROM {{t1}}";
        $rows = $connection->createCommand( $sql )->query();
        foreach ($rows as $k => $v) {
            echo $v['id'] . '<br>';
        }

        //PDO 写入
        $connection = Yii::app()->db;
        $sql = 'insert into {{t1}} VALUES (null,"fdsafdsafds")';
        $rows = $connection->createCommand( $sql )->execute();


        // =================事务====================
        $db = Yii::app()->db;
        $dbTrans = $db->beginTransaction();

        try {
            $sql1 = 'insert into {{t1}} VALUES (null,"lisi1")';
            Yii::app()->db->createCommand( $sql1 )->execute();


            $sql2 = 'insert into {{t2}} VALUES (null,"lisi2")';
            Yii::app()->db->createCommand( $sql2 )->execute();

            $t1 = new T1();
            $t1->name = '张三';
            $t1->save();

            $t2 = new T2();
            $t2->name = '张三';
            $t2->save();

            $dbTrans->commit();
        } catch (Exception $e) {
            $dbTrans->rollback();
        }
    }
}
