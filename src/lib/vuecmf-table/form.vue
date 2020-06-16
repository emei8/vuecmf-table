<template>
    <div>
        <!-- 数据表单 -->
        <i-modal
                :z-index="1"
                ref="dlg"
                :title="dataFormTitle"
                v-model="dataFormShow"
                :width="modelWidth"
                class-name="vertical-center-modal"
                :styles="{top: '20px',height: modelHeight}"
                draggable
                @on-ok="closeDlg"
                @on-cancel="closeDlg"
        >
            <div slot="header">
                <h3>{{ dataFormTitle }}</h3>
            </div>
            <i-form :label-width="formLabelWidth" :model="dataForm" :inline="true"  :ref="refName"  :rules="ruleValidate">
                <i-tabs type="card" >
                  <i-tab-pane :label="tab_item.tab_name" v-for=" tab_item in formTabs ">
                    <i-row>
                        <template v-for="(item,index) in fieldsData">
                          <i-col :span=" tab_item.tab_fields.length == 1 ? 24 : 12 ">
                              
                              <template v-if="item.data_form == true && tab_item.tab_fields.indexOf(item.slot) != -1 ">
                                <template v-if="item.data_type == 'hidden'">
                                  <input type="hidden" v-model="dataForm[item.slot]" >
                                </template>
                                <template v-else>

                                  <vue-neditor-wrap  v-if=" item.data_type == 'editor' "
                                                     v-model="dataForm[item.slot]"
                                                     :config="editorConfig"
                                                     :destroy="false"
                                                     @ready="editorReady">
                                  </vue-neditor-wrap>


                                    <i-form-item :label="item.title"  :prop="item.slot"  v-else>
                                      <i-select @on-change="selectEvent(item,dataForm[item.slot])" style="width:200px"  v-model="dataForm[item.slot]" filterable  placeholder="请选择" v-if=" item.data_type == 'select' " clearable  :disabled=" item.is_disabled " :transfer="true" >
                                        <template v-if="typeof dataForm[item.slot+'_options'] != 'undefined' ">
                                            <template v-for="(option_item,option_index) in dataForm[item.slot+'_options']">
                                                <i-option v-if=" /^\d+$/.test(option_index) "
                                                        :key="option_index"
                                                        :value="parseInt(option_index)">{{ option_item }}</i-option>
                                                <i-option v-else-if=" /^\d+\.+\d+$/.test(option_index) "
                                                        :key="option_index"
                                                        :value="parseFloat(option_index)">{{ option_item }}</i-option>
                                                <i-option v-else
                                                        :key="option_index"
                                                        :value="option_index" v-html="option_item"></i-option>
                                            </template>
                                        </template>
                                        <template v-else>
                                            <template v-for="(option_item,option_index) in item.options">
                                                <i-option v-if=" /^\d+$/.test(option_index) "
                                                        :key="option_index"
                                                        :value="parseInt(option_index)">{{ option_item }}</i-option>
                                                <i-option v-else-if=" /^\d+\.+\d+$/.test(option_index) "
                                                        :key="option_index"
                                                        :value="parseFloat(option_index)">{{ option_item }}</i-option>
                                                <i-option v-else
                                                        :key="option_index"
                                                        :value="option_index" v-html="option_item"></i-option>
                                            </template>
                                        </template>
                                        
                                        

                                      </i-select>

                                      <i-date-picker
                                        :disabled=" item.is_disabled "
                                        v-else-if=" item.data_type == 'date' "
                                        v-model="dataForm[item.slot]"
                                        type="date"
                                        format="yyyy-MM-dd"
                                        placeholder=""
                                        @on-change="(datetime) =>{ changeDatetime(datetime,item.slot)}"
                                        @on-open-change="dataFormShow = true"  clearable
                                      >
                                      </i-date-picker>

                                        
                                      <i-date-picker
                                        v-else-if=" item.data_type == 'datetime' "
                                        v-model="dataForm[item.slot]"
                                        type="datetime"
                                        format="yyyy-MM-dd HH:mm:ss"
                                        placeholder=""
                                        @on-change="(datetime) =>{ changeDatetime(datetime,item.slot)}"
                                        @on-open-change="dataFormShow = true"  clearable  :disabled=" item.is_disabled "
                                      >
                                      </i-date-picker>

                                      <vc-upload @on-upload-success="uploadSuccess" @on-upload-remove="uploadRemove" :upload-file-server="uploadFileServer + '&tag=' + tag " :extension="item.extension"  :upload-file-max-size="uploadFileMaxSize"  :field-length="item.field_length"  :ref="item.slot" :prop="item.slot"  v-else-if=" item.data_type == 'image' || item.data_type == 'img' || item.data_type == 'file' " :upload-list="dataForm[item.slot + '_file_info']"  :disabled=" item.is_disabled " ></vc-upload>



                                      <i-radio-group  v-else-if=" item.data_type == 'radio' "  v-model="dataForm[item.slot]" clearable :disabled=" item.is_disabled ">
                                        <template v-for="(option_item,option_index) in item.options">
                                          <i-radio v-if=" /^\d+$/.test(option_index) " :label="parseInt(option_index)"><span>{{ option_item }}</span></i-radio>
                                          <i-radio v-else-if=" /^\d+\.+\d+$/.test(option_index) " :label="parseFloat(option_index)"><span>{{ option_item }}</span></i-radio>
                                          <i-radio v-else  :label="option_index"><span>{{ option_item }}</span></i-radio>
                                        </template>
                                      </i-radio-group>

                                      <i-checkbox-group  v-else-if=" item.data_type == 'checkbox' "  v-model="dataForm[item.slot]" :disabled=" item.is_disabled ">
                                        <template v-for="(option_item,option_index) in item.options">

                                          <i-checkbox  :label="option_index"><span>{{ option_item }}</span></i-checkbox>
                                        </template>
                                      </i-checkbox-group>

                                      <i-input v-model="dataForm[item.slot]" type="textarea" :rows="4" :placeholder="'请输入' + item.title" v-else-if=" item.data_type == 'textarea' "  clearable  :disabled=" item.is_disabled " ></i-input>

                                      <span v-else-if=" item.data_type == 'string' || item.data_type == 'ip' ">{{ dataForm[item.slot] == '' ? '--' : dataForm[item.slot] }}</span>

                                      
                                      <!-- <i-input-number v-model="dataForm[item.slot]" v-else-if="(typeof dataForm[item.slot] != 'string' || dataForm[item.slot] == '') && item.data_type == 'text' && (['int','tinyint','bigint','smallint'].indexOf(item.field_type) != -1 || item.slot.indexOf('weight') != -1) "  :disabled=" item.is_disabled "></i-input-number> -->

                                      <i-input v-model="dataForm[item.slot]" :placeholder="'请输入' + item.title" v-else=" item.data_type == 'text' "  clearable  :disabled=" item.is_disabled ">
                                          <span slot="append" v-if="item.slot.indexOf('percent') != -1" >%</span>
                                          <span slot="prepend" v-else-if="item.slot.indexOf('price') != -1" >$</span>
                                      </i-input>


                                    </i-form-item>

                                </template>

                              </template>
                          </i-col>
                        </template>
                    </i-row>

                  </i-tab-pane>
                </i-tabs>



            </i-form>
            <div slot="footer" class="dialog-footer">
                <i-button  type="default" @click="resetDataForm" v-if=" refName == 'addDataForm' ">重置</i-button>
                <i-button type="primary" @click="saveDataForm()">保存</i-button>
            </div>

        </i-modal>

    </div>
