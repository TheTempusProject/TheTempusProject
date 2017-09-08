<div class="row">
    <div class="col-sm-8 blog-main">
        <div class="blog-post">
            <h2 class="blog-post-title">{title}</h2>
            <p class="blog-post-meta">{DTC}{created}{/DTC} by <a href="{BASE}admin/users/view/{author}">{author_name}</a></p>
            {content}
        </div><!-- /.blog-post -->
    </div><!-- /.blog-main -->
</div><!-- /.row -->
<a href="{BASE}admin/blog/delete/{ID}" class="btn btn-sm btn-danger" role="button">delete</a>
<a href="{BASE}admin/blog/edit/{ID}" class="btn btn-sm btn-warning" role="button">edit</a>