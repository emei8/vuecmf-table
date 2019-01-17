<template>
    <div class="vc-expand">
        <template v-if="row.type == 'table'">
            <Table border :columns="columns"  :data="tableData" size="small" :stripe="true" ></Table>
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
                columns.push({
                    'title': val['label'],
                    'key': val['prop']
                })
            })

            return {
                columns: columns,
                tableData: this.row.tableList
            }
        },
        method:{

        }
    };
</script>