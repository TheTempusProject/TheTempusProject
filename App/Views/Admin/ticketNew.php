<script language="JavaScript" type="text/javascript">tinymce.init({ selector:'#description' });</script>
<form action="" method="post" class="form-horizontal">
    <legend>New Ticket</legend>
    <div class="form-group">
        <label for="name" class="col-lg-3 control-label">Name</label>
        <div class="col-lg-3">
            <input type="text" class="form-check-input" name="name" id="name">
        </div>
    </div>
    <div class="form-group">
        <label for="live" class="col-lg-3 control-label">Is the issue live? (on production)</label>
        <div class="col-lg-3">
            <input name="live" id="live" type="checkbox" value="true">
        </div>
    </div>
    <div class="form-group">
        <label for="category" class="col-lg-3 control-label">Category:</label>
        <div class="col-lg-2">
            {categorySelect}
        </div>
    </div>
    <div class="form-group">
        <label for="project" class="col-lg-3 control-label">Project:</label>
        <div class="col-lg-2">
            {projectSelect}
        </div>
    </div>
    <div class="form-group">
        <label for="description" class="col-lg-3 control-label">Description</label>
        <div class="col-lg-6">
            <textarea class="form-control" name="description" maxlength="2000" rows="10" cols="50" id="description"></textarea>
        </div>
    </div>
    <button name="submit" value="submit" type="submit" class="btn btn-lg btn-success center-block">Create</button>
    <input type="hidden" name="token" value="{TOKEN}">
</form>