<template>
    <div :style="'width:'+ width + 'px'">
        <i-row :gutter="10" v-if=" showToolbar !== false ">
            <i-col  :xs="24" :sm="8" :md="8" :lg="8"   class="btn-group">
                <i-button v-if="showAddBtn" @click="addForm"  type="primary"><i-icon type="md-add-circle" /> 添加</i-button>

                <slot name="headerAction"></slot>

            </i-col>
            <i-col :xs="24" :sm="16" :md="16" :lg="16"  class="table-tools">
                <i-row type="flex" justify="end">
                    <div class="search-input">
                        <i-input
                                placeholder="请输入内容"
                                v-model="keywords"
                                @on-change="search"
                                clearable>
                        </i-input>
                    </div>

                    <i-button type="default"  title="刷新" @click="refresh"><i-icon type="md-refresh" /></i-button>
                    <i-poptip
                            placement="bottom-end"
                            :width="filter_form_width"
                            v-model="filter_show"
                            >
                        <i-button type="default"  title="筛选" ><i-icon type="ios-funnel" /></i-button>
                        <div slot="content">
                            <i-form :inline="true" :model="filterForm"  ref="filterForm">
                                <div class="filter-form-content">
                                    <template v-for="(item,index) in fields_data" >
                                        <i-col :xs="24" :sm="12" :md="8" :lg="8" :xl="6"  v-if="item.filter">
                                        <i-form-item :label="item.title"  :prop="item.slot">
                                            <i-input v-model="filterForm[item.slot]" clearable  :placeholder="'请输入' + item.title" v-if=" item.data_type == 'string' "></i-input>
                                            <i-select :transfer="true"  @on-change="filter_show = true"  v-model="filterForm[item.slot]" filterable  placeholder="请选择" v-if=" item.data_type == 'select' ">
                                                <i-option v-for="(option_item,option_index) in item.options" :key="option_index" :value="option_index">{{ option_item }}</i-option>
                                            </i-select>

                                            <i-date-picker
                                                    v-if=" item.data_type == 'date' "
                                                    v-model="filterForm[item.slot]"
                                                    type="daterange"
                                                    format="yyyy-MM-dd"
                                                    placeholder=""
                                                    :transfer="true"
                                                    @on-change="(datetime) =>{ changeDatetime(datetime,'filter',item.slot)}"
                                                    @on-open-change="filter_show = true"
                                            >
                                            </i-date-picker>

                                            <i-date-picker
                                                    v-if=" item.data_type == 'datetime' "
                                                    v-model="filterForm[item.slot]"
                                                    type="datetimerange"
                                                    format="yyyy-MM-dd HH:mm:ss"
                                                    placeholder=""
                                                    :transfer="true"
                                                    @on-change="(datetime) =>{ changeDatetime(datetime,'filter',item.slot)}"
                                                    @on-open-change="filter_show = true"
                                            >
                                            </i-date-picker>

                                        </i-form-item>

                                        </i-col>
                                    </template>
                                </div>
                                <i-col>
                                <div style="text-align: right; margin: 0">
                                    <i-button  type="default" @click="restFilter">重置</i-button>
                                    <i-button type="primary"  @click="filter">确定</i-button>
                                </div>
                                </i-col>
                            </i-form>
                        </div>
                    </i-poptip>

                    <!--<el-button type="default" size="small" title="日历"><i class="fa fa-calendar"></i></el-button>
                    <el-button type="default" size="small" title="透视" @click="pivot"><i class="fa fa-table"></i></el-button>
                    <el-button type="default" size="small" title="图表"><i class="fa fa-bar-chart"></i></el-button>
                    <el-button type="default" size="small" title="看板"><i class="fa fa-th-large"></i></el-button>-->

                    <i-poptip placement="bottom-end" :width="200" >
                        <i-button type="default"  title="列">
                            <i-icon type="ios-apps" />
                        </i-button>
                        <div slot="content">
                            <i-checkbox-group v-model="checkList" class="dropdown-content" @on-change="fieldChange">
                                <template v-for="(item,index) in fields_data">
                                    <i-checkbox  :label="item.slot" >
                                        <span v-html="item.title"></span>
                                    </i-checkbox><br>
                                </template>
                            </i-checkbox-group>
                        </div>
                    </i-poptip>

                    <i-button type="default"  title="导入" @click="importModal = true"><i-icon type="md-cloud-upload" /></i-button>

                    <i-dropdown trigger="click" @on-click="downloadExport" :transfer="true"  placement="bottom-end">
                        <i-button type="default"  title="导出">
                            <i-icon type="md-download" />
                        </i-button>
                        <i-dropdown-menu slot="list">
                            <i-dropdown-item name="csv">CSV</i-dropdown-item>
                            <i-dropdown-item name="xlsx">MS-Excel</i-dropdown-item>
                            <i-dropdown-item name="xml">XML</i-dropdown-item>
                        </i-dropdown-menu>
                    </i-dropdown>


                </i-row>
            </i-col>
        </i-row>

        <i-table
                :data="tableData"
                :columns="columns"
                border
                @on-sort-change="sort"
                :loading="loading"
                :stripe="true"
                :height="height"
                :width="width"
                @on-select="currentSelect"
                @on-selection-change="getSelectRows"
                @on-select-all="selectAll"
                @on-select-all-cancel="selectAllCancel"
        >
            <!-- 表格行自定义 -->
            <template v-for="(item,index) in columns"  slot-scope="{ row }"   :slot="item.slot"  >
                <span  v-html="formatter(row,item.slot,item.data_type,item.options)"></span>
            </template>

            <!-- 操作列 -->
            <template slot-scope="{ row, index }" slot="action" >
                <template v-if="showEditBtn">
                    <i-button style="margin-right: 5px"
                              type="success"
                              size="small"
                              @click.native.prevent="editForm(index, row)" ><Icon type="ios-create" /> 编辑</i-button>
                </template>
                <template v-if="showDelBtn">
                    <i-button style="margin-right: 5px"
                              type="error"
                              size="small"
                              @click.native.prevent="delForm(row)" ><Icon type="md-trash" /> 删除</i-button>
                </template>

                <!-- 自定义行事件 -->
                <slot name="rowAction" :row="row"></slot>
            </template>

        </i-table>

        <div class="pagination">
            <i-page
                    @on-page-size-change="handleSizeChange"
                    @on-change="handleCurrentChange"
                    :current="currentPage"
                    :page-size="pageSize"
                    :total="total"
                    :page-size-opts="[5, 20, 30, 40,50,100,200,300,500,1000]"
                    :show-total="showTotal"
                    :show-elevator="showElevator"
                    :show-sizer="showSizer"
                    >
            </i-page>
        </div>

        <!-- 下载数据 -->
        <i-modal
                title="正在下载数据，请稍后..."
                v-model="downloadTips"
                width="30%"
                :closable="false"
                >
            <span class="ivu-tag-red">{{downloadError}}</span>
            <i-progress  :stroke-width="18" :percent="percentage"></i-progress>
            <div slot="footer"></div>
        </i-modal>

        <!-- 添加数据表单 -->
        <vc-form :token="token" :data-form-title="dataFormTitle"  :editor-config="editorConfig"   :model-width="modelWidth" :model-height="modelHeight"  :form-label-width="formLabelWidth" :data-form="dataForm" :rule-validate="ruleValidate" :upload-file-server="uploadFileServer" :upload-file-max-size="uploadFileMaxSize" :fields-data="fields_data" ref-name="addDataForm" ref="addDataDlg"  @on-save-data-form="saveAddDataForm"></vc-form>

        <!-- 修改数据表单 -->
        <vc-form :token="token" :data-form-title="dataFormTitle"  :editor-config="editorConfig"  :model-width="modelWidth"  :model-height="modelHeight"  :form-label-width="formLabelWidth" :data-form="editDataForm" :rule-validate="ruleValidate" :upload-file-server="uploadFileServer" :upload-file-max-size="uploadFileMaxSize" :fields-data="fields_data" ref-name="editDataForm" ref="editDataDlg"  @on-save-data-form="saveEditDataForm"></vc-form>


        <!-- 导入数据 -->
        <i-modal v-model="importModal" title="导入" >
            <i-row class="import-btn">
                <i-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12">
                    <i-button icon="md-cloud-download" type="success" @click="downloadTemplate" >下载模板</i-button>
                </i-col>
                <i-col :xs="24" :sm="12" :md="12" :lg="12" :xl="12">
                    <input type="file" ref="importExcelForm" class="file-form" @change="importExcel" >
                    <i-button icon="md-cloud-upload" type="primary" @click="triggerUpload">上传文件</i-button>
                </i-col>
            </i-row>
            <i-row>
                <i-col>
                    <i-progress  :stroke-width="18" :percent="importExcelPercentage"></i-progress>
                </i-col>
            </i-row>

            <template slot="footer">
                <i-button type="default"   @click="importModal = false">取消</i-button>
                <i-button type="primary"   @click="startImportData">开始</i-button>
            </template>
        </i-modal>


    </div>

