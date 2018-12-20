import vcTable from './table.vue'
import 'babel-polyfill'

const VueSuperTablePlugin = {
    install: function(Vue) {
        Vue.component(vcTable.name, vcTable)
    }
}

// global 情况下 自动安装
if (typeof window !== 'undefined' && window.Vue) {
    window.Vue.use(VueSuperTablePlugin)
}
// 导出模块
export default VueSuperTablePlugin