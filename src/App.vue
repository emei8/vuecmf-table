<template>
  <div id="app">
    <h2>vuecmf-table demo</h2>
    <vc-table :header-action="headerAction" server="http://www.b2b.com/api/Table/index" page="page" :limit="20"></vc-table>

  </div>
</template>

<script>

export default {
  name: 'app',
  data () {
    return {
        headerAction:[
            {
                event: this.fun,
                title: '测试',
                type: 'success',
                icon: 'fa fa-plus-circle'
            },
            {
                event: this.fun2,
                title: '测试2',
                type: 'primary',
                icon: 'fa fa-edit'
            }
        ]
    }
  }
}
</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}

h1, h2 {
  font-weight: normal;
}

ul {
  list-style-type: none;
  padding: 0;
}

li {
  display: inline-block;
  margin: 0 10px;
}

a {
  color: #42b983;
}
</style>
