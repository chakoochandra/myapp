<?php if (isset($view)): ?>
	<div class="w-100 pb-5" <?php echo isset($cardStyle) ? $cardStyle : '' ?> style="min-height: 100vh;">
		<?php $this->load->view($view) ?>
	</div>
<?php endif ?>