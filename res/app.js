function stubSubmit(event) {
	event.preventDefault();
	event.stopPropagation();
}

function triggerErrorMessage(event, $message, text) {
	$message.show();
	$message.html(text);
}

function checkEmail($form, event) {
	event.preventDefault();

	var $email = $form.find('input[name=email]');
	var $emailMessage = $form.find('.invalid-feedback.email');

	if ($email.val() == '') {
		triggerErrorMessage(event, $emailMessage, 'Email field must be filled');
		return false;
	} else if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($email
			.val()) == false) {
		triggerErrorMessage(event, $emailMessage, 'Invalid email');
		return false;
	} else if($email.val().length > 128) {
		triggerErrorMessage(event, $emailMessage, 'Email is too long');
		return false;
	}

	$emailMessage.hide();
	return true;
}

function checkPassword($form, event) {
	event.preventDefault();

	var $password = $form.find('input[name=password]');
	var $passwordMessage = $form.find('.invalid-feedback.password');

	if ($password.val() == '') {
		triggerErrorMessage(event, $passwordMessage, 'Password must be filled');
		return false;
	} else if ($password.val().length < 6) {
		triggerErrorMessage(event, $passwordMessage,
				'Password can not be less then 6 symbols');
		return false;
	}

	$passwordMessage.hide();
	return true;
}

function checkPasswordConfirmation($form, event) {
	event.preventDefault();

	var $password = $form.find('input[name=password]');
	var $passwordConfirmation = $form.find('input[name=password-confirmation]');
	var $passwordConfirmationMessage = $form.find('.invalid-feedback.password-confirmation');

	if ($passwordConfirmation.val() == '') {
		triggerErrorMessage(event, $passwordConfirmationMessage,
				'Password confirmation must be filled');
		return false;
	} else if ($passwordConfirmation.val() != $password.val()) {
		triggerErrorMessage(event, $passwordConfirmationMessage,
				'Password and password confirmation must be the same');
		return false;
	}

	$passwordConfirmationMessage.hide();
	return true;
}

function showErrorMessage(message) {
	jQuery('#error-message .message').html(message);
	jQuery('#error-message').modal();
}

function initUserForms() {
	jQuery('#login-modal form').on('submit', async function(event) {
		var $modal = jQuery('#login-modal');

		if(checkEmail($modal, event) && checkPassword($modal, event)) {
			let response = await fetch('/ajax/login', {
				method: 'POST',
				body: new FormData($modal.find('form')[0])
			});

			let result = await response.json();
			if(result == 'ok'){
				document.location.reload();
			}
			else{
				jQuery('#login-modal .request').html(result);
			}
		}
	});

	jQuery('#registration-modal form').on('submit', async function(event) {
		var $modal = jQuery('#registration-modal');

		if(checkEmail($modal, event) && checkPassword($modal, event) && checkPasswordConfirmation($modal, event)) {
			let response = await fetch('/ajax/register', {
				method: 'POST',
				body: new FormData($modal.find('form')[0])
			});

			let result = await response.json();
			if(result == 'ok'){
				document.location.reload();
			}
			else{
				jQuery('#registration-modal .request').html(result);
			}
		}
	});
}

function hitUser(element) {
	jQuery('#hit-modal input[name=user-id]').val(jQuery(element).attr('data-id'));
}

function initFightLauncher() {
	// setup dialogs
	jQuery('#hit-modal button.btn-primary').on('click', async function() {
		var formData = new FormData();
		formData.append('user-id', jQuery('#hit-modal input[name=user-id]').val());

		let response = await fetch('/ajax/invite', {
				method: 'POST',
				body: formData
			});

		let result = await response.json();

		jQuery('#hit-modal').modal('hide');

		if(result == 'ok') {
			// invitation was created dialog
			jQuery('#info-message').modal('show');
			jQuery('#info-message .modal-body').html('Invitation was sent');
		}
		else {
			// show error dialog
			jQuery('#info-message').modal('show');
			jQuery('#info-message .modal-body').html(result);
		}
	});
}

function initPickInviteForms() {
	if(jQuery('.users-list-container').length) {
		jQuery('#prompt-modal button.btn-primary').on('click', async function(){
			// accept invite
			var formData = new FormData();
			formData.append('invite-id', jQuery('#prompt-modal input[name=data-id]').val());

			let response = await fetch('/ajax/accept-invite', {
				method: 'POST',
				body: formData
			});

			let result = await response.json();
		});

		jQuery('#prompt-modal button.btn-danger').on('click', async function(){
			// decline invite
			var formData = new FormData();
			formData.append('invite-id', jQuery('#prompt-modal input[name=data-id]').val());

			let response = await fetch('/ajax/decline-invite', {
				method: 'POST',
				body: formData
			});

			let result = await response.json();
			jQuery('#prompt-modal').modal('hide');
		});
	}
}

function setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {   
    document.cookie = name+'=; Max-Age=-99999999;';  
}

function needDisplayResult(battleHistory) {
	if(battleHistory.length==0){
		return false;
	}

	var lastRoundDisplayed = 1*getCookie('last-round-id');

	if(battleHistory[battleHistory.length-1].id > lastRoundDisplayed || lastRoundDisplayed == null) {
		setCookie('last-round-id', battleHistory[battleHistory.length-1].id);
		return true;
	}

	return false;
}

function compileMoveMessage(firstMove, secondMove) {
	return 'Your move: <b>' + firstMove + '</b><br/>' + 
		'Your opponent\'s move: <b>' + secondMove + '</b><br/><br/>';
}

