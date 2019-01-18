<template>
  <div id="app">
    <h2>vuecmf-table demo</h2>
    <vc-table :add="save" :edit="edit" :del="del"  :checkbox="true"  ref="vcTable"  header-component="radio"  :header-action="headerAction" :cell-event="cellEvent" :row-action="rowAction" server="http://www.b2b.com/api/Table/index" page="page" :limit="20"  :expand="true"  :width="width"  :height="height" :operate-width="120"  :show-toolbar="true"  :form-label-width="150" model-width="60%" >
      <template slot="headerAction">
        <i-radio-group v-model="animal">
          <i-radio label="金斑蝶"></i-radio>
          <i-radio label="爪哇犀牛"></i-radio>
          <i-radio label="印度黑羚"></i-radio>
        </i-radio-group>
      </template>

    </vc-table>
  </div>
</template>

<script>

import VcTable from "./lib/vuecmf-table/table.vue";

import Vue from 'vue'
import iView from 'iview'
import 'iview/dist/styles/iview.css';
Vue.use(iView)

export default {
  components: {VcTable},
  name: 'app',
  data () {
    let that = this
    return {
        animal:'金斑蝶',
        height: 300,
        width: 800,
        headerAction:[
            {
                event: function (selectRows) {
                    console.log(selectRows)
                },
                title: '测试',
                label: '测试',
                type: 'success',
                icon: 'fa fa-plus-circle'
            },
            {
                event: function (selectRows) {
                    console.log(selectRows)
                    alert('测试2')
                },
                title: '测试2',
                label: '测试2',
                type: 'primary',
                icon: 'fa fa-edit'
            }
        ],
        //针对自定义单元格内容的事件处理， 可借助jquery进行DOM操作和事件处理
        cellEvent:
            function (currentList) { //currentList 为当前页列表数据
                //console.log(currentList)
                currentList.forEach(function (val,index) {
                   // console.log(index)
                })
            }
        ,
        rowAction:[
            {
                event: function (index,row) {
                    console.log(index,row)
                },
                title: '编辑',
                type: 'success',
                icon: '',
                callback: function(index,row){  //自定义操作项内容
                    if(row.status == 10){
                        return false;  //返回false 则不替换操作按钮
                    }else{
                        return 'hello world'  //替换操作按钮内容
                    }
                }
            },
            {
                event: function (index,row) {
                    console.log(index,row)
                    that.test()
                },
                title: '删除',
                type: 'primary',
                icon: '',
                callback: function(index,row){  //自定义操作项内容

                }
            }
        ]
    }
  },
    methods:{
        save: function (form_data) {
            console.log(form_data)
            console.log('addddddd')
        },
        edit:function (form_data) {
            console.log(form_data)
            console.log('editdddddd')
        },
        del: function (form_data) {
            console.log(form_data)
            console.log('deldddddd')
        },
        test:function () {
            console.log('测试事件')
        }
    },
  mounted: function () {
      let that = this

      let current_width = document.documentElement.clientWidth - 10
      let current_height = document.documentElement.clientHeight - 150
      that.height = current_height
      that.width = current_width

      that.rowAction[1].callback = function (index,row) {

          //that.$set(that.$refs.vcTable.tableData[index],'callback_result','未关注');  //替换默认操作

          that.$set(that.$refs.vcTable.tableData[index],'callback_result',false);  //不替换默认操作

         /*
         //异常回调处理
         that.$refs.vcTable.post('http://www.b2b.com/api/table/index',{id:'11'}).then(
              resolve => {
                  //console.log('请求成功',resolve.data)

                  that.$set(that.$refs.vcTable.tableData[index],'callback_result','未关注');

                  //return Promise.resolve(resolve.data)
              },
              reject => {
                 // console.log('请求异常')
                 // return Promise.reject(reject)
              }
          );*/

      }

  }
}
</script>

<style>



</style>
