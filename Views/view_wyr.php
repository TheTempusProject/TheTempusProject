<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad">
            <legend>Would you rather...</legend>
            <p>Select which Deck you would like to draw from and either click random or next card to begin playing.</p>
            <form action="" method="post" class="form-horizontal">
                <div class="row">
                    <div class="col-lg-4 col-lg-offset-4">
                        <div class="form-group">
                            <label for="deckSelect">Deck:</label>
                                {deckSelect}
                            <p id="urlHelp" class="form-text text-muted">The deck you would like to play from.</p>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="currentCard" value="{ID}">
                <input type="hidden" name="token" value="{TOKEN}">
                <div class="row">
                    <button name="submit" value="next" type="submit" class="btn btn-lg btn-primary center-block">next card</button><br>
                </div>
                <div class="row">
                    <button name="submit" value="random" type="submit" class="btn btn-lg btn-primary center-block">random card</button>
                </div>
            </form>
        </div>
    </div>
</div>