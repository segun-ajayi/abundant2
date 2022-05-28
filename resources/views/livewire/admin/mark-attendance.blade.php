<div class="card">
    <div class="card-header">
        <i class="fa fa-edit"></i>
        Mark Attendance
    </div>
    <div class="card-body">
        <div class="card">
            <div class="card-body">
                <x-jet-label>Choose Year</x-jet-label>
                <select class="form-control" wire:model="year">
                    @for($i = \Carbon\Carbon::now()->format('Y'); $i > 2019; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
        <div id="scrollX">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    @foreach($attendance as $item)
                        <th>{{ $item->getMonth() }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($members as $item)
                    <tr>
                        <td>{{ $item->member_id }}</td>
                        <td>{{ $item->name }}</td>
                        @foreach($attendance as $x)
                            <td>
                                <label class="switch switch-label switch-success">
                                    <input class="switch-input mark" wire:click="markAttendance({{ $x->id }}, {{ $item->id }})" type="checkbox"
                                       @if($x->members->contains('id', $item->id))
                                           checked
                                       @endif
                                    >
                                    <span class="switch-slider" data-checked="P" data-unchecked="A"></span>
                                </label>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
