<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad">
            <legend>Create a new Card</legend>
            <p>The concept is simple. The prompt is would you rather: A, or B. Simply type your a and b options in the card-text box and select a deck to add the card to.</p>
            <form action="" method="post" class="form-horizontal">
                <div class="row">
                    <div class="col-lg-4 col-lg-offset-4">
                        <div class="form-group">
                            <label for="deckSelect">Deck:</label>
                                {deckSelect}
                            <p id="urlHelp" class="form-text text-muted">The deck you would like to add this card to.</p>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="entry" class="col-lg-3 control-label">Card Text</label>
                    <div class="col-lg-6">
                        <textarea class="form-control" name="entry" maxlength="2000" rows="10" cols="50" id="entry"></textarea>
                        <p id="urlHelp" class="form-text text-muted">Text should be formatted as "A or B".</p>
                    </div>
                </div>
                <input type="hidden" name="token" value="{TOKEN}">
                <button name="submit" value="submit" type="submit" class="btn btn-lg btn-primary center-block">Submit</button>
            </form>
        </div>
    </div>
</div>