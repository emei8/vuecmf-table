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

            $table = 'erp_orders';

            //拉取字段信息
            if($action == 'getField'){
                $table_name = config('database')['prefix'].$table;
                $field_info = Db::query('select COLUMN_NAME as prop,column_comment as label,data_type,column_key,extra from INFORMATION_SCHEMA.Columns where table_name="'.$table_name.'" and table_schema = "'.config('database')['database'].'"');

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
                $val['erp_orders_sn'] .= '<input type="text" class="form-control" value="22">';
            }


            return json($result);
        }catch (Exception $e){
            echo $e->getMessage().', 在文件'.$e->getFile().' 第'.$e->getLine().'行';
        }
    }


    public function test(){
        $new_val = '8a8a';
        $old_val = '4wwww';
        $rate = $new_val / $old_val;

        dump($rate);
    }

}