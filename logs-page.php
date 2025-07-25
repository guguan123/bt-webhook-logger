<?php
/**
 * logs-page.php
 * BT WebHook 日志展示页面
 */

// 防止直接访问此文件
if (!defined('ABSPATH')) exit;

$page  = max(1, intval($_GET['paged'] ?? 1)); // 获取当前页码
$limit = 20; // 每页显示数量
$offset = ($page - 1) * $limit; // 计算偏移量

// 使用 WP_Query 查询自定义文章类型
$args = array(
	'post_type'      => self::POST_TYPE,
	'posts_per_page' => $limit,
	'paged'          => $page,
	'post_status'    => 'publish', // 只获取已发布的日志
	'order'          => 'DESC',
	'orderby'        => 'date',
);
$log_query = new WP_Query($args);

$rows = $log_query->posts; // 获取文章对象数组
$total = $log_query->found_posts; // 获取总日志数（用于分页）
?>
<div class="wrap">
	<h1>BT WebHook 日志</h1>

	<div class="btwl-toolbar">
		<p>共 <?php echo esc_html($total); ?> 条记录</p>
		<!-- 清空记录表单，包含 Nonce 字段和确认提示 -->
		<form method="post" onsubmit="return confirm('确定要清空所有 WebHook 日志吗？此操作不可逆！');">
			<?php wp_nonce_field('btwl_clear_logs_nonce'); ?>
			<input type="submit" name="btwl_clear_logs" class="button button-danger" value="清空所有记录">
		</form>
	</div>

	<table class="widefat fixed striped">
		<thead>
			<tr>
				<th style="width: 150px;">时间</th>
				<th style="width: 120px;">来源 IP</th>
				<th style="width: 100px;">格式</th>
				<th>请求体内容</th>
			</tr>
		</thead>
		<tbody>
		<?php if ($log_query->have_posts()) : ?>
			<?php foreach ($rows as $post):
				// 从文章元数据中获取日志详情
				$log_ip = get_post_meta($post->ID, '_btwl_ip', true);
				$log_body = print_r(get_post_meta($post->ID, '_btwl_body', true), true);
				$log_format = get_post_meta($post->ID, '_btwl_format', true);
			?>
				<tr>
					<td><?php echo esc_html($post->post_date); ?></td>
					<td><?php echo esc_html($log_ip); ?></td>
					<td><?php echo esc_html($log_format); ?></td>
					<td>
						<!-- 使用 pre 标签保留格式，并添加样式控制显示 -->
						<pre style="white-space: pre-wrap; word-break: break-all; margin: 0; padding: 5px; background: #f9f9f9; border: 1px solid #eee; overflow: auto; max-height: 200px;"><?php echo esc_html($log_body); ?></pre>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php else : ?>
			<tr>
				<td colspan="4">暂无 WebHook 日志。</td>
			</tr>
		<?php endif; ?>
		</tbody>
	</table>
	<?php
	// 简单分页导航
	// 使用 $log_query->max_num_pages 获取总页数
	$max = $log_query->max_num_pages;
	if ($max > 1) {
		echo '<div class="tablenav"><div class="tablenav-pages">';
		echo esc_html(paginate_links([
			'base'    => add_query_arg('paged', '%#%'), // 分页链接的基础 URL
			'format'  => '',
			'current' => $page,
			'total'   => $max,
			'prev_text' => '&laquo;', // 上一页文本
			'next_text' => '&raquo;', // 下一页文本
		]));
		echo '</div></div>';
	}
	?>
</div>
