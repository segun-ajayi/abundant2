<div>

    @php
        $att = $this->current[$getState()];
        $tog = $att->members->contains('id', $getRecord()->id);
        $member = $getRecord();
    @endphp
    <div class="px-4"
         x-data="{
            state: @js($tog),
            att: @js($att['id']),
            member: @js($member['id']),

        }"
         x-init="
            $watch('state', (value) => {
                $wire.mark(att, member, state);
            })
         "
    >
        <x-filament::input.checkbox
            x-model="state"
        />
    </div>
</div>
