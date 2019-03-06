<?php
/**
 *
 * User: tulihua
 * Date: 2018/11/5
 * Time: 12:10
 */

namespace app\api\controller;


use think\Controller;
use think\Db;
use think\Exception;
use think\Request;
use app\home\helper\Cache;

header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:*');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with,content-type');

class Table extends Controller
{


    public function index(){

        try{
            $action = input('post.action');

            $table = 'base_info_log';

            //拉取字段信息
            if($action == 'getField'){
                $table_name = config('database.prefix').$table;
                $field_info = Db::query('select COLUMN_NAME as prop,column_comment as label,data_type,column_key,extra from INFORMATION_SCHEMA.Columns where table_name="'.$table_name.'" and table_schema = "'.config('database.database').'"');

                foreach ($field_info as &$val){
                    $val['width'] = 150; //列宽度
                    $val['data_form'] = true; //是否为数据表单
                    $val['validate'] = [
                        [ 'required' => true, 'message' => '表单必填', 'trigger' => 'blur' ]
                    ]; //表单验证规则

                    $val['sortable'] = true; //是否有排序功能
                    $val['show'] = true; //是否在列表中显示
                    $val['fixed'] = false; //是否固定列
                    $val['data_type'] = $val['data_type'] == 'varchar' ? 'string' : $val['data_type'];

                    if($val['column_key'] == 'PRI' && $val['extra'] == 'auto_increment'){
                        $val['data_type'] = 'hidden'; //隐藏域
                    }

                    /*if(in_array($val['prop'],['id','status'])){
                        $val['data_form'] = false;  //是否为表单
                    }*/

                    if($val['prop'] == 'inquiry_sheet_id'){
                        $val['data_type'] = 'hidden'; //隐藏域
                    }

                    //是否有筛选功能
                    if(in_array($val['data_type'],['datetime','date','string'])){
                        $val['filter'] = true;
                    }else{
                        $val['filter'] = false;
                        $val['show'] = false;
                    }

                    if(in_array($val['prop'],['sku_name','product_batch_sn','shelf_life_type','shelf_life','way_num_status'])){
                        $val['filter'] = false;
                    }

                    if($val['prop'] == 'erp_orders_sn'){
                        $val['fixed'] = true;
                        $val['tooltip'] =  '<span class="fee_purchase">2222 </span> ( 2344 )<br><span class="currency_rate">汇率：344</span>';
                    }

                    if(in_array($val['prop'],['master_bill_consignee','master_bill_notity_party','old_value'])){
                        $val['data_type'] = 'image';
                    }

                    if($val['prop'] == 'value'){
                        $val['data_type'] = 'editor';
                    }

                    //如果是文件类型，设置可上传扩展名
                    if($val['data_type'] == 'image'){
                        $val['extension'] = ['jpg','jpeg','png'];
                    }else if($val['data_type'] == 'file'){
                        $val['extension'] = ['zip','rar','xls','xlsx','doc','docx'];
                    }



                    if(in_array($val['prop'],['orders_status'])){
                        $val['data_type'] = 'select';
                        $val['default'] = '10';
                        $val['options'] = [
                            '-10' => '新建',
                            '-5' => '意向单审批中',
                            '1' => '有效意向单',
                            '2' => '审批中',
                            '3' => '审批通过',
                            '4' => '审批不通过',
                            '10' => '待确认',
                            '12' => '财务已审核',
                            '14' => '法务已审核',
                            '20' => '（财务预收款）已审核',
                            '30' => '部分已出货',
                            '31' => '已发货',
                            '40' => '订单取消',
                            '41' => '完结'
                        ];
                    }


                }
                return json([
                    'fields' => $field_info, //列字段信息
                ]);
            }

            //拉取列表数据
            $pageSize = input('post.pageSize',20);
            $filter = input('post.');


            $where = [];
            if(!empty($filter['filter'])){
                foreach ($filter['filter'] as $k => $v){
                    if(empty($v))  continue;
                    if(is_array($v)){
                        if(empty($v[0]) || empty($v[1])) continue;
                        $where[$k] = ['between',[$v[0],$v[1]]];
                    }else{
                        $where[$k] = $v;
                    }
                }
            }

            if(!empty($filter['keywords']) && !empty($filter['keywords_field'])){
                $where[implode('|',$filter['keywords_field'])] = ['like','%'.trim($filter['keywords']).'%'];
            }

            $res = Db::name($table)->where($where)->order(Db::name($table)->getPk(),'desc')->paginate($pageSize);
            $result = $res->toArray();


            foreach ($result['data'] as &$val){
                $val['old_value'] = 'https://o5wwk8baw.qnssl.com/a42bdcc1178e62b4694c830f028db5c0/avatar,https://o5wwk8baw.qnssl.com/bc7521e033abdd1e92222d733590f104/avatar';
               // $val['erp_orders_sn'] .= '<input type="text" class="form-control" value="22">';
                /*$val['expandData'] = [
                    'type' => 'table',
                    'tableFields' => [
                        ['label'=>'需求单号','prop'=>'order_sn','width'=> 200, 'data_type'=>'string'],
                        ['label'=>'调拨类型','prop'=>'type', 'data_type'=>'string'],
                        ['label'=>'状态','prop'=>'num','data_type'=>'select','options' => [
                            10 => '待执行',
                            20 => '已执行'
                        ]],
                    ],
                    'tableList' => [
                        ['order_sn'=> 'dbd903452','type' => 'POP','num' => 10],
                        ['order_sn'=> 'dbd123123','type' => 'POP','num' => 20],
                        ['order_sn'=> 'dbd144155','type' => 'POP','num' => 10],
                    ]
                ];*/
            }


            return json($result);
        }catch (Exception $e){
            echo $e->getMessage().', 在文件'.$e->getFile().' 第'.$e->getLine().'行';
        }
    }


    public function importData(){
        $data = input('post.data/a');
        return json($data);
    }


    public function test(){
        $currentUser = getAdminUser();
        dump($currentUser['name']);

    }


    public function upload(){
        $file = request()->file('file');
        $upload_base_url = 'http://www.b2b.com/upload/';
        $arr = [];

        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload');
            if($info){
                $arr = [
                    'code' => 200,
                    'url' => 'http://www.b2b.com/upload/20190306/8add65c597260ea8b8b67f5a9017d88a.png',
                    'name' => $info->getFilename(),
                    'size' => $info->getSize(),
                    'state' => 'SUCCESS'  //FAIL
                ];
            }else{
                $arr = [
                    'message' => $file->getError(),
                    'state' => 'FAIL'  //FAIL
                ];
            }
        }

        return json($arr);


    }



}