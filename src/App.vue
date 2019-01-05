<template>
  <div id="app">
    <h2>vuecmf-table demo</h2>
    <vc-table :add="true" :edit="true" :del="true" :selectable="selectable" :checkbox="true"  ref="vcTable"  :header-action="headerAction" :cell-event="cellEvent" :row-action="rowAction" server="http://www.b2b.com/api/Table/index" page="page" :limit="20"  :operate-width="200"></vc-table>

  </div>
</template>

<script>

export default {
  name: 'app',
  data () {
    return {
        selectable: function (row, index) {
            if(index % 2 == 0){
                return false;  //不允许勾选
            }else{
                return true; //可以勾选
            }

        },
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
                console.log('cellevent')
                console.log(currentList)
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
  mounted: function () {
      let that = this
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
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;

}

h1, h2 {
  font-weight: normal;
}

ul {
  list-style-type: none;
  padding: 0;
}

li {
  display: inline-block;
  margin: 0 10px;
}

a {
  color: #42b983;
}
</style>
