<template>
    <div class="aaa">

        <div id="main" style="width: 900px;height:600px;"></div>
<!--        <div id="a" style="width: 600px;height:400px;"></div>-->
    </div>
</template>

<script>
   import echarts from 'echarts'
   export default {
       data() {
            return {
                x: [],
                value: [],
            }
       },

       methods: {

           async getCurveGoods() {
               const {data: res} = await this.$http.get('/curve');
               this.x = res.content.x;
           }

       },

       mounted() {
           this.getCurveGoods();
           // 基于准备好的dom，初始化echarts实例
           var myChart = echarts.init(document.getElementById('main'));
           var option = {

               tooltip: {trigger: 'axis' },
               legend: {
                   data: ['女装', '男装', '上衣']
               },
               xAxis: {
                   type: 'category',   // 还有其他的type，可以去官网喵两眼哦
                   data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],   // x轴数据
                   name: '日期',   // x轴名称
                   // x轴名称样式
                   nameTextStyle: {
                       fontWeight: 600,
                       fontSize: 18
                   }
               },
               yAxis: {},
               series: [ {
                   name: '女装',
                   data: [30, 30, 56, 23, 43, 45, 12],
                   type: 'line'
               },
                   {
                       name: '男装',
                       data: [66, 25, 46, 63, 13, 5, 63],
                       type: 'line'
                   },
                   {
                       name: '上衣',
                       data: [10, 70, 6, 23, 53, 65, 32],
                       type: 'line'
                   },
                   ]
           };
           myChart.setOption(option);
           a.setOption(option);
       },
   }

</script>

<style scoped>
    .aaa{
        display: flex;
        margin-top:40px;
    }
</style>
