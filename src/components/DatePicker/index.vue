<template>
  <div class="date-picker" @click="open" :class="{ 'date-picker-clicked': isClick }">
      <span>{{ defaultNum | twoNumberWithZero }}<icon-svg icon-class="triangle1" class="avatar-icon"></icon-svg></span>
      <ul v-if="isClick">
          <li v-for="n in (max - min + 1)" :key="n" v-on:click="picker(n)">{{ (min + n - 1) | twoNumberWithZero}}</li>
      </ul>
  </div>
</template>

<script>
export default {
  name: 'datePicker',
  props: {
    min: {
      type: Number,
      required: true
    },
    max: {
      type: Number,
      required: true
    },
    defaultNum: {
      type: Number,
      required: true
    }
  },
  data () {
    return {
      isClick: false
    }
  },
  methods: {
    picker: function (value) {
      this.defaultNum = this.min + value - 1
      this.$emit('pick', this.defaultNum)
    },
    open: function () {
      this.isClick = !this.isClick
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.date-picker{
    max-width: 80px;
    text-align: center;
    box-shadow: 0px 3px 7px 0px
    rgba(99, 99, 99, 0.16);
  border: solid 2px rgba(171, 171, 171, 0.45);
    background-color: rgba(248, 249, 250, 0.45);
  border-radius: 4px;
    padding: 14px 6px 13px 6px;
    cursor: pointer;
    position: relative;
    span{
        font-size: 17px;
        color: #757575;
    }
    ul{
        height: 174px;
        width: 100%;
      background-color: #f8f9fa;
      box-shadow: 0px 3px 7px 0px
    rgba(86, 119, 252, 0.4);
      border-radius: 4px;
      border: solid 2px #99b3e3;
        font-size: 16px;
        color: #5677fc;
        padding: 10px 0 10px 0;
        overflow-y: scroll;
        cursor: pointer;
        position: absolute;
        top: 48px;
        left: 0;
        li{
            margin-bottom: 5px;
            padding: 5px 0 5px 0x;
            &:hover{
                color: white;
                background-color: #5677fc;
            }
        }
    }
}
.date-picker-clicked{
    background-color: rgba(248, 249, 250, 0.4);
    box-shadow: 0px 3px 7px 0px
    rgba(86, 119, 252, 0.16);
    border: solid 2px rgba(0, 64, 185, 0.4);
}
.avatar-icon{
    margin-left: 5px;
    color: #5677fc;
}
</style>
