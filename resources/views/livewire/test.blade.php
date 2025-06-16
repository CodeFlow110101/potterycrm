<?php

use function Livewire\Volt\{state, mount};
use Illuminate\Support\Facades\Mail;



$submit = function ($deviceId) {
    dd($deviceId);
};

?>

<div>
    @dump()
</div>

@script
<script>
    FingerprintJS.load().then(fp => {
        fp.get().then(result => {
            $wire.submit(result.visitorId);
        });
    });
</script>
@endscript