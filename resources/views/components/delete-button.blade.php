@props(['action', 'confirm' => 'Are you sure you want to delete this item?', 'size' => 'sm', 'label' => null])

<form action="{{ $action }}" method="POST" class="d-inline"
      onsubmit="return confirm(@js($confirm));">
    @csrf
    @method('DELETE')
    <button type="submit" {{ $attributes->merge(['class' => "btn btn-outline-danger btn-{$size}"]) }} title="Delete">
        <i class="bi bi-trash"></i>{{ $label ? ' '.$label : '' }}
    </button>
</form>
