<template>
    <div>
        <!-- 数据表单 -->
        <i-modal
                :title="dataFormTitle"
                v-model="dataFormShow"
                :width="modelWidth"
                class-name="vertical-center-modal"
                :styles="{top: '20px',height: modelHeight}"
                draggable
        >
            <div slot="header">
                <h3>{{ dataFormTitle }}</h3>
            </div>
            <i-form :label-width="formLabelWidth" :model="dataForm"  :ref="refName"  :rules="ruleValidate">
                <template v-for="(item,index) in fieldsData">
                    <template v-if="item.data_form == true">
                        <template v-if="item.data_type == 'hidden'">
                            <input type="hidden" v-model="dataForm[item.slot]" >
                        </template>
                        <template v-else>
                            <i-row>
                                <i-form-item :label="item.title"  :prop="item.slot" >
                                    <i-select @on-change="selectEvent(item,dataForm[item.slot])" style="width:200px"  v-model="dataForm[item.slot]" filterable  placeholder="请选择" v-if=" item.data_type == 'select' ">
                                        <template v-for="(option_item,option_index) in item.options">
                                            <i-option v-if=" /^\d+$/.test(option_index) "
                                                    :key="option_index"
                                                    :value="parseInt(option_index)">{{ option_item }}</i-option>
                                            <i-option v-else-if=" /^\d+\.+\d+$/.test(option_index) "
                                                      :key="option_index"
                                                      :value="parseFloat(option_index)">{{ option_item }}</i-option>
                                            <i-option v-else
                                                      :key="option_index"
                                                      :value="option_index">{{ option_item }}</i-option>
                                        </template>

                                    </i-select>

                                    <i-date-picker
                                            v-else-if=" item.data_type == 'date' "
                                            v-model="dataForm[item.slot]"
                                            type="date"
                                            format="yyyy-MM-dd"
                                            placeholder=""
                                            @on-change="(datetime) =>{ changeDatetime(datetime,item.slot)}"
                                            @on-open-change="dataFormShow = true"
                                    >
                                    </i-date-picker>

                                    <i-date-picker
                                            v-else-if=" item.data_type == 'datetime' "
                                            v-model="dataForm[item.slot]"
                                            type="datetime"
                                            format="yyyy-MM-dd HH:mm:ss"
                                            placeholder=""
                                            @on-change="(datetime) =>{ changeDatetime(datetime,item.slot)}"
                                            @on-open-change="dataFormShow = true"
                                    >
                                    </i-date-picker>

                                    <vc-upload @on-upload-success="uploadSuccess" :upload-file-server="uploadFileServer" :extension="item.extension"  :upload-file-max-size="uploadFileMaxSize"  :ref="item.slot" :prop="item.slot"  v-else-if=" item.data_type == 'image' || item.data_type == 'file' "></vc-upload>

                                    <vue-neditor-wrap  v-else-if=" item.data_type == 'editor' "
                                            v-model="dataForm[item.slot]"
                                            :config="editorConfig"
                                            :destroy="false"
                                            @ready="editorReady">
                                    </vue-neditor-wrap>

                                    <i-radio-group  v-else-if=" item.data_type == 'radio' "  v-model="dataForm[item.slot]">
                                        <template v-for="(option_item,option_index) in item.options">
                                            <i-radio v-if=" /^\d+$/.test(option_index) " :label="parseInt(option_index)"><span>{{ option_item }}</span></i-radio>
                                            <i-radio v-else-if=" /^\d+\.+\d+$/.test(option_index) " :label="parseFloat(option_index)"><span>{{ option_item }}</span></i-radio>
                                            <i-radio v-else  :label="option_index"><span>{{ option_item }}</span></i-radio>
                                        </template>
                                    </i-radio-group>

                                    <i-input v-model="dataForm[item.slot]" :placeholder="'请输入' + item.title" v-else=" item.data_type == 'string' "></i-input>

                                </i-form-item>

                            </i-row>
                        </template>

                    </template>


                </template>
            </i-form>
            <div slot="footer" class="dialog-footer">
                <i-button  type="default" @click="resetDataForm">重置</i-button>
                <i-button type="primary" @click="saveDataForm()">保存</i-button>
            </div>

        </i-modal>

    </div>
