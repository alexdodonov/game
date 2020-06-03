<div class="container pt-5">
	<h1>Fight!</h1>
	<h3>Rounds history:</h3>
	<div class="rounds-list-container"></div>
	<h3 class="pt-5">Remaining time:</h3>
	<span class="remaining-time"></span> seconds

	<h3 class="pt-5">Make your move</h3>
	<button onclick="makeMove('stone');" type="button"
		class="btn btn-primary" data-id="stone">stone</button>
	<button onclick="makeMove('scissors');" type="button"
		class="btn btn-primary" data-id="scissors">scissors</button>
	<button onclick="makeMove('paper');" type="button"
		class="btn btn-primary" data-id="paper">paper</button>
	<button onclick="makeMove('lizard');" type="button"
		class="btn btn-primary" data-id="lizard">lizard</button>
	<button onclick="makeMove('spok');" type="button"
		class="btn btn-primary" data-id="spok">spok</button>
	<button onclick="leaveBattlePrompt();" type="button"
		class="btn btn-danger">Leave</button>
</div>

<!-- The Battle Result Modal -->
<div class="modal" id="battle-result">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Battle is over</h4>
			</div>

			<!-- Modal body -->
			<div class="modal-body"></div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="document.location.reload()">OK</button>
			</div>
		</div>
	</div>
</div>

<!-- The Leave Battle Modal -->
<div class="modal" id="leave-battle">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Are you shure?</h4>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				Do you want to leave this battle?
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">No</button>
				<button type="button" class="btn btn-danger" onclick="leaveBattle()">Yes</button>
			</div>
		</div>
	</div>
</div>