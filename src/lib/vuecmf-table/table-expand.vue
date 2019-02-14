<template>
    <div class="vc-expand">
        <template v-if="row.type == 'table'">
            <i-table border :columns="columns"  :data="tableData" size="small" :stripe="true" >
                <!-- 表格行自定义 -->
                <template v-for="(item,index) in columns"  slot-scope="{ row }"   :slot="item.slot"  >
                    <span  v-html="formatter(row,item.slot,item.data_type,item.options)"></span>
                </template>
            </i-table>
        </template>
    </div>
</template>
<style>
    .vc-expand .ivu-table-stripe .ivu-table-body tr:nth-child(2n) td{ background-color: #fff !important;}
</style>

<script>
    export default {
        props: {
            row: Object
        },
        data(){
            let columns = []

            this.row.tableFields.forEach(function (val,index) {
                let col = {
                    'title': val['label'],
                    'slot': val['prop'],
                    'data_type': val['data_type'],
                    'options': val['options']
                }

                if(val['width'] != undefined) col['width'] = val['width']
                columns.push(col)
            })

            return {
                columns: columns,
                tableData: this.row.tableList
            }
        },
        methods:{
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
                console.log(cellValue)
                return cellValue
            }


        }
    };
</script>