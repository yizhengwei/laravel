<template>
 <el-card>aa</el-card>
</template>

<script>
    export default {
       data() {
           return{
               id: 0,
           }
       },

        methods: {
           getOrderList() {
               var that=this;
               var form={}
               form.id=this.$route.query.id
               if (form.id === undefined) return;

            }
        },

        mounted() {
            this.getOrderList();
        },

    }
</script>

<style scoped>

</style>
