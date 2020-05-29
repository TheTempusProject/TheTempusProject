<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad">
            <legend>Create a new Deck</legend>
            <p>All "would you rather" cards are grouped by decks. Please enter a name and description for your new deck.</p>
            <form action="" method="post" class="form-horizontal">
                <label for="title">Deck Name:</label>
                <input type="text" name="title" id="title" class="form-control" aria-describedby="urlHelp">
                <p id="urlHelp" class="form-text text-muted">
                    Must be 50 characters or less, only letters and spaces.
                </p>
                <div class="form-group">
                    <label for="entry" class="col-lg-3 control-label">Description</label>
                    <div class="col-lg-6">
                        <textarea class="form-control" name="entry" maxlength="2000" rows="10" cols="50" id="entry"></textarea>
                        <p id="urlHelp" class="form-text text-muted">
                            A brief description of your deck's theme.
                        </p>
                    </div>
                </div>
                <input type="hidden" name="token" value="{TOKEN}">
                <button name="submit" value="submit" type="submit" class="btn btn-lg btn-primary center-block">Submit</button>
            </form>
        </div>
    </div>
</div>