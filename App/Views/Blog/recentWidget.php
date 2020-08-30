<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">Recent Posts</h3>
    </div>
    <ul class="list-group">
        {LOOP}
            <li class="list-group-item">
                <a href="{BASE}blog/post/{ID}">{title}</a>
            </li>
        {/LOOP}
        {ALT}
            <li class="list-group-item">No Posts to show</li>
        {/ALT}
    </ul>
</div>