<template>
    <div>
        <div class="vc-upload-list" v-for="item in uploadList">
            <template v-if="item.status === 'finished'">
                <img :src="item.url">
                <div class="vc-upload-list-cover">
                    <i-icon type="ios-eye-outline" title="查看" @click.native="handleView(item)"></i-icon>
                    <i-icon type="ios-trash-outline" title="移除" @click.native="handleRemove(item)"></i-icon>
                </div>
            </template>
            <template v-else>
                <i-progress v-if="item.showProgress" :percent="item.percentage" hide-info></i-progress>
            </template>
        </div>
        <i-upload
                ref="upload"
                :show-upload-list="false"
                :default-file-list="defaultList"
                :on-success="handleSuccess"
                :format="['jpg','jpeg','png']"
                :max-size="2048"
                :on-format-error="handleFormatError"
                :on-exceeded-size="handleMaxSize"
                :before-upload="handleBeforeUpload"
                multiple
                type="drag"
                action="http://www.b2b.com/api/Table/upload"
                style="display: inline-block;width:58px;">
            <div style="width: 58px;height:58px;line-height: 58px;">
                <i-icon type="md-add" size="20"></i-icon>
            </div>
        </i-upload>
        <i-modal title="查看图像" v-model="visible">
            <img :src="viewCurrentFile" v-if="visible" style="width: 100%">
        </i-modal>
    </div>
</template>

<script>
    export default {
        name: 'vc-upload',
        props:['uploadServer','prop'],
        data(){
            return {
                defaultList: [
                    /*{
                        'name': 'a42bdcc1178e62b4694c830f028db5c0',
                        'url': 'https://o5wwk8baw.qnssl.com/a42bdcc1178e62b4694c830f028db5c0/avatar'
                    },
                    {
                        'name': 'bc7521e033abdd1e92222d733590f104',
                        'url': 'https://o5wwk8baw.qnssl.com/bc7521e033abdd1e92222d733590f104/avatar'
                    }*/
                ],
                viewCurrentFile: '',
                visible: false,
                uploadList: []
            }
        },
        methods: {
            handleView (file) {
                console.log(file)
                this.viewCurrentFile = file.url;
                this.visible = true;
            },
            handleRemove (file) {
                const fileList = this.$refs.upload.fileList;
                this.$refs.upload.fileList.splice(fileList.indexOf(file), 1);
            },
            handleSuccess (response, file, fileList) {
                file.url = response.url
                file.name = response.name
                this.$emit('on-upload-success',this.uploadList, this.prop)
            },
            handleFormatError (file) {
                this.$Notice.warning({
                    title: 'The file format is incorrect',
                    desc: 'File format of ' + file.name + ' is incorrect, please select jpg or png.'
                });
            },
            handleMaxSize (file) {
                this.$Notice.warning({
                    title: 'Exceeding file size limit',
                    desc: 'File  ' + file.name + ' is too large, no more than 2M.'
                });
            },
            handleBeforeUpload () {
                const check = this.uploadList.length < 5;
                if (!check) {
                    this.$Notice.warning({
                        title: 'Up to five pictures can be uploaded.'
                    });
                }
                return check;
            }
        },
        updated: function(){
            this.uploadList = this.$refs.upload.fileList

        },
        mounted: function(){
            //this.uploadList = this.$refs.upload.fileList
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
</style>