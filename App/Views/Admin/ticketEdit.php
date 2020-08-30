<script language="JavaScript" type="text/javascript">tinymce.init({ selector:'#description' });</script>
<form action="" method="post" class="form-horizontal">
    <legend>Edit Ticket: {name}</legend>
    <div class="form-group">
        <label for="name" class="col-lg-3 control-label">Name</label>
        <div class="col-lg-3">
            <input type="text" class="form-check-input" name="name" id="name" value="{name}">
        </div>
    </div>
    <div class="form-group">
        <label for="live" class="col-lg-3 control-label">Is the issue live? (on production)</label>
        <div class="col-lg-3">
            <input name="live" id="live" type="checkbox" value="true"{checked}>
        </div>
    </div>
    <div class="form-group">
        <label for="category" class="col-lg-3 control-label">Category:</label>
        <div class="col-lg-2">
            {OPTION=category}
            {categorySelect}
        </div>
    </div>
    <div class="form-group">
        <label for="project" class="col-lg-3 control-label">Project:</label>
        <div class="col-lg-2">
            {OPTION=project}
            {projectSelect}
        </div>
    </div>
    <div class="form-group">
        <label for="status" class="col-lg-3 control-label">Status:</label>
        <div class="col-lg-2">
            {OPTION=status}
            {statusSelect}
        </div>
    </div>
    <div class="form-group">
        <label for="description" class="col-lg-3 control-label">Description</label>
        <div class="col-lg-6">
            <textarea class="form-control" name="description" maxlength="2000" rows="10" cols="50" id="description">{description}</textarea>
        </div>
    </div>
    <div class="form-group">
        <label for="name" class="col-lg-3 control-label">branch</label>
        <div class="col-lg-3">
            <input type="text" class="form-check-input" name="branch" id="branch" value="{branch}">
        </div>
    </div>
    <button name="submit" value="submit" type="submit" class="btn btn-lg btn-primary center-block">Update</button>
    <input type="hidden" name="token" value="{TOKEN}">
</form>