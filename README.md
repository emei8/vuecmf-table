# vuecmf-table

> 基于vue2和iview的列表组件，内置添加、修改、删除、展开、搜索、筛选、分页、导入及导出EXCEL等功能

- 示例演示： http://www.vuecmf.com

## 项目中安装

``` bash
# 命令行中yarn方式安装 vuecmf-table
yarn add vuecmf-table
 
# 命令行中npm方式安装 vuecmf-table
npm install vuecmf-table
```

## 后端返回JSON 数据样例：
### 字段数据样例
后端接收前端POST数据中 action = getField
返回JSON
```
{
	"data": {
        "fields": [
        {
            "prop": "id",  //字段名称
            "label": "",  //表头显示名称
            "data_type": "int", //字段值类型
            "sortable": true, //是否可排序
            "show": false, //默认是否在表格中显示
            "fixed": false, //是否固定列
            "filter": false //是否可筛选
        },
        {
            "prop": "inquiry_sheet_id",
            "label": "inquiry_sheet的id",
            "data_type": "hidden",
            "sortable": true,
            "show": false,
            "fixed": false,
            "filter": false
        },
        {
            "prop": "sku",
            "label": "sku",
            "data_type": "string",
            "sortable": true,
            "show": true,
            "fixed": false,
            "filter": true
        }]
    },
    "msg": "",
	"code": 0
}
```

### 列表数据样例
```
{
	"data": {
        "total": 53,
        "last_page": 3,
        "data": [
        {
            "id": "63",
            "sku": "EUR001",
            "area": "欧洲区",
            "sku_name": "VC泡腾片 樱桃味 20片",
            "upc": "5605615274539",
            "unit": "瓶",
            "currency": "1",
            "quantity": "100",
            "unit_price": "95.0000",
            "status": "10",
            "expandData":{
                "type":"table",
                "tableFields":[
                    {"label":"需求单号","prop":"order_sn"},
                    {"label":"调拨类型","prop":"type"},
                    {"label":"计划数量","prop":"num"}
                ],
                "tableList":[
                    {"order_sn":"dbd903452","type":"POP","num":300},
                    {"order_sn":"dbd123123","type":"POP","num":400},
                    {"order_sn":"dbd144155","type":"POP","num":580}
                ]
            }
        },
        {
            "id": "62",
            "sku": "EUR019",
            "area": "欧洲区",
            "sku_name": "南瓜籽蔓越莓胶囊 30粒",
            "upc": "7626600561310",
            "unit": "盒",
            "currency": "4",
            "quantity": "200",
            "unit_price": "39.0000",
            "status": "10",
            "expandData":{
                "type":"table",
                "tableFields":[
                    {"label":"需求单号","prop":"order_sn"},
                    {"label":"调拨类型","prop":"type"},
                    {"label":"计划数量","prop":"num"}
                ],
                "tableList":[
                    {"order_sn":"dbd903452","type":"POP","num":300},
                    {"order_sn":"dbd123123","type":"POP","num":400},
                    {"order_sn":"dbd144155","type":"POP","num":580}
                ]
            }
        },
        {
            "id": "60",
            "sku": "EUR002",
            "area": "欧洲区",
            "sku_name": "VC泡腾片 香橙味 微量元素 20片",
            "upc": "6805615205298",
            "unit": "瓶",
            "currency": "3",
            "quantity": "300",
            "unit_price": "59.0000",
            "status": "10",
            "expandData":{
                "type":"table",
                "tableFields":[
                    {"label":"需求单号","prop":"order_sn"},
                    {"label":"调拨类型","prop":"type"},
                    {"label":"计划数量","prop":"num"}
                ],
                "tableList":[
                    {"order_sn":"dbd903452","type":"POP","num":300},
                    {"order_sn":"dbd123123","type":"POP","num":400},
                    {"order_sn":"dbd144155","type":"POP","num":580}
                ]
            }
        }]
    },
    "msg": "",
	"code": 0
}
```
