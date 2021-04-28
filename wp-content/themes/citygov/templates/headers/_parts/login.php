<div id="popup_login" class="popup_wrap popup_login bg_tint_light">
	<a href="#" class="popup_close"></a>
	<div class="form_wrap">
		<div class="form_left">
			<form action="<?php echo wp_login_url(); ?>" method="post" name="login_form" class="popup_form login_form">
				<input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url('/')); ?>">
				<div class="popup_form_field login_field iconed_field icon-user"><input type="text" id="log" name="log" value="" placeholder="<?php esc_attr_e('Login or Email', 'citygov'); ?>"></div>
				<div class="popup_form_field password_field iconed_field icon-lock"><input type="password" id="password" name="pwd" value="" placeholder="<?php esc_attr_e('Password', 'citygov'); ?>"></div>
				<div class="popup_form_field remember_field">
					<a href="<?php echo esc_url(wp_lostpassword_url(get_permalink())); ?>" class="forgot_password"><?php esc_html_e('Forgot password?', 'citygov'); ?></a>
					<input type="checkbox" value="forever" id="rememberme" name="rememberme">
					<label for="rememberme"><?php esc_html_e('Remember me', 'citygov'); ?></label>
				</div>
				<div class="popup_form_field submit_field"><input type="submit" class="submit_button" value="<?php esc_attr_e('Login', 'citygov'); ?>"></div>
			</form>
		</div>
        <div class="form_right">
            <div class="login_socials_title"><?php esc_html_e('You can login using your social profile', 'citygov' ); ?></div>
            <?php
            $social_login = str_replace('[', '', citygov_get_theme_option('social_login'));
            $social_login = str_replace(']', '', $social_login);
            if (strlen($social_login) > 0) {
                ?>
                <div class="loginSoc login_plugin">
                    <?php
                    if (strlen($social_login) > 0) echo do_shortcode( '[' . $social_login . ']' );
                    ?>
                </div>
            <?php } else {?>
                <div><?php _e("Install social plugin that has it's own SHORTCODE and add it to Theme Options - Socials - 'Login via Social network' field. We recommend: Wordpress Social Login", 'citygov'); ?></div>
            <?php }?>
            <div class="result message_block"></div>
        </div>
	</div>	<!-- /.login_wrap -->
</div>		<!-- /.popup_login -->
