<template>
	<li class="index-card">
		<header>
			<img v-bind:src="item.author.image">
			<span class="clickable">{{ item.author.name }}</span>
			<!--<span class="clickable" @click="$router.push({ path: `/planet/${item.group.id}`, query: { name: item.group.name }})">{{ item.group.name }}</span>-->
			<time>{{ item.create_at | formatTime}}</time>
		</header>
		<div class="index-card-content">
			<h1 @click="$router.push({path: `/topic/${item.id}`, query: { name: item.title }})">{{ item.title }}</h1>
			<div class="preview-html">
				{{ item.content }}
			</div>
			<div class="preview-imgs">
				<img v-bind:src="imglink" v-for="imglink in item.image">
			</div>
		</div>
	</li>
</template>

<script>
	export default{
		data(){
			return{
			}
		},
		props: {
			item: {
				type: Object,
				required: true
			}
		}
	}
</script>

<style type="stylesheet/scss" lang="scss" scoped>
    .index-card {
      padding: 38px 26px 0px 26px;
      width: 714px;   
      background-color: #ffffff; 
      border-radius: 4px; 
      &:not(:first-child) {
        margin-top: 20px;
      }
      &:last-child {
        margin-bottom: 108px;
      }
      header {    
        display: flex;    
        align-items: center;    
        margin-bottom: 27px;
        font-size:12px;   
        color:#999999;
        & > .clickable {
          transition: all 0.2s ease-in-out;
          &:hover {
            color: #5677fc;
          }
        }
        img {
          width: 50px;    
          height: 50px;   
          border-radius: 100%;    
          margin-right: 24px;   
        }
        span {    
          font-size: 24px;
          color: #333333;
          &:not(:first-child) {   
            margin-left: 5px;   
          }
        }
        time {    
          margin: 6px 0 0 42px;
          font-size: 18px;
          color: #999999;
          /*flex-grow: 1;*/
          /*line-height: 20px;
          display: inline-block;*/
          /*text-align: right;*/
        }   
      }
      div.index-card-content {
        h1 {
          display: inline-block;
          position: relative;
          cursor: pointer;
          margin-bottom: 19px;
          text-overflow: ellipsis;
          overflow: hidden;
          color: #333333;
          font-family:PingFangHK-Semibold;
          font-size:24px;
          white-space: nowrap;
          // hover animation
          &::after {
            content: '';
            transition: all 0.5s ease-in-out;
            transform: scaleX(0);
            position: absolute;
            width: 100%;
            height: 2px;
            bottom: 0;
            left: 0;
            background: #2e5897;
          }
          &:hover {
            &::after {
              transform: scaleX(1);
            }
          }
        }
        div.preview-html {
          /*margin-bottom: 12px;*/
          word-break: break-all;
          font-family: PingFangHK-Medium;
          font-size:18px;
          color:#444444;
          letter-spacing:0;
          text-align:justify;
        }
        div.preview-imgs {
          display: flex;
          margin-top: 33px;
          img {
            margin-right: 15px;
            width: 210px;
            height: 210px;
            margin-bottom: 20px;
          }

        }
      }
    }   
</style>