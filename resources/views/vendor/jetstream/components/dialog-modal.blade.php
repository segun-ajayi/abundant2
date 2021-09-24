@props(['id' => null, 'maxWidth' => null])

<x-jet-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="modal-dialog modal-{{ $class }}" role="document">
        <div class="modal-content">
            <div class="modal-header">
                {{ $title }}
            </div>
            <div class="modal-body">
                {{ $content }}
            </div>
            <div class="modal-footer">
                {{ $footer }}
            </div>
        </div>
        <!-- /.modal-content-->
    </div>
</x-jet-modal>
