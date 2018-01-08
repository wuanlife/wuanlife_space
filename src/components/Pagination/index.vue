<template>
	<div class="pagination">
		<div class="center">
			<span v-if="pagination.currentPage != 1">
			<span @click="pagination.currentPage--">上一页</span>
			</span>
			
			<span v-else>
			<span class="disabled">上一页</span>
			</span>
			
			<el-pagination layout="pager" :current-page.sync="pagination.currentPage" :page-count="pagination.pageCount">
			</el-pagination>
			
			<span v-if="pagination.currentPage != pagination.pageCount">
			<span @click="pagination.currentPage++">下一页</span>
			</span>
			
			<span v-else>
			<span class="disabled">下一页</span>
			</span>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				pagination: {
					pageCount: 245,
					currentPage: 1,
					limit: 20,
				}
			}
		}
	}
</script>
<style>
	.pagination {
		width: inherit;
	}
	
	.center {
		width: -webkit-fit-content;
		margin: 0 auto;
	}
	
	.center * {
		padding: 0;
		margin: 0;
		display: inline-block;
		/*middle和baseline无法搞定时,考虑用line-hight*/
		/*vertical-align: 80%;*/
		line-height: 20px;
	}
	
	.pagination span {
		cursor: pointer;
		-moz-user-select: none;
		-webkit-user-select: none;
		-ms-user-select: none;
	}
	
	.disabled {
		color: darkgray;
		cursor: auto;
	}
</style>