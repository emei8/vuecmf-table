<template>
    <div :style="'width:'+ width + 'px'">
        <Row :gutter="10" >
            <Col  :xs="24" :sm="8" :md="8" :lg="8"   class="btn-group">
                <Button v-if="add" @click="addForm"  type="primary"><Icon type="md-add-circle" /> 添加</Button>
                <ButtonGroup v-if="headerAction">
                    <template v-for="(item, index) in headerAction">
                        <Button   :type="item.type" @click="fun(item.event)"  :title="item.title"><Icon :type="item.icon" /> {{ item.label }}</Button>
                    </template>
                </ButtonGroup>
            </Col>
            <Col :xs="24" :sm="16" :md="16" :lg="16"  class="table-tools">
                <Row type="flex" justify="end">
                    <div>
                        <Input
                                placeholder="请输入内容"
                                v-model="keywords"
                                @on-change="search"
                                clearable>
                        </Input>
                    </div>

                    <Button type="default"  title="刷新" @click="refresh"><Icon type="md-refresh" /></Button>
                    <Poptip
                            placement="bottom-end"
                            :width="filter_form_width"
                            >
                        <Button type="default"  title="筛选" ><Icon type="ios-funnel" /></Button>
                        <div slot="content">
                            <Form :inline="true" :model="filterForm"  ref="filterForm"  >
                                <div class="filter-form-content">
                                    <template v-for="(item,index) in fields_data" >
                                        <Col :xs="24" :sm="12" :md="8" :lg="8" :xl="6"  v-if="item.filter">
                                        <FormItem :label="item.title"  :prop="item.slot">
                                            <Input v-model="filterForm[item.slot]" clearable  :placeholder="'请输入' + item.title" v-if=" item.data_type == 'string' "></Input>
                                            <Select  v-model="filterForm[item.slot]" filterable  placeholder="请选择" v-if=" item.data_type == 'select' ">
                                                <Option v-for="(option_item,option_index) in item.options" :key="option_index" :value="option_index">{{ option_item }}</Option>
                                            </Select>

                                            <DatePicker
                                                    v-if=" item.data_type == 'date' "
                                                    v-model="filterForm[item.slot]"
                                                    type="daterange"
                                                    format="yyyy-MM-dd"
                                            >
                                            </DatePicker>

                                            <DatePicker
                                                    v-if=" item.data_type == 'datetime' "
                                                    v-model="filterForm[item.slot]"
                                                    type="datetimerange"
                                                    format="yyyy-MM-dd HH:mm:ss"
                                            >
                                            </DatePicker>

                                        </FormItem>

                                        </Col>
                                    </template>
                                </div>
                                <Col>
                                <div style="text-align: right; margin: 0">
                                    <Button  type="default" @click="restFilter">重置</Button>
                                    <Button type="primary"  @click="filter">确定</Button>
                                </div>
                                </Col>
                            </Form>
                        </div>
                    </Poptip>

                    <!--<el-button type="default" size="small" title="日历"><i class="fa fa-calendar"></i></el-button>
                    <el-button type="default" size="small" title="透视" @click="pivot"><i class="fa fa-table"></i></el-button>
                    <el-button type="default" size="small" title="图表"><i class="fa fa-bar-chart"></i></el-button>
                    <el-button type="default" size="small" title="看板"><i class="fa fa-th-large"></i></el-button>-->

                    <Poptip placement="bottom-end" :width="200" >
                        <Button type="default"  title="列">
                            <Icon type="ios-apps" />
                        </Button>
                        <div slot="content">
                            <CheckboxGroup v-model="checkList" class="dropdown-content" @on-change="fieldChange">
                                <template v-for="(item,index) in fields_data">
                                    <Checkbox  :label="item.slot" >
                                        <span v-html="item.title"></span>
                                    </Checkbox><br>
                                </template>
                            </CheckboxGroup >
                        </div>
                    </Poptip>

                    <Dropdown trigger="click" @on-click="downloadExport" :transfer="true"  placement="bottom-end">
                        <Button type="default"  title="导出">
                            <Icon type="md-download" />
                        </Button>
                        <Dropdown-menu slot="list">
                            <Dropdown-item name="csv">CSV</Dropdown-item>
                            <Dropdown-item name="xlsx">MS-Excel</Dropdown-item>
                            <Dropdown-item name="xml">XML</Dropdown-item>
                        </Dropdown-menu>
                    </Dropdown>


                </Row>
            </Col>
        </Row>

        <Table
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
        >
            <!-- 表格行自定义 -->
            <template v-for="(item,index) in columns"  slot-scope="{ row }"   :slot="item.slot"  >
                <span  v-html="formatter(row,item.slot,item.data_type,item.options)"></span>
            </template>

            <!-- 操作列 -->
            <template slot-scope="{ row, index }" slot="action" v-if="rowAction">
                <span v-for="(item,k) in rowAction">
                        <template v-if=" item.callback == undefined || item.callback(index, row) == false || row.callback_result == false ">
                            <Button style="margin-right: 5px"
                                    :type="item.type"
                                    @click.native.prevent="rowFun(item.event,index, row)" ><Icon :type="item.icon" /> {{item.title}}</Button>
                        </template>
                        <template v-else>

                            <span v-html="row.callback_result"></span>
                        </template>

                    </span>
            </template>

        </Table>

        <div class="pagination">
            <Page
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
            </Page>
        </div>


        <Modal
                title="正在下载数据，请稍后..."
                v-model="downloadTips"
                width="30%"
                :closable="false"
                >
            <span class="ivu-tag-red">{{downloadError}}</span>
            <Progress  :stroke-width="18" :percent="percentage"></Progress >
            <div slot="footer">

            </div>
        </Modal>

        <!-- 数据表单 -->
        <Modal
                title="添加"
                v-model="dataForm_show"
                width="60%"
                >
            <Form :label-width="label_width" :model="dataForm"  ref="dataForm"  >
                <template v-for="(item,index) in fields_data">
                    <Row>
                    <input type="hidden" v-model="dataForm[item.slot]" v-if=" item.data_type == 'hidden' ">

                    <FormItem :label="item.title"  :prop="item.slot"  v-else=" item.data_form  && item.data_type != 'hidden' " >
                        <Select  v-model="dataForm[item.slot]" filterable  placeholder="请选择" v-if=" item.data_type == 'select' ">
                            <Option
                                    v-for="(option_item,option_index) in item.options"
                                    :key="option_index"
                                    :value="option_index">
                                {{ option_item }}
                            </Option>
                        </Select>

                        <DatePicker
                                v-else-if=" item.data_type == 'date' "
                                v-model="dataForm[item.slot]"
                                type="date"
                                format="yyyy-MM-dd"
                        >
                        </DatePicker>

                        <DatePicker
                                v-else-if=" item.data_type == 'datetime' "
                                v-model="filterForm[item.slot]"
                                type="datetime"
                                format="yyyy-MM-dd HH:mm:ss"
                        >
                        </DatePicker>

                        <Input v-model="dataForm[item.slot]" :placeholder="'请输入' + item.title" v-else=" item.data_type == 'string' "></Input>

                    </FormItem>



                </Row>
                </template>
            </Form>
            <div slot="footer" class="dialog-footer">
                <Button  type="default" @click="resetDataForm">重置</Button>
                <Button type="primary" @click="saveDataForm">保存</Button>
            </div>

        </Modal>


    </div>

