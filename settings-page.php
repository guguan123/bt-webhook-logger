<?php
/**
 * settings-page.php
 * BT WebHook 设置页
 */

// 防止直接访问此文件
if (!defined('ABSPATH')) exit;

// 获取当前保存的 Access Key 和邮件设置
$current_access_key = get_option(self::OPTION_ACCESS_KEY, '');
$current_enable_email = get_option(self::OPTION_ENABLE_EMAIL, '0'); // 默认为禁用
$current_target_email = get_option(self::OPTION_TARGET_EMAIL, '');
// 获取 REST API 的基础 URL
$rest_api_base_url = get_rest_url(null, 'bt-webhook-logger/v1/receive');
if (empty($current_access_key)) {
	$webhook_url_example = $rest_api_base_url;
} else {
	$webhook_url_example = add_query_arg('access_key', $current_access_key, $rest_api_base_url);
}
?>
<div class="wrap">
	<h1>BT WebHook 设置</h1>

	<form method="post">
		<?php wp_nonce_field('btwl_settings_nonce'); // Nonce 字段用于安全验证 ?>
		<table class="form-table">
			<tr class="btwl-settings-section">
				<th scope="row"><label for="btwl_access_key">Access Key</label></th>
				<td>
					<input type="text" id="btwl_access_key" name="btwl_access_key" value="<?php echo esc_attr($current_access_key); ?>" class="regular-text">
					<p class="description">设置一个 Access Key 来保护你的 WebHook。留空表示不需要 Access Key（不推荐）。</p>
					<p class="description">你的 WebHook 地址: <?php echo esc_url($webhook_url_example); ?></p>
					<?php if (empty($current_access_key)) : ?>
						<p class="description" style="color: red;">当前未设置 Access Key，WebHook 地址对所有请求开放，存在安全风险。</p>
					<?php endif; ?>
				</td>
			</tr>
			<tr class="btwl-settings-section">
				<th scope="row">邮件通知</th>
				<td>
					<label for="btwl_enable_email">
						<input type="checkbox" id="btwl_enable_email" name="btwl_enable_email" value="1" <?php checked('1', $current_enable_email); ?>>
						启用 WebHook 邮件通知
					</label>
					<p class="description">勾选此项以在每次收到 WebHook 时发送邮件通知。</p>
				</td>
			</tr>
			<tr class="btwl-settings-section">
				<th scope="row"><label for="btwl_target_email">目标邮箱地址</label></th>
				<td>
					<input type="email" id="btwl_target_email" name="btwl_target_email" value="<?php echo esc_attr($current_target_email); ?>" class="regular-text">
					<p class="description">接收 WebHook 通知邮件的邮箱地址。请确保您的 WordPress 已正确配置邮件发送服务。</p>
					<?php if (!empty($current_enable_email) && !is_email($current_target_email)) : ?>
						<p class="description" style="color: red;">邮件通知已启用，但目标邮箱地址无效，请检查。</p>
					<?php endif; ?>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" name="btwl_save_settings" id="submit" class="button button-primary" value="保存设置">
			<!-- 按钮点击时调用 JavaScript 函数生成随机密钥 -->
			<button type="button" class="button button-secondary" onclick="document.getElementById('btwl_access_key').value = generateRandomKey();">生成随机密钥</button>
		</p>
	</form>
</div>