</template>

<style>
    .ivu-modal-content{ height: 100% !important;}
    .ivu-modal-body{ height: calc(100% - 110px) !important;}
    .ivu-modal-body{ overflow: auto ;}
    .ivu-input-wrapper{ width: 98% !important;}
</style>

<script>

    import VueNeditorWrap from 'vue-neditor-wrap'
    import VcUpload from './upload.vue'


    export default {
        name:'vc-form',
        props:['dataFormTitle','modelWidth','modelHeight','formLabelWidth','dataForm','ruleValidate','fieldsData','refName','uploadFileServer','uploadFileMaxSize','editorConfig'],
        components:{VcUpload,VueNeditorWrap},
        data(){
            return {
                editorCfg: {
                    // 如果需要上传功能,找后端小伙伴要服务器接口地址
                    serverUrl: '',
                    // 你的UEditor资源存放的路径,相对于打包后的index.html
                    UEDITOR_HOME_URL: '',
                    // 编辑器不自动被内容撑高
                    autoHeightEnabled: false,
                    // 初始容器高度
                    initialFrameHeight: 240,
                    // 初始容器宽度
                    initialFrameWidth: '100%',
                    // 关闭自动保存
                    enableAutoSave: false
                },
                dataFormShow: false
            }
        },
        updated:function () {
            let that = this
            that.fieldsData.forEach(function (val,key) {
                if(val.data_type == 'date' ||  val.data_type == 'datetime'){
                    //处理iview的DatePicker时间带T带Z格式问题
                    that.dataForm[val.slot] = that.getFormatDate(that.dataForm[val.slot])
                }
            })

        },
        mounted: function () {
            let that = this
            that.editorCfg = that.editorConfig
            that.editorCfg.serverUrl = that.uploadFileServer
            console.log(that.editorCfg)

            //初始化表单默认值
            console.log(that.fieldsData)

        },
        methods:{
            editorReady(text){
                console.log(text)
            },

            //下拉选择事件
            selectEvent(item,sel_val){
                let that = this
                that.dataFormShow = true
                console.log(item)
                if(item.filter_form_name != '' && item.filter_field != '' && item.filter_url != ''){
                    //下拉事件过滤
                    that.fieldsData.forEach(function (v,k) {
                        if(v.slot == item.filter_form_name){  //要联动变化的表单
                            // v.field_id  通过此字段ID 查询出关联的模型
                            // item.filter_field  在关联模型中要作为筛选条件的字段
                            // sel_val 筛选条件的值
                            that.$parent.post(item.filter_url,{field_id:v.field_id,filter_field:item.filter_field,sel_val:sel_val}).then(function (data) {
                                v.options = data.data.data
                                console.log(data)
                            });

                           // console.log(v.field_id)
                            //v.options = ''
                        }
                    })
                }
            },
            uploadSuccess: function (uploadList,prop) {
                let fileList = []
                uploadList.forEach(function (v,k) {
                    fileList.push(v.url)
                })
                this.dataForm[prop] = fileList.join(',')
            },
            //处理iview的DatePicker时间带T带Z格式问题
            getFormatDate: function(dateStr){
                let dateJson = new Date(dateStr).toJSON()
                let dateRes = new Date(+ new Date(dateJson) + 8 * 3600 * 1000).toISOString().replace(/T/g,' ').replace(/\.[\d]{3}Z/,'')
                return dateRes
            },
            //重置数据表单
            resetDataForm: function () {
                this.$refs[this.refName].resetFields()
            },
            //保存数据表单
            saveDataForm: function () {
                this.$emit('on-save-data-form',this.dataForm)
                this.dataFormShow = false
            },
            //处理日历控件数据
            changeDatetime: function (datetime, prop) {
                this.dataForm[prop] = datetime
                this.dataFormShow = true
            },
        }
    }
</script>