function displayRoundResultIfNecessary(result) {
	if(needDisplayResult(result.history)){
		jQuery('#info-message').modal('show');
		var message = '';
		var lastRound = result.history[result.history.length-1];
		if(result.you_are == lastRound.winner) {
			message = 'You have won the last round<br/>';
		} else if(lastRound.winner == 'none') {
			message = 'We don\'t have winner in the last round<br/>';
		} else {
			message = 'Your opponent have won the last round<br/>';
		}

		if(result.you_are == 'usera') {
			message = message + compileMoveMessage(lastRound['usera_move'], lastRound['userb_move']);
		}
		if(result.you_are == 'userb') {
			message = message + compileMoveMessage(lastRound['userb_move'], lastRound['usera_move']);
		}
		if(lastRound.usera_move == 'no move' || lastRound.userb_move == 'no move') {
			message = message + 'It\'s timeout'
		}

		jQuery('#info-message .modal-body').html(message);
		setTimeout(function(){
			jQuery('#info-message').modal('hide');
			}, 5000
		);

		startCountdown();
		jQuery('.remaining-time').html(30);
	}
}

function displayBattleHistory(result) {
	var battleHistory = '';
	if(result.history.length==0) {
		battleHistory = 'No rounds yet<br/>';
	}
	for(var i = 0; i<result.history.length; i++) {
		result.history[i]['usera_move'] = result.history[i]['usera_move'] == 'none' ? 'no move' : result.history[i]['usera_move'];
		result.history[i]['userb_move'] = result.history[i]['userb_move'] == 'none' ? 'no move' : result.history[i]['userb_move'];
	}

	if(result.you_are == 'userb') {
		for(var k = 0; k<result.history.length; k++) {
			battleHistory = battleHistory + 'You: <b>' + result.history[k]['userb_move'] + 
				'</b> and your opponent: <b>' + result.history[k]['usera_move'] + '</b><br/>';
		}
	} else {
		for(var j = 0; j<result.history.length; j++) {
			battleHistory = battleHistory + 'You: <b>' + result.history[j]['usera_move'] + 
				'</b> and your opponent: <b>' + result.history[j]['userb_move'] + '</b><br/>';
		}
	}

	jQuery('.rounds-list-container').html(battleHistory);
}

function displayBattleResultIfNecessary(result) {
	if(result.one_user_left_the_battle) {
		jQuery('#info-message').modal('hide');
		jQuery('#battle-result').modal({backdrop: 'static', keyboard: false});
		jQuery('#battle-result .modal-body').html('Your opponent left the battle</b>');
	} else if(result.usera_wins >= 5) {
		jQuery('#info-message').modal('hide');
		jQuery('#battle-result').modal({backdrop: 'static', keyboard: false});
		jQuery('#battle-result .modal-body').html('The winner is <b>' + result.usera_login + '</b>');
	} else if(result.userb_wins >= 5) {
		jQuery('#info-message').modal('hide');
		jQuery('#battle-result').modal({backdrop: 'static', keyboard: false});
		jQuery('#battle-result .modal-body').html('The winner is <b>' + result.userb_login + '</b>');
	}
}

async function makeMove(move) {
	var formData = new FormData();
	formData.append('move', move);

	let response = await fetch('/ajax/make-move', {
		method: 'POST',
		body: formData
	});

	let result = await response.json();
	if(result == 'ok') {
		jQuery('#info-message').modal('show');
		jQuery('#info-message .modal-body').html('You\'ve made your move.');
	}
	else {
		jQuery('#error-message').modal('show');
		jQuery('#error-message .modal-body').html(result);
	}
}

function leaveBattlePrompt() {
	jQuery('#leave-battle').modal('show');
}

async function leaveBattle() {
	let response = await fetch('/ajax/leave-battle', {
		method: 'POST'
	});

	await response.json();

	document.location.reload();
}

function parseLongPollWaitResult(result) {
	if('users-table' in result) {
		jQuery('.users-list-container').html(result['users-table']);
	}

	if('invite-id' in result) {
		jQuery('#prompt-modal').modal('show');
		jQuery('#prompt-modal .modal-body').html('You have invite. Accept?');
		jQuery('#prompt-modal').find('input[name=data-id]').val(result['invite-id']);
	}

	if('battle-started' in result && result['battle-started']) {
		document.location.reload();
		return;
	}

	runLongPollWait();
}

async function runLongPollWait() {
	if(jQuery('.users-list-container').length) {
		let response = await fetch('/ajax/long-poll-wait/');

		let result = await response.json();

		parseLongPollWaitResult(result);
	}
}

var countDown = 0;

function startCountdown() {
	if(countDown != 0) {
		clearInterval(countDown);
	}

	countDown = setInterval(function(){
		var currentTime = jQuery('.remaining-time').html();
		currentTime -= 1;
		jQuery('.remaining-time').html(currentTime);

		if(currentTime == 0) {
			jQuery('.remaining-time').html(30);
		}
	}, 1000);
}

async function initRoundsHistory() {
	if(jQuery('.rounds-list-container').length) {
		// setup initial data
		let response = await fetch('/ajax/battle-runner');

		let result = await response.json();

		displayBattleHistory(result);

		jQuery('.remaining-time').html(result.remaining_time);
		
		startCountdown();
	}
}

async function runLongPollBattle() {
	if(jQuery('.rounds-list-container').length) {
		// start long poll
		let response = await fetch('/ajax/long-poll-battle/');

		let result = await response.json();

		if('history' in result) {
			displayBattleHistory(result);

			displayRoundResultIfNecessary(result);

			displayBattleResultIfNecessary(result);
		}

		if(result.one_user_left_the_battle == false) {
			runLongPollBattle();
		}
	}
}

(function() {
	initUserForms();
	initPickInviteForms();

	runLongPollWait();

	initFightLauncher();
	runLongPollBattle();
	initRoundsHistory();
})();