</template>

<script>
    import Vue from 'vue'
    import axios from 'axios'
    import jsonExport from './jsonExport'

    /*
    如果iview页面使用CDN外链接引入的话，则注释这段*/
    import iView from 'iview'
    import 'iview/dist/styles/iview.css';
    Vue.use(iView)

    //数组移除元素
    Array.prototype.remove = function(index)
    {
        if(isNaN(index)||index>this.length){return false;}
        this.splice(index,1);
    }


    export default {
        name:'vc-table',
        props:['add','edit','del','selectable','cellEvent','checkbox','headerAction','rowAction','server','page','limit','width','height','operateWidth'],//头部按钮
        data() {
            return {
                fields_data:[], //字段信息
                //数据表单
                dataForm:{},
                dataForm_show:false, //数据表单显示
                label_width:120, //表单文本宽度

                //筛选表单
                filterForm: {},
                filter_show: false, //筛选表单显示
                filter_form_width:0,
                keywords: '',
                keywords_field:[], //可模糊搜索字段

                checkList: [], //列显示

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
            let that = this
            that.pageSize = that.limit
            that.loadTableField()
            that.refresh()
            that.resizeWin()
            window.onresize = () => {
                that.resizeWin()
            }

        },
       /* watch:{
            filterForm: function(newFilterForm){
                this.$refs['filterForm'].resetFields();
                console.log(newFilterForm)
            }
        },*/
        updated() {
            let that = this
            //执行外部传入的事件
            if(that.$props.cellEvent != undefined && that.$props.cellEvent != '' && that.$props.cellEvent != null){
                let callfun = that.$props.cellEvent
                callfun(that.tableData)
            }
        },
        methods: {
            //添加表单
            addForm: function () {
                this.dataForm_show = true
                //this.$refs['dataForm'].resetFields()
            },
            //重置数据表单
            resetDataForm: function () {
                this.$refs['dataForm'].resetFields()
            },
            //保存数据表单
            saveDataForm: function () {
                this.dataForm_show = false
            },
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
                this.refresh()
            },
            //加载表格字段回调
            updateTableField: function (data) {
                let that = this

                //可多选列
                if(that.checkbox == true){
                    that.columns.push({
                        type: 'selection',
                        width: 50,
                        align: 'center',
                        fixed: true
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
                        that.$set(that.dataForm,val['prop'],'')
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
                                    h('Tooltip',{props:{placement:"bottom"}},[
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
                                return h('span',{style:{ fontSize:"12px"}},params.column.title)
                            }

                        },

                        data_type: val['data_type'],
                        width: val['width'],
                        tips: val['tooltip'],
                        options: val['options'],
                        filter: val['filter']

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
                if(that.rowAction != undefined && that.rowAction.length > 0){
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
            refresh: function () {
                this.loading = true
                this.pullData(this.currentPage,this.getList)
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
            sort(column,key,order){
                this.orderField = column.key
                this.orderSort = order
                this.refresh()
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

<style scoped="true">
    .filter-form-content{ max-height:460px; display: block; overflow-y: auto;}
    .dropdown-content{ max-height: 260px; padding: 10px; overflow-y: auto; max-width: 500px; overflow-x: auto }
    .ivu-col{ margin-bottom: 10px;}
    .table-tools .ivu-btn{ margin-left: -1px !important;  border-radius: 0px;}
    .table-tools .ivu-dropdown:last-child .ivu-btn{ border-top-right-radius: 4px !important; border-bottom-right-radius: 4px !important;  }
    .pagination{ margin-top: 10px;}

</style>

<style>
    .table-tools .ivu-input{ border-top-right-radius: 0 !important; border-bottom-right-radius: 0 !important; }
    .ivu-table-cell{ padding-left: 10px !important; padding-right: 10px !important;}
</style>

