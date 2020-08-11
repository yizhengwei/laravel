<template>
 <el-card>
     <el-table
         ref="multipleTable"
         :data="tableData"
         tooltip-effect="dark"
         style="width: 100%"
         border
        >
         <el-table-column
             type="selection"
             width="55">
         </el-table-column>
         <el-table-column
             label="商品">
             <template slot-scope="scope">
                 <div class="shangpi">
                     <div class="left_img">
                         <img
                             :src="scope.row.list_img"
                             v-if="scope.row.img!=''"
                             style="height: auto;">
                         <img v-else src="/images/logo.png" style="height: auto;">
                     </div>
                     <div class="right_content">
                         <div class="top_title">
                             <a v-if="scope.row.goods_name!=''">{{scope.row.goods_name}}</a>
                             <span v-if="scope.row.goods_name==''">暂无</span>
                         </div>
                         <div class="code_1">
                             <span v-text="scope.row.goods_no" v-if="scope.row.goods_no!=''"></span>
                             <span v-if="scope.row.goods_no==''">暂无</span>
                         </div>
                     </div>
                 </div>
             </template>
         </el-table-column>
         <el-table-column
             label="商品规格"
             prop="spec_name"

         >

         </el-table-column>
         <el-table-column
             prop="sales_num"
             label="销售价格(元)"
           >
         </el-table-column>
         <el-table-column
             prop="count"
             label="购买数量(件)"
             >
         </el-table-column>
         <el-table-column
             prop="d"
             label="下单时间"
         >
         </el-table-column>

     </el-table>
 </el-card>
</template>

<script>
    export default {
       data() {
           return{
               id: 0,
               tableData: [],
           }
       },

        methods: {
           async getOrderList() {
               var that=this;
               var form={}
               form.id=this.$route.query.id
               if (form.id === undefined) return;
               const {data: res} =  await this.$http.post('/orderDetail', form);
               this.tableData = res.content;
            }
        },

        mounted() {
            this.getOrderList();
        },

    }
</script>

<style scoped>

</style>
