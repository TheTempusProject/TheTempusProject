<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad">
            <p>Select which Deck you would like to draw from and click play to get a new card from that deck.</p>
            <form action="" method="post" class="form-horizontal">
                <div class="form-group">
                    <label for="deckSelect">Deck:</label>
                    <div class="col-lg-2">
                        {OPTION=currentDeck}
                        {deckSelect}
                    </div>
                    <p id="urlHelp" class="form-text text-muted">
                        The deck you would like to play from
                    </p>
                </div>
                <input type="hidden" name="currentCard" value="{ID}">
                <input type="hidden" name="token" value="{TOKEN}">
                <button name="submit" value="next" type="submit" class="btn btn-lg btn-primary center-block">next card</button>
                <button name="submit" value="random" type="submit" class="btn btn-lg btn-primary center-block">random card</button>
            </form>
        </div>
    </div>
</div>