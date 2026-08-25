@if($showPermissionModal)
    <div>
        <h2>Assign Permission and Role</h2>

        <p>Modal is working!</p>

        <button wire:click="$set('showPermissionModal', false)">
            Close
        </button>
    </div>
@endif