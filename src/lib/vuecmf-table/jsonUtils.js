import FileSaver from 'file-saver'
import XLSX from 'xlsx'

//字符串转ArrayBuffer
let stringToBuffer = function (s) {
    let buf = new ArrayBuffer(s.length);
    let view = new Uint8Array(buf);
    for (let i=0; i < s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
    return buf;
}

// 将一个sheet转成最终的excel文件的blob对象
let sheet2blob = function (sheet, fileType, sheetName) {
    sheetName = sheetName || 'sheet1';
    let workbook = {
        SheetNames: [sheetName],
        Sheets: {}
    };
    workbook.Sheets[sheetName] = sheet;
    // 生成excel的配置项
    let wopts = {
        bookType: fileType, // 要生成的文件类型, 支持 xlsx, csv, xml, txt
        bookSST: false, // 是否生成Shared String Table，如果开启生成速度会下降，但在低版本IOS设备上有更好的兼容性
        type: 'binary'
    };
    let wbout = XLSX.write(workbook, wopts);
    let blob = new Blob([stringToBuffer(wbout)], {type:"application/octet-stream"});

    return blob;
}


/**
 * 保存导出
 * 参数：
 *      dataList  导出的数据集合列表
 *      fileType  导出保存的文件类型
 *      fileName  导出保存的文件名
 */
export function jsonExport(dataList,fileType,fileName) {
    let cur_date = new Date()
    let sheet = XLSX.utils.json_to_sheet(dataList)
    FileSaver.saveAs(sheet2blob(sheet,fileType), fileName + cur_date.toLocaleString() + '.' + fileType)
}

/**
 * 导入Excel数据
 * @param fileObj  表单对象
 * @param callback  回调函数， 处理上传的数据
 * @returns {boolean}
 */
export function jsonImport(fileObj,callback) {
    if(!fileObj.files) {
        return false
    }

    let file = fileObj.files[0]
    let reader = new FileReader()
    let json_data = []

    reader.onload = function(e) {
        let data = e.target.result
        let binaryData = XLSX.read(data, {
            type: 'binary' //以二进制的方式读取
        })

        let sheet = binaryData.Sheets[binaryData.SheetNames[0]]
        json_data = XLSX.utils.sheet_to_json(sheet)
        callback(json_data)
    }

    reader.readAsBinaryString(file)
}