</template>

<script>
    import Vue from 'vue'
    import axios from 'axios'
    import {jsonExport,jsonImport} from './jsonUtils'
    import expandRow from './table-expand.vue'
    import VcForm from './form.vue'
    import qs from 'qs'
    /*
    如果iview页面使用CDN外链接引入的话，则注释这段*/
    /*import iView from 'iview'
    import 'iview/dist/styles/iview.css';
    Vue.use(iView)*/

    export default {
        name:'vc-table',
        props:['importServer','formLabelWidth','modelWidth','modelHeight' ,'expand','showToolbar','cellEvent','checkbox','server','page','limit','width','height','operateWidth','showEditBtn','showDelBtn','showAddBtn','uploadFileServer','uploadFileMaxSize','editorConfig','isReady'],//头部按钮
        components:{VcForm},
        data() {
            return {
                //数据导入相关
                importModal: false, //是否显示导入对话框
                importExcelData: [], //导入的文件内容
                importExcelPercentage:0, //导入进度百分比
                importCurrentPage:0, //导入当前进度页

                //数据表单
                currentRowData:{}, //当前编辑行数据
                oldCurrentRowData:{},
                dataForm:{}, //添加表单数据
                editDataForm:{}, //编辑表单数据
                dataFormTitle:'添加',
                ruleValidate:{}, //表单数据验证
                token:'', //获取数据的令牌

                //筛选表单
                filterForm: {},
                filter_show: false, //筛选表单显示
                filter_form_width:0,
                keywords: '',
                keywords_field:[], //可模糊搜索字段

                //分页设置
                showTotal:true, //显示总数
                showElevator:true, //显示电梯，可以快速切换到某一页
                showSizer:true, //显示分页，用来改变page-size
                currentPage:1,
                pageSize:20,

                //导出设置
                exportData: [], //导出的数据
                exportFileType: 'xlsx', //导出的文件类型
                totalPages: 1, //总页数
                downloadTips: false, //下载进度提示框的显示与隐藏
                percentage:0, //下载进度
                downloadError:'', //下载错误提示
                loading:false,
                downloadCurrentPage:0,

                //表格相关
                tableData: [],
                fields_data:[], //字段信息
                checkList: [], //可显示的列属性
                total: 0,
                orderField : '',
                orderSort: 'desc',
                columns : [], //显示的列信息
                selectRows : [], //已选择行数据
                currentSelectRow : {}, //当前选择行数据

            }
        },
        mounted(){
            let that = this
            that.pageSize = that.limit
            that.loadTableField()
            that.refresh()
            that.resizeWin()
            window.onresize = () => {
                that.resizeWin()
            }

        },
        updated() {
            let that = this
            //执行外部传入的事件
            if(that.$props.cellEvent != undefined && that.$props.cellEvent != '' && that.$props.cellEvent != null){
                let callfun = that.$props.cellEvent
                callfun(that.tableData)
            }
        },
        methods: {
            getUrlParam: function (url,paraName){
                let arrObj = url.split("?");

                if (arrObj.length > 1) {
                    let arrPara = arrObj[1].split("&");
                    let arr;

                    for (let i = 0; i < arrPara.length; i++) {
                        arr = arrPara[i].split("=");

                        if (arr != null && arr[0] == paraName) {
                            return arr[1];
                        }
                    }
                    return "";
                }
                else {
                    return "";
                }
            },
            //格式化日期
            dateFormat(dateObj,fmt){
                let obj = {
                    "m+" : dateObj.getMonth() + 1,                 //月份
                    "d+" : dateObj.getDate() - 1,                    //日
                    "H+" : dateObj.getHours(),                   //小时
                    "i+" : dateObj.getMinutes(),                 //分
                    "s" : dateObj.getSeconds(),                 //秒
                };
                if(/(Y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (dateObj.getFullYear()+""));
                for(let k in obj){
                    if(new RegExp("("+ k +")").test(fmt)){
                        fmt = fmt.replace(RegExp.$1, obj[k] < 10 ? "0" + obj[k] : obj[k]);
                    }
                }
                return fmt;
            },
            //触发上传事件
            triggerUpload(){
                this.importExcelPercentage = 0
                this.importCurrentPage = 0
                this.$refs.importExcelForm.click()
            },
            //上传导入EXCEL
            importExcel(fileForm){
                if(fileForm.target.files[0].type != 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' && fileForm.target.files[0].type != 'application/vnd.ms-excel'){
                    this.$Message.error('上传文件类型错误！只能上传文件xlsx,xls类型文件');
                    return false
                }
                jsonImport(fileForm.target,this.callbackUploadExcelData)
            },
            //获取上传的EXCEL数据的回调函数
            callbackUploadExcelData(file_data){
                let that = this
                if(file_data.length == 0) return false

                let newData = []

                file_data.forEach(function (v,k) {
                    let item_data = {}
                    that.columns.forEach(function (val, key) {
                        if(val['title'] != undefined && val['slot'] != 'action'){
                            let new_val = v[val['title']]
                            if((val['data_type'] == 'datetime' || val['data_type'] == 'date') && new_val > 40000 && new_val < 90000){
                                new_val = that.dateFormat(new Date(1900, 0, new_val),'Y/m/d H:i:s')  //如果日期变成类似42747 则用这种方式转换
                            }
                            if(val['options'] != undefined && val['options'] != ''){
                                val['options'].forEach(function(sv,sk){
                                    if(sv == new_val){
                                        new_val = sk
                                    }
                                })
                            }
                            item_data[val['slot']] = new_val
                        }
                    })
                    newData[k] = item_data
                })

                this.importExcelData = newData
            },
            //开始导入数据
            startImportData(){
                let that = this

                //每次处理20条
                let pageNum = 20
                let pages = Math.ceil(that.importExcelData.length / pageNum)

                if(that.importCurrentPage >= pages) return false

                let post_data = that.importExcelData.slice(that.importCurrentPage * pageNum,(that.importCurrentPage + 1) * pageNum)

                if(post_data != '' && post_data != null && post_data.length != 0){
                    that.post(that.importServer,{data:post_data}).then(function(data){
                        if(data.status == 200){
                            that.importExcelPercentage = Math.ceil((that.importCurrentPage + 1) / pages * 100)
                        }
                        that.importCurrentPage ++
                        that.startImportData()
                    })
                }

            },
            //下载模板文件
            downloadTemplate(){
                let tpl_data = []
                let item = []
                //将下载的字段名替换成表格的表头名称
                this.columns.forEach(function (v,k) {
                    if(v['slot'] != 'action' && v['slot'] != undefined){
                        //过滤HTML标签
                        let label = v['title'].replace(/<[^>]*>/g,'')
                        item[label] = ''
                    }
                });
                tpl_data.push(item)
                jsonExport(tpl_data,'xlsx','数据模板')
            },
            //处理日历控件数据
            changeDatetime: function (datetime, formType ,prop) {
                if(formType == 'filter'){
                    this.filterForm[prop] = datetime
                    this.filter_show = true
                }
            },
            //添加表单
            addForm: function () {
                //this.$refs['addDataDlg'].$refs['addDataForm'].resetFields()
                this.dataFormTitle = '添加'
                this.$refs['addDataDlg'].dataFormShow = true
                console.log(this.$refs)
                this.$refs['addDataDlg'].editorCfg.zIndex = 1800
            },
            //修改表单
            editForm: function (index, row) {
                this.dataFormTitle = '修改'
                this.editDataForm = row
                this.$refs['editDataDlg'].dataFormShow = true

                this.$refs['editDataDlg'].editorCfg.zIndex = 1800

                console.log(row)
                console.log(this.fields_data)
                let that = this
                this.fields_data.forEach(function (v,k) {
                    if(v['data_type'] == 'image' || v['data_type'] == 'img' || v['data_type'] == 'file'){
                        let file = row[v['slot'] + '_file_info']
                        if(file != ''){
                            let file_list = []
                            file.forEach(function (val,key) {
                                file_list.push({
                                    full_url: val.full_url,
                                    status: val.status,
                                    name: val.name,
                                })
                            })
                            that.$refs['editDataDlg'].$refs[v['slot']][0].uploadList = file_list
                        }

                    }
                })
            },
            //删除行数据
            delForm(row){
                this.$emit('on-del',row)
            },
            //保存添加数据表单
            saveAddDataForm: function (data) {
                this.$emit('on-add',data)
            },
            //保存修改的数据表单
            saveEditDataForm: function (data) {
                this.$emit('on-edit',data)
            },
            post: function (url,data) {
                let config = {
                    headers : {
                        'Content-Type':'application/x-www-form-urlencoded'
                    },
                };
                return axios.post(url, qs.stringify(data),config)
            },
            get: function (url) {
                return axios.get(url)
            },
            //格式化行数据
            formatter:function (row, field_name, data_type, options) {
                let cellValue = row[field_name]

                if((data_type == 'switch' || data_type == 'select' || data_type == 'radio') && options != '' && options != undefined){
                    cellValue = options[cellValue]
                }else if(data_type == 'image' || data_type == 'img'){
                    let img = ''
                    row[field_name + '_file_info'].forEach(function (v,k) {
                        img = img + '<img src="' + v.full_url + '" style="width:60px" /> '
                    })
                    cellValue = img
                }else if(data_type == 'url' || data_type == 'file'){
                    let file = ''
                    row[field_name + '_file_info'].forEach(function (v,k) {
                        file = file + '<a href="' + v.full_url + '" target="_blank">' + v.url + '</a> '
                    })
                    cellValue = file
                }

                if(row[field_name + '-html'] != undefined){
                    cellValue = row[field_name + '-html']
                }

                return cellValue
            },
            //当前选择行数据
            currentSelect:function (selection, row) {
                this.currentSelectRow = row
                this.$emit('on-select',selection, row)
            },
            //获取所选行数据
            getSelectRows:function (selection) {
                this.selectRows = selection
                this.$emit('on-selection-change',selection)
            },
            //取消全选
            selectAllCancel:function (selection) {
                this.$emit('on-select-all-cancel',selection)
            },
            //全选
            selectAll:function (selection) {
                this.selectRows = selection
                this.$emit('on-select-all',selection)
            },
            cellFun: function (callfun,index,row) {
                //调用外部函数
                callfun(index,row)
            },

            pivot: function () {
                /*this.$exportExcel({
                    url: 'http://www.b2b.com/api/Table/index'
                })*/
            },

            //拉取数据
            pullData: function (currentPage,callback,action) {
                let that = this
                let url = that.server

                if(typeof that.isReady == "function"){
                    that.isReady(that)
                }

                that.token = that.getUrlParam(url,'token')

                if(action != undefined && action == 'getField'){  //拉取表格字段信息
                    that.post(url,{
                        action: action
                    }).then(function (data) {
                        callback(data.data)
                    })
                }else{  //拉取列表数据
                    let post_url = url + '?'+ that.page + '=' + currentPage
                    if(url.indexOf('?') != -1){
                        post_url = url + '&'+ that.page + '=' + currentPage
                    }

                    that.post(post_url,{
                        pageSize: that.pageSize,
                        orderField: that.orderField,
                        orderSort: that.orderSort,
                        keywords: that.keywords,
                        keywords_field: that.keywords_field,
                        filter: that.filterForm,
                        //兼容后端只接收offset 和 limit 参数分页处理
                        offset: that.pageSize * (currentPage - 1),
                        limit: that.pageSize
                    }).then(function (data) {
                        callback(data.data)
                    })
                }
            },

            //重置筛选
            restFilter: function () {
                this.$refs['filterForm'].resetFields()
            },
            //筛选
            filter: function () {
                this.filter_show = false
                this.refresh()
            },
            //加载表格字段回调
            updateTableField: function (data) {
                let that = this
                that.columns = []
                that.fields_data = []
                //可多选列
                if(that.checkbox == true){
                    that.columns.push({
                        type: 'selection',
                        width: 50,
                        align: 'center',
                        fixed: true
                    })
                }


                //展开功能
                if(that.expand){
                    that.columns.push({
                        type: 'expand',
                        width: 50,
                        render: (h, params) => {
                            return h(expandRow, {
                                props: {
                                    row: params.row.expandData
                                }
                            })
                        }
                    })
                }


                //主体字段
                data.data.fields.forEach(function (val,key) {
                    let filterName = val['prop']
                    if(val['filterName'] != '' &&  val['filterName'] != undefined){
                        filterName = val['filterName']
                    }

                    if(val['data_type'] == 'string'){
                        that.keywords_field.push(filterName)
                    }
                    if(val['filter'] == true){
                        //由于搜索表单中v-model 是动态绑定，这里必须使用 Vue.$set(obj, key, val) 方式设置 v-model要绑定的数据，动态绑定才生效
                        that.$set(that.filterForm,filterName,'')
                        //that.filterForm[val['prop']] = ''  //这种生成的v-model的数据 动态绑定v-model是不生效的
                    }

                    if(val['data_form'] == true){
                        let defalut_val = '';
                        if(val['default'] != undefined) defalut_val = val['default']
                        if(typeof defalut_val == "string") defalut_val = defalut_val.trim()
                        that.$set(that.dataForm,val['prop'],defalut_val)
                        if(val['validate'] != undefined && val['validate'].length != undefined){
                            that.$set(that.ruleValidate,val['prop'],val['validate'])
                        }

                    }

                    let col = {
                        title: val['label'],
                        //key: val['prop'],
                        slot: val['prop'],
                        fixed: val['fixed'],
                        sortable: val['sortable'],
                        renderHeader: (h,params) => {
                            if(params.column.tips){
                                return h('span',[
                                    h('Tooltip',{props:{placement:"bottom",maxWidth:240}},[
                                        h('Icon',{props:{type:"md-help-circle"}}),
                                        h('div',{slot:"content"},[
                                            h('div',{style:{fontWeight:300},domProps:{innerHTML:params.column.tips}})
                                        ])
                                    ]),
                                    h('span',{
                                            props:{}, //属性
                                            style:{ fontSize:"12px"}, //样式
                                            on:{ //事件绑定
                                                /*'on-click':(e)=>{
                                                    console.log(e.target.value)
                                                }*/
                                            },
                                            domProps:{innerHTML:params.column.title}  //支持HTML
                                        }
                                    )
                                ])
                            }else{
                               // return h('span',{style:{ fontSize:"12px"}},params.column.title)
                                return h('span',{style:{ fontSize:"12px"},domProps:{innerHTML:params.column.title}})
                            }

                        },

                        data_type: val['data_type'],
                        width: val['width'],
                        tips: val['tooltip'],
                        options: val['options'],
                        filter: val['filter'],
                        data_form: val['data_form'],
                        extension: undefined != val['extension'] ? val['extension'] : '',
                        filter_field: val['filter_field'],
                        filter_form_name: val['filter_form_name'],
                        field_id: val['field_id'],
                        filter_url: val['filter_url']
                    }

                    if(that.checkbox == true){
                        col['_checked'] = val['checked'] == undefined ? false : val['checked']
                    }

                    col['_disabled'] = val['disabled'] == undefined ? false : val['disabled'];

                    that.fields_data.push(col)
                    if(val['show'] == true){
                        that.checkList.push(val['prop'])
                        that.columns.push(col)
                    }
                })


                //操作列
                if((that.rowAction != undefined  && that.rowAction.length > 0) || that.showAddBtn || that.showEditBtn || that.showDelBtn){
                    that.columns.push({
                        title: '操作',
                        slot: 'action',
                        width: that.operateWidth,
                        align: 'center',
                        fixed:'right',
                        renderHeader:(h,params) => {
                            return h('span',{style:{ fontSize:"12px"}},params.column.title)
                        }
                    })
                }

                if(typeof that.isReady == "function"){
                    that.isReady(that)
                }

            },
            //加载表格字段信息
            loadTableField: function () {
                this.pullData(1,this.updateTableField,'getField')
            },
            //搜索
            search: function () {
                this.refresh()
            },
            //拉取要导出的数据的回调
            getExportData: function (data) {
                if(data.data.data == undefined){
                    this.downloadError = '接口异常，无法拉取数据！';
                    return false
                }

                let that = this
                data.data.data.forEach(function (val, index) {
                    let item = []
                    //将下载的字段名替换成表格的表头名称
                    that.columns.forEach(function (v,k) {
                        if(v['slot'] != 'action' && v['slot'] != undefined){
                            //过滤HTML标签
                            let label = v['title'].replace(/<[^>]*>/g,'')
                            let value = val[v['slot']]
                            if(v['options'] != undefined && v['options'] != ''){
                                value = v['options'][value]
                            }
                            item[label] = value
                        }
                    });
                    that.exportData.push(item)
                })

                this.percentage = Math.ceil(this.downloadCurrentPage / this.totalPages * 100)

                //全部拉取完后，开始下载数据
                if(this.downloadCurrentPage == this.totalPages){
                    this.downloadTips = false
                    this.downloadError = ''
                    this.percentage = 0
                    this.downloadCurrentPage = 0
                    jsonExport(this.exportData,this.exportFileType)
                    this.exportData = []
                }else{
                    this.downloadExport(this.exportFileType)
                }

            },
            //下载导出
            downloadExport: function (type) {
                if(this.total == 0){
                    this.$Message.error('列表无数据！');
                    return false
                }
                this.downloadTips = true
                this.exportFileType = type
                this.downloadCurrentPage ++
                //逐页拉取数据
                this.pullData(this.downloadCurrentPage,this.getExportData)
            },
            //刷新
            refresh: function () {
                this.loading = true
                this.pullData(this.currentPage,this.getList)

                //处理选择分类进入列表后，表单如有select，联动的表单没有进行过滤问题
                let that = this
                that.fields_data.forEach(function (item,index) {
                    if(item.data_type == 'select'){
                        that.$refs['addDataDlg'].selectEvent(item,that.$refs['addDataDlg'].dataForm[item.slot],false)
                    }
                })

            },
            //改变窗口大小
            resizeWin: function () {
                //如果页数不够page-count，sizes 将不会显示
                if(document.body.offsetWidth < 768){
                    this.showTotal = false
                    this.showElevator = false
                    this.showSizer = false
                    this.label_width = 120
                }else{
                    this.showTotal = true
                    this.showElevator = true
                    this.showSizer = true
                    this.label_width = 80
                }

                this.filter_form_width = document.body.offsetWidth * 0.8
            },
            //列的显示与隐藏
            fieldChange(check_val){
                let that = this
                //将已选择的加入显示列
                that.fields_data.forEach(function (val,k) {
                    if(check_val.indexOf(val.slot) != -1){
                        console.log(val.slot)
                        let flag = true
                        //检测是否已经存在
                        that.columns.forEach(function (val2,k2) {
                            if(val.slot == val2.slot){
                                flag = false
                            }
                        })
                        if(flag == true)  that.columns.push(val)
                    }else{
                        //检测是否已经存在
                        that.columns.forEach(function (val2,k2) {
                            if(val.slot == val2.slot){
                                that.columns.splice(k2,1)
                            }
                        })
                    }
                })
            },
            //列排序
            sort(column){
                if(column.key == undefined){
                    this.orderField = column.column.slot
                }else{
                    this.orderField = column.key
                }

                this.orderSort = column.order
                this.refresh()
            },
            //拉取列表数据的回调
            getList(data){
                if(data.data.data == undefined){
                    let msg = '接口异常，无法拉取数据！'
                    if(data.data.message != undefined) msg = msg + data.data.message
                    if(data.data.msg != undefined) msg = msg + data.data.msg
                    this.$Message.error(msg);
                    return false
                }
                this.tableData = data.data.data
                this.total = parseInt(data.data.total)
                this.totalPages = Math.ceil(this.total / this.pageSize)
                this.loading = false



            },
            //每页显示条数修改
            handleSizeChange(val) {
                this.pageSize = val
                this.refresh()
            },
            //当前页修改
            handleCurrentChange(val) {
                this.currentPage = val
                this.refresh()

            }
        }
    }
</script>

<style scoped="true" >
    .filter-form-content{ max-height:460px; display: block; overflow-y: auto;}
    .dropdown-content{ max-height: 260px; padding: 10px; overflow-y: auto; max-width: 500px; overflow-x: auto }
    .ivu-col{ margin-bottom: 10px;}
    .filter-form-content .ivu-col{ margin-bottom: 0;}
    .filter-form-content .ivu-form-item{ margin-bottom: 10px;}
    .table-tools .ivu-btn{ margin-left: -1px !important;  border-radius: 0px; height: 32px;}
    .table-tools .ivu-dropdown:last-child .ivu-btn{ border-top-right-radius: 4px !important; border-bottom-right-radius: 4px !important;  }
    .pagination{ margin-top: 10px; font-size: 12px;}
    .table-tools .ivu-btn-default:hover{ border-color: #dcdee2}

    .vertical-center-modal{
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .vertical-center-modal .ivu-modal{
        top: 0;
    }

    .import-btn .ivu-col{
        text-align: center;
    }

</style>

<style>
    .table-tools .search-input .ivu-input{ border-top-right-radius: 0 !important; border-bottom-right-radius: 0 !important; }
    .ivu-table-cell{ padding-left: 10px !important; padding-right: 10px !important;}
    .file-form{ display: none; }
</style>

