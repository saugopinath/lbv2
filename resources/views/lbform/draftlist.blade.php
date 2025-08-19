<x-layouts.app>
    <table>
        <tr>
            <th>Application Id</th>
            <th>Action</th>
        </tr>
        @foreach($lists as $list)
        <tr>
            <td>{{ $list->application_id }}</td>
            <td>
                <a href="{{route('draftedit', $list->application_id)}}">Edit</a>
            </td>
        </tr>
        @endforeach
    </table>
    <div>
        {{ $lists->links() }}
    </div>

</x-layouts.app>