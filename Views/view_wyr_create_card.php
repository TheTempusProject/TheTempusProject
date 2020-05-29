<legend>Create a new Card</legend>
<p>The concept is simple. The prompt is would you rather: a, or B. Simply type your a and b options in the card-text box and select a deck to add the card to.</p>
<form action="" method="post" class="form-horizontal">
    <div class="form-group">
    <label for="deckSelect">Deck:</label>
        <div class="col-lg-2">
            {deckSelect}
        </div>
        <p id="urlHelp" class="form-text text-muted">
            The deck you would like to add this card to
        </p>
    </div>
    <div class="form-group">
        <label for="entry" class="col-lg-3 control-label">Your cardText should be formatted as "A or B"</label>
        <div class="col-lg-6">
            <textarea class="form-control" name="entry" maxlength="2000" rows="10" cols="50" id="entry"></textarea>
        </div>
    </div>
    <input type="hidden" name="token" value="{TOKEN}">
    <button name="submit" value="submit" type="submit" class="btn btn-lg btn-primary center-block">Submit</button>
</form>