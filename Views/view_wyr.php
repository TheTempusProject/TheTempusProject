<legend>Create a new Card</legend>
<p>Select which Deck you would like to draw from and click play to get a new card from that deck.</p>
<form action="" method="post" class="form-horizontal">
    <div class="form-group">
    <label for="deckSelect">Deck:</label>
        <div class="col-lg-2">
            {deckSelect}
        </div>
        <p id="urlHelp" class="form-text text-muted">
            The deck you would like to play from
        </p>
    </div>
    <input type="hidden" name="token" value="{TOKEN}">
    <button name="submit" value="submit" type="submit" class="btn btn-lg btn-primary center-block">next card</button>
</form>