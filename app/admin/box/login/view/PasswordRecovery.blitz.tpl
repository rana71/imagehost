<div class="login-container">
	
	<div class="login-header login-caret">
		
		<div class="login-content">
			
			
			<p class="description">Podaj adres e-mail na który założone jest konto, prześlemy na niego link służacy do zmiany hasła.</p>
			
			<!-- progress bar indicator -->
			<div class="login-progressbar-indicator">
				<h3>%</h3>
				<span>trwa odnajdowanie Twojego konta...</span>
			</div>
		</div>
		
	</div>
	
	<div class="login-progressbar">
		<div></div>
	</div>
	
	<div class="login-form">
		
		<div class="login-content">
			
			<form method="post" role="form" id="form_forgot_password">
				
				<div class="form-forgotpassword-success">
					<i class="entypo-check"></i>
					<h3>Nowe hasło zostało wysłane.</h3>
					<p>Zostało wysłane na Twoja skrzynkę e-mail. Używaj do od kolejnego logowania.</p>
				</div>
				
				<div class="form-steps">
					
					<div class="step current" id="step-1">
					
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="entypo-mail"></i>
								</div>
								
								<input type="text" class="form-control" name="email" id="email" placeholder="Adres e-mail" data-mask="email" autocomplete="off" />
							</div>
						</div>
						
						<div class="form-group">
							<button type="submit" class="btn btn-info btn-block btn-login">
								Przypomnij hasło
								<i class="entypo-right-open-mini"></i>
							</button>
						</div>
					
					</div>
					
				</div>
				
			</form>
			
			
			<div class="login-bottom-links">
				
				<a href="{{this::url('Login::login')}}" class="link">
					<i class="entypo-lock"></i>
					Wróć do logowania
				</a>
				
			</div>
			
		</div>
		
	</div>
	
</div>
<script src="/admin/assets/js/neon-forgotpassword.js"></script>