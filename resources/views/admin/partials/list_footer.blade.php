@if ($items->count() > 0)
<div class="box-footer">
    <div class="dataTables_info pull-left" style="margin-top: 30px;">{{trans('pagination.showing', ['from' => $items->firstItem(), 'to' => $items->lastItem(), 'total' => $items->total()])}}</div>

    <div class="dataTables_paginate paging_simple_numbers box-tools pull-right">
        {{ $items->links() }}
    </div>
</div>
@endif