</template>

<style scoped>
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
        props:['formTabs','dataFormTitle','modelWidth','modelHeight','formLabelWidth','dataForm','ruleValidate','fieldsData','refName','uploadFileServer','uploadFileMaxSize','editorConfig','token'],
        components:{VcUpload,VueNeditorWrap},
        data(){
            return {
                tag: '',
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
                    enableAutoSave: false,
                    zIndex: 1800
                },
                dataFormShow: false
            }
        },
        updated:function () {
            let that = this
           

            //that.$myScroll(that.$refs.dlg)

            that.fieldsData.forEach(function (val,key) {
                if(val.data_type == 'date' ||  val.data_type == 'datetime'){
                    //处理iview的DatePicker时间带T带Z格式问题
                    that.dataForm[val.slot] = that.getFormatDate(that.dataForm[val.slot])
                }

            })

            if(typeof that.dataForm['tag'] != "undefined"){
                that.tag = that.dataForm['tag']
            }

            that.$refs[that.refName].validate()

        },
        mounted: function () {
            let that = this
            that.editorCfg = that.editorConfig
            that.editorCfg.serverUrl = that.uploadFileServer

            if(typeof that.dataForm['tag'] != "undefined"){
                that.tag = that.dataForm['tag']
            }
            

            

        },
        methods:{
            editorReady(text){
                console.log(text)

            },

            closeDlg(){
                //将对话框中滚动条重置到顶部
                this.$refs['dlg'].$refs.content.children[2].scrollTop = 0;
                this.$refs['dlg'].$refs.content.children[2].scrollLeft = 0;
                this.$emit('on-close-dialog')
            },

            //下拉选择事件
            selectEvent(item,sel_val,showDlg){
                let that = this
                if(showDlg == undefined) showDlg = true
                if(showDlg == true){
                    that.dataFormShow = true
                }else{
                    that.dataFormShow = false
                }

                if(item.filter_form_name != '' && item.filter_field != '' && item.filter_url != ''){
                    //下拉事件过滤
                    that.fieldsData.forEach(function (v,k) {
                        if(v.slot == item.filter_form_name){  //要联动变化的表单
                            // v.field_id  通过此字段ID 查询出关联的模型
                            // item.filter_field  在关联模型中要作为筛选条件的字段
                            // sel_val 筛选条件的值
                            let tokenParam = ''
                            if(that.token != '') tokenParam = '?token=' + that.token
                            that.$parent.post(item.filter_url + tokenParam,{field_id:v.field_id,filter_field:item.filter_field,sel_val:sel_val}).then(function (data) {
                                v.options = data.data.data
                                console.log('======',that.fieldsData)
                            });

                           // console.log(v.field_id)
                            //v.options = ''
                        }
                    })
                }
            },
            //移除上传的文件
            uploadRemove: function(uploadList,prop){
                console.log('uploadList=',uploadList)
                this.uploadSuccess(uploadList,prop)
            },
            //上传成功
            uploadSuccess: function (uploadList,prop) {
                let fileList = []
                let thumbFileList = []
                let middleFileList = []
                uploadList.forEach(function (v,k) {
                    console.log('上传=',v,k)
                    fileList.push(v.url)
                    let url_arr = v.url.split('.')
                    let index_val = url_arr.length - 1
                    let ext = url_arr[index_val].toString()
                    ext = ext.toLowerCase()
                    console.log(ext)
                    if(['jpg','jpeg','png','gif'].indexOf(ext) != -1){
                        thumbFileList.push(v.small_url)
                        if(v.middle_url != ''){
                            middleFileList.push(v.middle_url)
                        }
                    }
                })
                console.log(fileList)
                //this.dataForm[prop] = fileList.join(',')
                this.$set(this.dataForm, prop, fileList.join(','))
                this.$set(this.dataForm,'thumb_' + prop, thumbFileList.join(','))
                this.$set(this.dataForm,'middle_' + prop, middleFileList.join(','))

            },
            //处理iview的DatePicker时间带T带Z格式问题
            getFormatDate: function(dateStr){
                if(dateStr == '' || dateStr == 0) return  '';
                let dateJson = new Date(dateStr).toJSON()
                let dateRes = new Date(+ new Date(dateJson) + 8 * 3600 * 1000).toISOString().replace(/T/g,' ').replace(/\.[\d]{3}Z/,'')
                return dateRes
            },
            //重置数据表单
            resetDataForm: function (is_close_dlg) {
                this.$refs[this.refName].resetFields()
                if(is_close_dlg == true){
                    this.dataFormShow = false
                }
            },
            //保存数据表单
            saveDataForm: function () {
                let that = this
                let saveData = {}

                console.log('dataForm=',that.dataForm)

                that.$refs[that.refName].validate(function(data){
                    if(data == true){
                        that.fieldsData.forEach(function (v,k) {
                            let fv = that.dataForm[v['slot']]
                            if(v['data_type'] == 'select' && v['is_tree'] == true && typeof that.dataForm[v['slot']] == 'string'){
                                //针对目录树分类ID处理，因ID加了单引号
                                fv = parseInt(fv.replace(/'/g,''))
                                saveData[v['slot']] = fv
                            }else if(['image','img'].indexOf(v['data_type']) != -1){
                                if(typeof that.dataForm['thumb_' + v['slot']] != "undefined"){
                                    saveData['thumb_' + v['slot']] = that.dataForm['thumb_' + v['slot']]
                                }

                                if(typeof that.dataForm['middle_' + v['slot']] != "undefined"){
                                    saveData['middle_' + v['slot']] = that.dataForm['middle_' + v['slot']]
                                }

                                saveData[v['slot']] = fv
                            }else{
                                saveData[v['slot']] = fv
                            }

                        })

                        that.$emit('on-save-data-form',saveData)
                    }else{
                       that.$Message.error('表单验证未通过！'); 
                    }

                })
            },
            //处理日历控件数据
            changeDatetime: function (datetime, prop) {
                this.dataForm[prop] = datetime
              //  this.dataFormShow = true
            },
        }
    }
</script>
