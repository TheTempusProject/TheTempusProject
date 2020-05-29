<legend>Create a new Deck</legend>
<p>All would you rather cards are grouped by decks. Please enter a name and description for your new deck.</p>
<form action="" method="post" class="form-horizontal">
    <label for="title">Title:</label>
    <input type="text" name="title" id="title" class="form-control" aria-describedby="urlHelp">
    <p id="urlHelp" class="form-text text-muted">
        50 characters, only letters and spaces.
    </p>
    <div class="form-group">
        <label for="entry" class="col-lg-3 control-label">Give a quick description of your new deck.</label>
        <div class="col-lg-6">
            <textarea class="form-control" name="entry" maxlength="2000" rows="10" cols="50" id="entry"></textarea>
        </div>
    </div>
    <input type="hidden" name="token" value="{TOKEN}">
    <button name="submit" value="submit" type="submit" class="btn btn-lg btn-primary center-block">Submit</button>
</form>