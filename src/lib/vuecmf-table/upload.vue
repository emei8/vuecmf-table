<template>
    <div>
        <template v-for="item in uploadFileList">
            <div class="vc-upload-list" v-if="typeof item != 'undefined' ">
                <template v-if="item.status === 'finished'">
                    <img :src="item.full_url" height="70">
                    <div class="vc-upload-list-cover">
                        <i-icon type="ios-eye-outline" title="查看" @click.native="handleView(item)"></i-icon>
                        <i-icon type="ios-trash-outline" title="移除" @click.native="handleRemove(item)"></i-icon>
                    </div>
                </template>
                <template v-else>
                    <i-progress v-if="item.showProgress" :percent="item.percentage" hide-info></i-progress>
                </template>
            </div>
        </template>

        <i-upload
                :disabled="disabled_upload" 
                ref="upload"
                :on-progress="handleProgress"
                :show-upload-list="false"
                :default-file-list="uploadFileList"
                :on-success="handleSuccess"
                :format="extension"
                :max-size="uploadFileMaxSize"
                :on-format-error="handleFormatError"
                :on-exceeded-size="handleMaxSize"
                :before-upload="handleBeforeUpload"
                :multiple="isMultiple"
                type="drag"
                :action="uploadFileServer"
                style="display: inline-block;width:58px;" v-if=" disabled != 1 ">
            <div style="width: 58px;height:58px;line-height: 58px;">
                <i-icon type="md-add" size="20" v-if=" disabled_upload == false "></i-icon>
                <div style="line-height:20px; text-align:center; padding-top:8px;" v-else><i-icon type="ios-loading" size="24" class="spin-icon-load" title="上传中，请稍后..."  ></i-icon><br>{{ upload_progress }}%</div>
            </div>
        </i-upload>
        <i-modal title="查看图像" v-model="visible" :z-index="1810">
            <img :src="viewCurrentFile" v-if="visible" >
        </i-modal>
    </div>
</template>

<script>
    export default {
        name: 'vc-upload',
        props:['disabled','uploadServer','prop','uploadFileServer','extension','uploadFileMaxSize','fieldLength','uploadList'],
        data(){
            return {
                upload_num: 0,
                upload_progress: 0,
                disabled_upload: false,
                fileList: [],
                uploadFileList: [],
                isMultiple: false,
                viewCurrentFile: '',
                visible: false
            }
        },
        watch: {
            //编辑加载时
            uploadList: function(value){
                this.uploadFileList = value
            },
            fileList: function(fileList){
                let that = this
                let file_list = []
                //根据上传数量设定延时
                let wait_time = fileList.length * 300

                if(that.upload_progress == 90){
                    setTimeout(function(){
                        that.upload_progress = 100
                        fileList.forEach(function (item,index) {
                            if(typeof item.status != "undefined" && item.status == 'finished'){
                                let full_url = ''
                                let url = ''
                                let small_url = ''
                                let middle_url = ''

                                if(typeof item.response != "undefined"){
                                    full_url = item.response.full_url
                                    url = item.response.url
                                    small_url = item.response.small_filename
                                    middle_url = item.response.middle_filename
                                }else{
                                    full_url = item.full_url
                                    url = item.url
                                    small_url = item.small_url
                                    middle_url = item.middle_url
                                }

                                let file_item = {
                                    name: item.name,
                                    percentage: item.percentage,
                                    showProgress: item.showProgress,
                                    size: item.size,
                                    status: item.status,
                                    full_url: full_url,
                                    url: url,
                                    small_url: small_url,
                                    middle_url: middle_url
                                }

                                if(that.fieldLength == 255 || that.fieldLength == 0){
                                    //多文件上传
                                    file_list[index] = file_item
                                }else{
                                    //单个文件上传
                                    file_list[0] = file_item
                                }
                            }
                        })

                        that.uploadFileList = file_list
                        that.$emit('on-upload-success', file_list, that.prop)
                        that.disabled_upload = false
                    },wait_time)
                }

                
            }

        },
        methods: {
            error_tips: function(msg){
                this.$Message.error({
                    background:true,
                    duration: 0,
                    closable: true,
                    content: msg
                });
                this.disabled_upload = false
                this.$refs.upload.clearFiles()
                return false;
            },
            handleView (file) {
                this.viewCurrentFile = file.full_url;
                this.visible = true;
            },
            handleRemove (file) {
                this.uploadFileList.splice(this.uploadFileList.indexOf(file), 1);
                this.$emit('on-upload-remove', this.uploadFileList, this.prop)
            },
            handleSuccess (response, file, fileList) {
                let that = this
                that.fileList = fileList; //在watch中处理

                if(response.code != 200){
                    that.error_tips('上传文件格式错误: 请上传基于web格式的图片文件.')

                }
            },
            handleFormatError (file) {
                this.error_tips('File format of ' + file.name + ' is incorrect, please select '+ this.extension.join(',') +'.')
            },
            handleMaxSize (file) {
                this.error_tips('File  ' + file.name + ' is too large, no more than 5M.')
            },
            handleProgress(event, file, fileList){
                if(file != null){
                    this.upload_num = this.upload_num + 1
                    let total = fileList.length - this.uploadFileList.length
                    let progress = 0
                    if(total == 0){
                        progress = 90
                    }else{
                        progress = Math.round(this.upload_num / total * 100) - 9
                        if(progress >= 90)  progress = 90
                    }
                    
                    this.upload_progress = progress
                }  
            },
            handleBeforeUpload (file) {
                this.upload_num = 0
                this.upload_progress = 0
                this.disabled_upload = true
                let max_num = 1
                if(this.fieldLength == 255 || this.fieldLength == 0){
                    max_num = 5
                }

                if (this.$refs.upload.fileList.length  > max_num - 1) {
                    return this.error_tips('最多只能上传'+ max_num +'个文件.')
                }
                return true; 
            }
        },
        updated: function(){

        },
        mounted: function(){
            if(this.fieldLength == 255 || this.fieldLength == 0){
                this.isMultiple = true
            }else{
                this.isMultiple = false
            }

            this.uploadFileList = this.uploadList

        }
    }
</script>

<style>
    .vc-upload-list{
        display: inline-block;
        width: 60px;
        height: 60px;
        text-align: center;
        line-height: 60px;
        border: 1px solid transparent;
        border-radius: 4px;
        overflow: hidden;
        background: #fff;
        position: relative;
        box-shadow: 0 1px 1px rgba(0,0,0,.2);
        margin-right: 4px;
    }
    .vc-upload-list img{
        width: 100%;
        height: 100%;
    }
    .vc-upload-list-cover{
        display: none;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,.6);
    }
    .vc-upload-list:hover .vc-upload-list-cover{
        display: block;
    }
    .vc-upload-list-cover i{
        color: #fff;
        font-size: 20px;
        cursor: pointer;
        margin: 0 2px;
    }

    .spin-icon-load{
        animation: ani-demo-spin 1s linear infinite;
    }

</style>
