<div class="col-sm-12 col-md-12">
    <div class="card">
        <div class="card-header"><i class="fa fa-edit"></i> Create Member</div>
        <div class="card-body">
                <div class="form-group">
                    <label for="memberId">Member ID</label>
                    <input type="number" class="form-control" placeholder="Available IDs: {{ $av }}" wire:model="member_id">
                    <x-jet-input-error for="member_id" />
                </div>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" wire:model="name">
                    <x-jet-input-error for="name" />
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" class="form-control" wire:model="address">
                    <x-jet-input-error for="address" />
                </div>
                <div class="form-group">
                    <label for="sex">Gender</label>
                    <select class="form-control" wire:model="sex">
                        <option value="male" >Male</option>
                        <option value="female" >Female</option>
                    </select>
                    <x-jet-input-error for="sex" />
                </div>
                <div class="form-group">
                    <label for="marital">Marital Status</label>
                    <select class="form-control" wire:model="marital">
                        <option value="married" >Married</option>
                        <option value="single" >Single</option>
                    </select>
                    <x-jet-input-error for="marital" />
                </div>
                <div class="form-group">
                    <label for="phone">Phone #</label>
                    <input type="text" class="form-control" id="phone" wire:model="phone">
                    <x-jet-input-error for="phone" />
                </div>
                <div class="form-group">
                    <label for="phone2">Phone 2 #</label>
                    <input type="text" class="form-control" id="phone2" wire:model="phone2">
                    <x-jet-input-error for="phone2" />
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" wire:model="email">
                    <x-jet-input-error for="email" />
                </div>
                <div class="form-group">
                    <label for="profession">Profession</label>
                    <input type="text" class="form-control" id="profession" wire:model="profession">
                    <x-jet-input-error for="profession" />
                </div>
                <div class="form-group">
                    <label for="purpose">Purpose</label>
                    <input type="text" class="form-control" id="purpose" wire:model="purpose">
                    <x-jet-input-error for="purpose" />
                </div>
                <div class="form-group">
                    <label for="referrer">Referrer</label>
                    <select class="form-control" id="referrer" wire:model="referrer">
                        <option value="">Select referrer</option>
                        @foreach($members as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <x-jet-input-error for="referrer" />
                </div>
                <div class="form-group">
                    <label for="nok">Next of Kin</label>
                    <input type="text" class="form-control" id="nok" wire:model="nok">
                    <x-jet-input-error for="nok" />
                </div>
                <div class="form-group">
                    <label for="nok_address">Next of Kin Address</label>
                    <input type="text" class="form-control" id="nok_address" wire:model="nok_address">
                    <x-jet-input-error for="nok_address" />
                </div>
                <div class="form-group">
                    <label for="nok_phone">Next of Kin Phone</label>
                    <input type="text" class="form-control" wire:model="nok_phone">
                    <x-jet-input-error for="nok_phone" />
                </div>
                <div class="form-group">
                    <label for="nok_phone2">Next of Kin Phone 2</label>
                    <input type="text" class="form-control" wire:model="nok_phone2">
                    <x-jet-input-error for="nok_phone2" />
                </div>
                <div class="form-group">
                    <label for="picture">Picture</label>
                    <input type="file" class="form-control" wire:model="picture">
                    <x-jet-input-error for="picture" />
                </div>
                <button type="button" class="btn btn-primary btn-block" wire:click="create">Submit</button>
            </form>
        </div>
    </div>
</div>
