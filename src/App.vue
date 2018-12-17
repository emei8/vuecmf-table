<template>
  <div id="app">
    <h2>vuecmf-table demo</h2>
    <vc-table :selectable="selectable" :checkbox="true"  :header-action="headerAction" :cell-event="cellEvent" :row-action="rowAction" server="http://www.b2b.com/api/Table/index" page="page" :limit="20"  :operate-width="200"></vc-table>

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
        cellEvent: function () {
            alert('sss');
        },
        rowAction:[
            {
                event: function (index,row) {
                    console.log(index,row)
                },
                title: '编辑',
                type: 'success',
                icon: '',
                hideValue: ''
            },
            {
                event: function (index,row) {
                    console.log(index,row)
                },
                title: '删除',
                type: 'primary',
                icon: '',
                hideValue: ''
            }
        ]
    }
  }
}
</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
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
