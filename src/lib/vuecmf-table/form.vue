<template>
    <div>
        <!-- 数据表单 -->
        <i-modal
                :title="dataFormTitle"
                v-model="dataFormShow"
                :width="modelWidth"
                class-name="vertical-center-modal"
        >
            <i-form :label-width="formLabelWidth" :model="dataForm"  :ref="refName"  :rules="ruleValidate">
                <template v-for="(item,index) in fieldsData">
                    <template v-if="item.data_form == true">
                        <template v-if="item.data_type == 'hidden'">
                            <input type="hidden" v-model="dataForm[item.slot]" >
                        </template>
                        <template v-else>
                            <i-row>
                                <i-form-item :label="item.title"  :prop="item.slot" >
                                    <i-select @on-change="dataFormShow = true" style="width:200px"  v-model="dataForm[item.slot]" filterable  placeholder="请选择" v-if=" item.data_type == 'select' ">
                                        <i-option
                                                v-for="(option_item,option_index) in item.options"
                                                :key="option_index"
                                                :value="option_index">{{ option_item }}</i-option>
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

                                    <vc-upload @on-upload-success="uploadSuccess"  :ref="item.slot" :prop="item.slot"  v-else-if=" item.data_type == 'image' || item.data_type == 'file' "></vc-upload>


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

<script>
    import VcUpload from './upload.vue'

    export default {
        name:'vc-form',
        props:['dataFormTitle','modelWidth','formLabelWidth','dataForm','ruleValidate','fieldsData','refName'],
        components:{VcUpload},
        data(){
            return {
                dataFormShow: false,
            }
        },
        updated:function () {
            //处理iview的DatePicker时间带T带Z格式问题
            let that = this
            that.fieldsData.forEach(function (val,key) {
                if(val.data_type == 'date' ||  val.data_type == 'datetime'){
                    that.dataForm[val.slot] = that.getFormatDate(that.dataForm[val.slot])
                }
            })
        },
        mounted: function () {

        },
        methods:{
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
            },
            //处理日历控件数据
            changeDatetime: function (datetime, prop) {
                this.dataForm[prop] = datetime
                this.dataFormShow = true
            },
        }
    }
</script>