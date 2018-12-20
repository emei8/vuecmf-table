<template>
    <div>
        <el-row :gutter="10" >
            <el-col :xs="24" :sm="8" :md="8" :lg="8" :xl="8"  class="btn-group">
                <el-button-group v-if="headerAction">
                    <el-button v-for="(item,k) in headerAction" size="small" :type="item.type" @click="fun(item.event)"  :title="item.title"><i :class="item.icon"></i> {{ item.label }}</el-button>
                </el-button-group>
            </el-col>
            <el-col :xs="24" :sm="16" :md="16" :lg="16" :xl="16" class="table-tools">
                <el-row type="flex" justify="end">

                    <el-input size="small"
                            placeholder="请输入内容"
                            v-model="keywords"
                            @change="search"
                            clearable>
                    </el-input>

                    <el-button type="default" size="small" title="刷新" @click="refreshList"><i class="fa fa-refresh"></i></el-button>
                    <el-popover
                            placement="bottom"
                            v-model="filter_show"
                            :width="filter_form_width"
                            >
                        <el-form :inline="true" :model="filterForm"  ref="filterForm"  class="filter-form-inline " size="small">
                            <div class="filter-form-content">
                                <template v-for="(item,index) in columns" >
                                    <el-col :xs="24" :sm="12" :md="8" :lg="8" :xl="6"  v-if="item.filter">
                                        <el-form-item :label="item.label"  :prop="item.prop">
                                            <el-input v-model="filterForm[item.prop]" :placeholder="'请输入' + item.label" v-if=" item.data_type == 'string' "></el-input>
                                            <el-select  v-model="filterForm[item.prop]" filterable  placeholder="请选择" v-if=" item.data_type == 'select' ">
                                                <el-option
                                                        v-for="(option_item,option_index) in item.options"
                                                        :key="option_index"
                                                        :label="option_item"
                                                        :value="option_index">
                                                </el-option>
                                            </el-select>

                                            <el-date-picker
                                                    v-if=" item.data_type == 'date' "
                                                    v-model="filterForm[item.prop]"
                                                    type="daterange"
                                                    value-format="yyyy-MM-dd HH:mm:ss"
                                                    >
                                            </el-date-picker>

                                            <el-date-picker
                                                    v-if=" item.data_type == 'datetime' "
                                                    v-model="filterForm[item.prop]"
                                                    type="datetimerange"
                                                    value-format="yyyy-MM-dd HH:mm:ss"
                                                    >
                                            </el-date-picker>

                                        </el-form-item>

                                    </el-col>
                                </template>
                            </div>
                            <el-col>
                                <div style="text-align: right; margin: 0">
                                    <el-button size="mini" type="default" @click="restFilter">重置</el-button>
                                    <el-button type="primary" size="mini" @click="filter">确定</el-button>
                                </div>
                            </el-col>
                        </el-form>

                        <el-button type="default" size="small" title="筛选" slot="reference"><i class="fa fa-filter"></i></el-button>
                    </el-popover>

                    <!--<el-button type="default" size="small" title="日历"><i class="fa fa-calendar"></i></el-button>
                    <el-button type="default" size="small" title="透视" @click="pivot"><i class="fa fa-table"></i></el-button>
                    <el-button type="default" size="small" title="图表"><i class="fa fa-bar-chart"></i></el-button>
                    <el-button type="default" size="small" title="看板"><i class="fa fa-th-large"></i></el-button>-->

                    <el-dropdown trigger="click" >
                        <el-button type="default" size="small" title="列">
                            <i class="fa fa-th"></i>
                        </el-button>
                        <el-dropdown-menu slot="dropdown" >
                            <el-checkbox-group v-model="checkList" class="dropdown-content" @change="fieldChange">
                                <template v-for="(item,index) in columns">
                                    <el-checkbox  :label="item.label" >
                                        <span v-html="item.label"></span>
                                    </el-checkbox><br>
                                </template>
                            </el-checkbox-group>
                        </el-dropdown-menu>
                    </el-dropdown>

                    <el-dropdown trigger="click" @command="downloadExport">
                        <el-button type="default" size="small" title="导出">
                            <i class="fa fa-sign-out"></i>
                        </el-button>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item command="csv">CSV</el-dropdown-item>
                            <el-dropdown-item command="xlsx">MS-Excel</el-dropdown-item>
                            <el-dropdown-item command="xml">XML</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>

                </el-row>


            </el-col>
        </el-row>

        <el-table
                :data="tableData"
                border
                style="width: 100%"
                size="small"
                @sort-change="sort"
                v-loading="loading"
                :stripe="true"
                :height="height"
                @select="currentSelect"
                @selection-change="getSelectRows"
        >
            <el-table-column v-if="checkbox"
                    fixed
                    type="selection"
                    :selectable="selectable"
                    width="50">
            </el-table-column>
            <template v-for="(item,index) in columns">
                <el-table-column v-if="item.show"
                        :prop="item.prop"
                        :label="item.label"
                        :sortable="item.sortable"
                        :fixed= "item.fixed"
                        :width="item.width"
                        >

                    <template slot="header" slot-scope="scope">
                        <el-tooltip v-if="item.tooltip" placement="bottom" effect="light">
                            <i class="el-icon-question"></i>
                            <div slot="content" >
                                <span v-html="item.tooltip"></span>
                            </div>
                        </el-tooltip>
                        <span v-html="item.label" style="font-size: 14px;">
                        </span>
                        <!--<div><el-input
                                v-model="filterForm[item.prop]"
                                size="mini"
                                placeholder="输入关键字搜索"/>
                        </div>-->
                    </template>

                    <template slot-scope="scope">
                        <span  v-html="formatter(scope.row,item.prop,item.data_type,item.options)"></span>
                    </template>

                </el-table-column>
            </template>

            <el-table-column
                    v-if="rowAction"
                    fixed="right"
                    label="操作"
                    :width="operateWidth"
                    >
                <template slot-scope="scope" >
                    <span v-for="(item,k) in rowAction">
                        <template v-if=" item.callback == undefined || item.callback(scope.$index, scope.row) == false || scope.row.callback_result == false ">
                            <el-button style="margin-right: 5px"
                                       size="mini"
                                       :type="item.type"
                                       @click.native.prevent="rowFun(item.event,scope.$index, scope.row)" ><i :class="item.icon"></i> {{item.title}}</el-button>
                        </template>
                        <template v-else>
                            <span v-html="scope.row.callback_result"></span>
                        </template>

                    </span>

                </template>
            </el-table-column>


        </el-table>

        <div class="pagination">
            <el-pagination
                    @size-change="handleSizeChange"
                    @current-change="handleCurrentChange"
                    :current-page="currentPage"
                    :page-sizes="[5, 20, 30, 40,50,100,200,300,500,1000]"
                    :page-size="pageSize"
                    :layout="pageLayout"
                    :pager-count="5"
                    :total="total">
            </el-pagination>
        </div>


        <el-dialog
                title="正在下载数据，请稍后..."
                :visible.sync="downloadTips"
                width="30%"
                :close-on-click-modal="false"
                :close-on-press-escape="false"
                :show-close="false"
                >
            <span class="el-tag--danger">{{downloadError}}</span>
            <el-progress :text-inside="true" :stroke-width="18" :percentage="percentage"></el-progress>
        </el-dialog>


    </div>

