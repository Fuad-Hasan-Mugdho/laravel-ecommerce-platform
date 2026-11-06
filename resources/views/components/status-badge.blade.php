@props(['active'])
<span class="badge rounded-pill {{ $active ? 'text-bg-success' : 'text-bg-secondary' }}">
    {{ $active ? 'Active' : 'Inactive' }}
</span>
