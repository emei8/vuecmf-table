<template>
    <div>
        <template v-if="row.type == 'table'">
            <Table border :columns="columns" :data="tableData"></Table>
        </template>
    </div>
</template>

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
        }
    };
</script>