</template>

<script>
    import Vue from 'vue'
   // import 'font-awesome/css/font-awesome.min.css'
    import axios from 'axios'
    import jsonExport from './jsonExport'
    /*
    如果elementUI页面使用CDN外链接引入的话，则注释这段
    import ElementUI from 'element-ui'
    import 'element-ui/lib/theme-chalk/index.css'
    Vue.use(ElementUI)
    */

    export default {
        name:'vc-table',
        props:['selectable','cellEvent','checkbox','headerAction','rowAction','server','page','limit','height','operateWidth'],//头部按钮
        data() {
            return {
                //筛选表单
                filterForm: {},
                filter_show: false, //筛选表单显示
                filter_form_width:0,
                keywords: '',
                keywords_field:[], //可模糊搜索字段

                checkList: [], //列显示

                //分页设置
                pageLayout: 'total, sizes, prev, pager, next, jumper',
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

                tableData: [],
                total: 0,
                orderField : '',
                orderSort: 'desc',
                columns : [],
                selectRows : [], //已选择行数据
                currentSelectRow : {} //当前选择行数据
            }
        },
        mounted(){
            this.pageSize = this.limit
            this.loadTableField()
            this.refreshList()
            this.resizeWin()
            window.onresize = () => {
                this.resizeWin()
            }



        },
       /* watch:{
            filterForm: function(newFilterForm){
                this.$refs['filterForm'].resetFields();
                console.log(newFilterForm)
            }
        },*/
        updated() {

        },
        methods: {
            post: function (url,data) {
                return axios.post(url, data)
            },
            get: function (url) {
                return axios.get(url)
            },
            formatter:function (row, field_name, data_type, options) {
                let cellValue = row[field_name]

                if((data_type == 'switch' || data_type == 'select') && options != '' && options != undefined){
                    cellValue = options[cellValue]
                }else if(data_type == 'image'){
                    cellValue = '<img src="' + cellValue + '" style="width:60px" />'
                }else if(data_type == 'url'){
                    cellValue = '<a href="' + cellValue + '" target="_blank">' + cellValue + '</a>'
                }

                if(row[field_name + '-html'] != undefined){
                    cellValue = row[field_name + '-html']
                }

                return cellValue
            },
            currentSelect:function (selection, row) {
                this.currentSelectRow = row
            },
            getSelectRows:function (selection) {
                this.selectRows = selection

            },
            fun: function (callfun) {
                //调用外部函数
                callfun(this.selectRows)
            },
            rowFun: function (callfun,index,row) {
                //调用外部函数
                callfun(index,row)
            },
            cellFun: function (callfun,index,row) {
                //调用外部函数
                callfun(index,row)
            },
            //添加表单
            add: function () {

            },
            pivot: function () {
                /*this.$exportExcel({
                    url: 'http://www.b2b.com/api/Table/index'
                })*/
            },

            //拉取数据
            pullData: function (currentPage,callback,action) {
                let url = this.server
                if(action != undefined && action == 'getField'){  //拉取表格字段信息
                    this.post(url,{
                        action: action
                    }).then(function (data) {
                        callback(data)
                    })
                }else{  //拉取列表数据
                    this.post(url + '?'+ this.page + '=' + currentPage,{
                        pageSize: this.pageSize,
                        orderField: this.orderField,
                        orderSort: this.orderSort,
                        keywords: this.keywords,
                        keywords_field: this.keywords_field,
                        filter: this.filterForm,
                        //兼容后端只接收offset 和 limit 参数分页处理
                        offset: this.pageSize * (currentPage - 1),
                        limit: this.pageSize
                    }).then(function (data) {
                        callback(data)
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
                this.refreshList()
            },
            //加载表格字段回调
            updateTableField: function (data) {
                let that = this
                this.columns = data.data.fields
                this.columns.forEach(function (val,key) {
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
                    if(val['show'] == true){
                        that.checkList.push(val['label'])
                    }
                })
            },
            //加载表格字段信息
            loadTableField: function () {
                this.pullData(1,this.updateTableField,'getField')
            },
            //搜索
            search: function () {
                this.refreshList()
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
                        //只下载显示的列
                        if(v['show']){
                            //过滤HTML标签
                            let label = v['label'].replace(/<[^>]*>/g,'')
                            let value = val[v['prop']]
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
                    this.$message.error('列表无数据！');
                    return false
                }
                this.downloadTips = true
                this.exportFileType = type
                this.downloadCurrentPage ++
                //逐页拉取数据
                this.pullData(this.downloadCurrentPage,this.getExportData)
            },
            //刷新
            refreshList: function () {
                this.loading = true
                this.pullData(this.currentPage,this.getList)
            },
            //改变窗口大小
            resizeWin: function () {
                //如果页数不够page-count，sizes 将不会显示
                if(document.body.offsetWidth < 768){
                    this.pageLayout = 'prev, pager, next'
                }else{
                    this.pageLayout = 'total, sizes, prev, pager, next, jumper'
                }

                this.filter_form_width = document.body.offsetWidth * 0.8
            },
            //列的显示与隐藏
            fieldChange(check_val){
                this.columns.forEach(function (item,index) {
                    if(check_val == '' || check_val == null || check_val.length == 0 || check_val == undefined){
                        item.show = false
                    }else{
                        if(check_val.indexOf(item.label) != -1){
                            item.show = true
                        }else{
                            item.show = false
                        }
                    }
                })
            },
            //列排序
            sort(column){
                this.orderField = column.prop
                this.orderSort = column.order == 'descending' ? 'desc' : 'asc'
                this.refreshList()
            },
            //拉取列表数据的回调
            getList(data){
                if(data.data.data == undefined){
                    let msg = '接口异常，无法拉取数据！'
                    if(data.data.message != undefined) msg = msg + data.data.message
                    if(data.data.msg != undefined) msg = msg + data.data.msg
                    this.$message.error(msg);
                    return false
                }
                this.tableData = data.data.data
                this.total = parseInt(data.data.total)
                this.totalPages = Math.ceil(this.total / this.pageSize)
                this.loading = false

                //执行外部传入的事件
                let that = this
                if(this.$props.cellEvent != undefined && this.$props.cellEvent != '' && this.$props.cellEvent != null){
                    this.$props.cellEvent.forEach(function (value,index) {
                        value(that.tableData)
                    })
                }


            },
            //每页显示条数修改
            handleSizeChange(val) {
                this.pageSize = val
                this.refreshList()
            },
            //当前页修改
            handleCurrentChange(val) {
                this.currentPage = val
                this.refreshList()

            }
        }
    }
</script>

<style scoped="true">
    .el-col{ margin-bottom: 10px;}
    .filter-form-inline .el-col{ margin-bottom: 0;}
    .el-form-item--small.el-form-item{ margin-bottom: 10px; }
    .pagination{ margin: 10px auto;}
    .table-tools{ text-align: right}
    .btn-group{ text-align: left}
    .dropdown-content{ max-height: 260px; padding: 10px 15px; overflow-y: auto; max-width: 500px; overflow-x: auto }
    .table-tools .el-button{ margin-left: -1px !important;  border-radius: 0px;}
    .el-dropdown:last-child .el-button{ border-top-right-radius: 4px !important; border-bottom-right-radius: 4px !important;  }
    .el-date-editor--daterange.el-input, .el-date-editor--daterange.el-input__inner{
        width: 100%  !important;
    }
    .el-date-editor--datetimerange.el-input, .el-date-editor--datetimerange.el-input__inner{
        width: 100%  !important;
    }
    .filter-form-content{
        max-height:460px; display: block; overflow-y: auto;}


</style>

<style>
    .table-tools .el-input__inner{ border-top-right-radius: 0 !important; border-bottom-right-radius: 0 !important; }
    .el-button--small{ font-size: 14px !important; padding: 8px 9px !important; }
    .el-table--small td, .el-table--small th{ padding: 5px 0 !important;}
    .el-form-item--small .el-form-item__content{
        width: 70% !important;
    }
    .el-popover{ max-width: 860px !important; }

</style>

