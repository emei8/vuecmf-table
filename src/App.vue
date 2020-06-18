<template>
  <div id="app">
    <h2>vuecmf-table demo</h2>
    <vc-table
            :formTabs="formTabs"
            @on-add="add"
            @on-edit="edit"
            @on-del="del"
            :show-del-btn="true"
            :show-edit-btn="true"
            :show-add-btn="true"
            :checkbox="true"
            ref="vcTable"
            :cell-event="cellEvent"
            server="http://www.b2b.com/api/Table/index"
            import-server="http://www.b2b.com/api/Table/importData"
            upload-file-server="http://www.b2b.com/api/Table/upload"
            :upload-file-max-size="uploadFileMaxSize"
            :editor-config="editorConfig"
            page="page"
            :limit="20"
            :expand="false"
            :width="width"
            :height="height"
            :operate-width="120"
            :show-toolbar="true"
            :form-label-width="150"
            model-width="80%"
            @on-select="selectRow" >
      <template #headerAction>
        <i-radio-group v-model="animal">
          <i-radio label="金斑蝶"></i-radio>
          <i-radio label="爪哇犀牛"></i-radio>
          <i-radio label="印度黑羚"></i-radio>
        </i-radio-group>
      </template>

      <template #rowAction="slotProps" >
        <i-button type="primary"  @click="edit(slotProps.row)">编辑</i-button>
      </template>

    </vc-table>
  </div>
</template>

<script>

import VcTable from "./lib/vuecmf-table/table.vue";

import Vue from 'vue'
import ViewUI from 'view-design'
import 'view-design/dist/styles/iview.css';
Vue.use(ViewUI)

export default {
  components: {VcTable},
  name: 'app',
  data () {
    let that = this
    return {
        formTabs:[],
        editorConfig: {
            // 你的UEditor资源存放的路径,相对于打包后的index.html
            UEDITOR_HOME_URL: '/public/NEditor/',
            // 编辑器不自动被内容撑高
            autoHeightEnabled: false,
            // 初始容器高度
            initialFrameHeight: 300,
            // 初始容器宽度
            initialFrameWidth: '100%',
            // 关闭自动保存
            enableAutoSave: false
        },
        uploadFileMaxSize: 5120, //最大可上传文件大小
        animal:'金斑蝶',
        height: 300,
        width: 800,
        //针对自定义单元格内容的事件处理， 可借助jquery进行DOM操作和事件处理
        cellEvent:
            function (currentList) { //currentList 为当前页列表数据
                //console.log(currentList)
                currentList.forEach(function (val,index) {
                   // console.log(index)
                })
            }

    }
  },
    methods:{
        selectRow: function (selection, row) {
            console.log(selection)
            console.log(row)
        },
        add: function (form_data) {
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

  }
}
</script>

<style>



</style>
