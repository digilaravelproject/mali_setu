@extends('admin.layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Homepage Heroes</h2>
        <a href="{{ route('heroes.create') }}" class="btn btn-success">+ Add Hero</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($heroes->count())
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Preview</th>
                        <th>Title</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($heroes as $idx => $hero)
                        <tr>
                            <td>{{ $heroes->firstItem() + $idx }}</td>
                            <td>
                                <img src="{{ asset('storage/'.$hero->image_path) }}" alt="Hero" style="height:60px">
                            </td>
                            <td>{{ $hero->title }}</td>
                            <td>{{ $hero->created_at->format('d M Y') }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-success" href="{{ route('heroes.show', $hero) }}"><i class="fas fa-eye"></i></a>
                                <a class="btn btn-sm btn-success" href="{{ route('heroes.edit', $hero) }}"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('heroes.destroy', $hero) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this hero?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-success" type="submit"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $heroes->links() }}
    @else
        <div class="alert alert-secondary">No heroes yet. Click “Add Hero” to create one.</div>
    @endif
</div>
@endsection
